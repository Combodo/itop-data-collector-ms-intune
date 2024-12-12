<?php

class iTopOSVersionInTuneCollector extends JsonCollector
{
    private array $aCollectedOSVersions = [];

    /**
     * @inheritdoc
     */
    public function Fetch(): array | false
    {
        // Exclude duplicate entries, if any
        // Set type according to OSFamily / Model mapping defined in configuration file
        $aData = parent::Fetch();
        while ($aData !== false) {
            $aData['primary_key'] = $aData['osfamily_id'].'-'.$aData['name'];
            if (($aData['osfamily_id'] != "") && !in_array($aData, $this->aCollectedOSVersions)) {
                $this->aCollectedOSVersions[] = $aData;
                break;
            } else {
                $this->iIdx++;
                $aData = parent::Fetch();
            }
        }
        return $aData;
    }

}

