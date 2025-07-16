<?php
require_once(APPROOT.'collectors/src/InTuneCollector.class.inc.php');

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
        $this->oModelLookup = new LookupTable($sOQL, array('brand_id_friendlyname', 'name'), $this->bCaseSensitiveLookups);
    }

    /**
     * @inheritdoc
     */
    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex): void
    {
        if (!$this->oModelLookup->Lookup($aLineData, array('brand_id', 'model_id'), 'model_id', $iLineIndex))
        {
            throw New IgnoredRowException('Unknown Model');
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

