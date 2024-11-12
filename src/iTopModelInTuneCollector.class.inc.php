<?php

class iTopModelInTuneCollector extends JsonCollector
{
    private $aCollectedModels = [];

    /**
     * @inheritdoc
     */
    public function Fetch(): array | false
    {
        // Exclude duplicate entries, if any
        // Set type according to OSFamily / Model mapping defined in configuration file
        $aData = parent::Fetch();
        while ($aData !== false) {
            $sBrand = $aData['brand_id'];
            $aData['primary_key'] = $sBrand.'-'.$aData['name'];
            if (($sBrand != "") && !in_array($aData, $this->aCollectedModels)) {
                $this->aCollectedModels[] = $aData;
                $sModelType = 'unknown';
                if (array_key_exists('osfamily_type_default_mapping', $this->aCollectorConfig)) {
                    $aDefaultMapping = $this->aCollectorConfig['osfamily_type_default_mapping'];
                    $sOS = $aData['type'];
                    if (array_key_exists($sOS, $aDefaultMapping)) {
                        if (is_array($aDefaultMapping[$sOS])) {
                            foreach ($aDefaultMapping[$sOS] AS $index => $aBrand) {
                                if ($aBrand['name'] == $sBrand) {
                                    $sModelType = $aBrand['type'];
                                    break;
                                }
                            }
                        } else {
                            $sModelType = $aDefaultMapping[$sOS];
                        }
                    }
                }
                $aData['type'] = $sModelType;
                break;
            } else {
                $this->iIdx++;
                $aData = parent::Fetch();
            }
        }
        return $aData;

    }
}

