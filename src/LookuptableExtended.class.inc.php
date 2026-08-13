<?php
// Copyright (C) 2014-2018 Combodo SARL
//
//   This application is free software; you can redistribute it and/or modify	
//   it under the terms of the GNU Affero General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.
//
//   iTop is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU Affero General Public License for more details.
//
//   You should have received a copy of the GNU Affero General Public License
//   along with this application. If not, see <http://www.gnu.org/licenses/>

/**
 * Helper class to perform advanced data lookups in iTop by retrieving objects via the REST API
 */
class LookupTableExtended extends LookupTable
{
    protected $aAllDataInItop;

    const LOOKUP_FIND = 1;
    const LOOKUP_NOT_FIND = 0;
    // Value present in iTop but not in the filtered data
    // it allows to identify if models are present in iTop but not in the good type for current file or if model not exists in iTop
    const LOOKUP_NOT_FIND_BUT_PRESENT = 2;

    public function SetAllDataInItop($sOQL, $aKeyFields)
    {
        $this->aAllDataInItop = $this->GetDataFromiTop($sOQL,$aKeyFields);
    }
    public function GetDataFromiTop($sOQL,$aKeyFields)
    {
        $aData = array();
        if (!preg_match('/^SELECT ([^ ]+)/', $sOQL, $aMatches)) {
            throw new Exception("Invalid OQL query: '$sOQL'. Expecting a query starting with 'SELECT xxx'");
        }
        $sClass = $aMatches[1];
        if(static::$oRestClient != null) {
            $oRestClient = static::$oRestClient;
        } else {
            $oRestClient = new RestClient();
        }
        $aRestFields = $aKeyFields;
        if ($this->sReturnAttCode !== 'id') {
            // If the return attcode is not the ID of the object, add it to the list of the required fields
            $aRestFields[] = $this->sReturnAttCode;
        }
        $aRes = $oRestClient->Get($sClass, $sOQL, implode(',', $aRestFields));
        if ($aRes['code'] == 0) {
            foreach ((array)$aRes['objects'] as $sObjKey => $aObj) {
                $iObjKey = 0;
                $aMappingKeys = array();
                foreach ($aKeyFields as $sField) {
                    if (!array_key_exists($sField, $aObj['fields'])) {
                        Utils::Log(LOG_ERR, "field '$sField' does not exist in '".json_encode($aObj['fields'])."'");
                        $aMappingKeys[] = '';
                    } else {
                        $aMappingKeys[] = $aObj['fields'][$sField];
                    }
                }
                $sMappingKey = implode( '_', $aMappingKeys);
                if (!$this->bCaseSensitive) {
                    if (function_exists('mb_strtolower')) {
                        $sMappingKey = mb_strtolower($sMappingKey);
                    } else {
                        $sMappingKey = strtolower($sMappingKey);
                    }
                }
                if ($this->sReturnAttCode !== 'id') {
                    // If the return attcode is not the ID of the object, check that it exists
                    if (!array_key_exists($this->sReturnAttCode, $aObj['fields'])) {
                        Utils::Log(LOG_ERR, "field '{$this->sReturnAttCode}' does not exist in '".json_encode($aObj['fields'])."'");
                        $iObjKey = 0;
                    } else {
                        $iObjKey = $aObj['fields'][$this->sReturnAttCode];
                    }
                } else {
                    // The return value is the ID of the object
                    if (!array_key_exists('key', $aObj)) {
                        // Emulate the behavior for older versions of the REST API
                        if (preg_match('/::([0-9]+)$/', $sObjKey, $aMatches)) {
                            $iObjKey = (int)$aMatches[1];
                        }
                    } else {
                        $iObjKey = (int)$aObj['key'];
                    }
                }
                $aData[$sMappingKey] = $iObjKey;
            }
        } else {
            Utils::Log(LOG_ERR, "Unable to retrieve the $sClass objects (query = $sOQL). Message: ".$aRes['message']);
        }
        return $aData;
    }

    /**
     * Replaces the given field in the CSV data by the identifier of the object in iTop, based on a list of lookup fields
     *
     * @param hash $aLineData The data corresponding to the line of the CSV file being processed
     * @param array $aLookupFields The list of fields used for the mapping key
     * @param string $sDestField The name of field (i.e. column) to populate with the id of the iTop object
     * @param int $iLineIndex The index of the line (0 = first line of the CSV file)
     *
     * @return bool true if the mapping succeeded, false otherwise
     */
    public function Lookup(&$aLineData, $aLookupFields, $sDestField, $iLineIndex, $bSkipIfEmpty = false )
    {
        $bRet = self::LOOKUP_FIND;
        if ($iLineIndex == 0) {
            $this->InitLineMappings($aLineData, array_merge($aLookupFields, array($sDestField)));
        } else {
            $iPos = $this->aFieldsPos[$sDestField];
            //skip search if field is empty
            if ($bSkipIfEmpty && $iPos !== null && $aLineData[$iPos] === '' ) {
                return false;
            }
            $aLookupKey = array();
            foreach ($aLookupFields as $sField) {
                $iPos = $this->aFieldsPos[$sField];
                if ($iPos !== null) {
                    $aLookupKey[] = $aLineData[$iPos];
                } else {
                    $aLookupKey[] = ''; // missing column ??
                }
            }
            $sLookupKey = implode('_', $aLookupKey);
            if (!$this->bCaseSensitive) {
                if (function_exists('mb_strtolower')) {
                    $sLookupKey = mb_strtolower($sLookupKey);
                } else {
                    $sLookupKey = strtolower($sLookupKey);
                }
            }
            if (!array_key_exists($sLookupKey, $this->aData)) {
                if ($this->bIgnoreMappingErrors) {
                    // Mapping *errors* are expected, just report them in debug mode
                    Utils::Log(LOG_DEBUG, "No mapping found with key: '$sLookupKey', '$sDestField' will be set to zero.");
                } else {
                    if (array_key_exists($sLookupKey, $this->aAllDataInItop)) {
                        $bRet = self::LOOKUP_NOT_FIND_BUT_PRESENT;
                        Utils::Log(LOG_DEBUG, "No mapping found with key: '$sLookupKey', '$sDestField' will be set to zero. But key is present in iTop");
                    } else{
                        $bRet = self::LOOKUP_NOT_FIND;
                        Utils::Log(LOG_WARNING, "No mapping found with key: '$sLookupKey', '$sDestField' will be set to zero.");
                    }
                }
            } else {
                $iPos = $this->aFieldsPos[$sDestField];
                if ($iPos !== null) {
                    $aLineData[$iPos] = $this->aData[$sLookupKey];
                } else {
                    Utils::Log(LOG_WARNING, "'$sDestField' is not a valid column name in the CSV file. Mapping will be ignored.");
                }
            }
        }

        return $bRet;
    }
}
