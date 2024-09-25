<?php

class iTopModelInTuneCollector extends JsonCollector
{
    private $oModelMapping;

    /**
     * @inheritdoc
     */
    protected function MustProcessBeforeSynchro()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function InitProcessBeforeSynchro(): void
    {
        // Create IPConfig mapping table
        $this->oModelMapping = new LookupTable('SELECT Model', array('brand_id_friendlyname', 'name'));
    }

    /**
     * @inheritdoc
     */
    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex): void
    {
        // We are just interested here to report the models that don't exist yet in iTop (whatever their type
        // Usage of type as 3rd attribute is to provide a column where to store the lookup that won't be used afterward
        if ($this->oModelMapping->Lookup($aLineData, array('brand_id', 'name'), 'type', $iLineIndex)) {
            if ($iLineIndex != 0) {
                throw new IgnoredRowException('Model already reported in iTop');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function Fetch(): array | false
    {
        $aData = parent::Fetch();
        if ($aData !== false) {
            // Then process each collected status
            $aData['primary_key'] = $aData['brand_id'].' - '.$aData['name'];
        }

        return $aData;

    }
}

