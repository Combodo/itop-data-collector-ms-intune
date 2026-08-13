<?php
require_once(APPROOT.'collectors/src/InTuneCollector.class.inc.php');
require_once(APPROOT.'collectors/src/LookuptableExtended.class.inc.php');

class iTopTabletInTuneCollector extends InTuneCollector
{
    private $oModelLookup;

    /**
     * @inheritdoc
     */
    protected function MustProcessBeforeSynchro(): bool
    {
        // We must reprocess the CSV data obtained from the inventory script
        // to lookup the Brand/Model and OSFamily/OSVersion in iTop
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function InitProcessBeforeSynchro(): void
    {
        $sOQL = 'SELECT Model AS m WHERE m.type = \'Tablet\'';
        $this->oModelLookup = new LookupTableExtended($sOQL, array('brand_id_friendlyname', 'name'), $this->bCaseSensitiveLookups, $this->bIgnoreMappingErrors);
        $sOQL = 'SELECT Model AS m ';
        $this->oModelLookup->SetAllDataInItop($sOQL, array('brand_id_friendlyname', 'name'));
    }

    /**
     * @inheritdoc
     */
    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex): void
    {
        $iRes = $this->oModelLookup->Lookup($aLineData, array('brand_id', 'model_id'), 'model_id', $iLineIndex);
        switch ($iRes)
        {
            case LookupTableExtended::LOOKUP_NOT_FIND:
                throw New IgnoredRowException('Unknown Model');
                break;

            case LookupTableExtended::LOOKUP_NOT_FIND_BUT_PRESENT:
                throw New IgnoredRowException('Model is not the right type (Tablet)');
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function Fetch(): array | false
    {
        $aData = parent::Fetch();
        if ($aData !== false) {
            if ($aData['contacts_list'] != '') {
                $aData['contacts_list'] = "contact_id->email:" . $aData['contacts_list'];
            }
        }

        return $aData;

    }
}

