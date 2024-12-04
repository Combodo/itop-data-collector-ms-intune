<?php
require_once(APPROOT.'collectors/src/InTuneCollector.class.inc.php');

class iTopTabletInTuneCollector extends InTuneCollector
{
    private $oModelLookup;

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
        $this->oModelLookup = new LookupTable('SELECT Model AS m WHERE m.type = \'Tablet\'', array('brand_id_friendlyname', 'name'));
    }

    /**
     * @inheritdoc
     */
    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
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
            $aData['contacts_list'] = "contact_id->email:".$aData['contacts_list'];
        }

        return $aData;

    }
}

