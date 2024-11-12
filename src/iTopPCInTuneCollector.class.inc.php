<?php

class iTopPCInTuneCollector extends JsonCollector
{
    private $oModelLookup;
    private $oOSVersionLookup;

    /**
     * @inheritdoc
     */
    protected function MustProcessBeforeSynchro()
    {
        // We must reprocess the CSV data obtained from the inventory script
        // to lookup the Brand/Model and OSFamily/OSVersion in iTop
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function InitProcessBeforeSynchro()
    {
        $this->oModelLookup = new LookupTable('SELECT Model AS m WHERE m.type = \'PC\'', array('brand_id_friendlyname', 'name'));
        $this->oOSVersionLookup = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));
    }

    /**
     * @inheritdoc
     */
    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        if (!$this->oModelLookup->Lookup($aLineData, array('brand_id', 'model_id'), 'model_id', $iLineIndex))
        {
            throw New IgnoredRowException('Unknown Model - Brand: '.$aLineData['brand_id'].' , Model: '.$aLineData['model_id']);
        }
        if (!$this->oOSVersionLookup->Lookup($aLineData, array('osfamily_id', 'osversion_id'), 'osversion_id', $iLineIndex))
        {
            throw New IgnoredRowException('Unknown OS Version - OS Family: '.$aLineData['osfamily_id'].' , OS Version: '.$aLineData['osversion_id']);
        }
    }

    /**
     * @inheritdoc
     */
    public function Fetch(): array | false
    {
        $aData = parent::Fetch();
        if ($aData !== false) {
            $aData['contacts_list'] = "contact_id->email:".$aData['contacts_list'];
        }

        return $aData;

    }
}

