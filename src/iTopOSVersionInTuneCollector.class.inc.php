<?php

class iTopOSVersionInTuneCollector extends JsonCollector
{
    private $oOSVersionMapping;

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
        $this->oOSVersionMapping = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));
    }

    /**
     * @inheritdoc
     */
    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex): void
    {
        // We are just interested here to report the models that don't exist yet in iTop (whatever their type
        // Usage of type as 3rd attribute is to provide a column where to store the lookup that won't be used afterward
        if ($this->oOSVersionMapping->Lookup($aLineData, array('osfamily_id_id', 'name'), 'type', $iLineIndex)) {
            if ($iLineIndex != 0) {
                throw new IgnoredRowException('OS Version already reported in iTop');
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
            $aData['primary_key'] = $aData['osfamily_id'].' - '.$aData['name'];
        }

        return $aData;

    }
}

