<?php

class iTopOSFamilyInTuneCollector extends JsonCollector
{
    private $aCollectedOSFamilies = [];

    /**
     * @inheritdoc
     */
    public function Fetch() {
        // Exclude duplicate entries, if any
        $aData = parent::Fetch();
        while ($aData !== false) {
            if (($aData['primary_key'] != "") && !in_array($aData, $this->aCollectedOSFamilies)) {
                $this->aCollectedOSFamilies[] = $aData;
                break;
            } else {
                $this->iIdx++;
                $aData = parent::Fetch();
            }
        }
        return $aData;
    }

}

