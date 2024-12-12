<?php
require_once(APPROOT.'collectors/src/InTuneCollector.class.inc.php');

class iTopModelInTuneCollector extends InTuneCollector
{
    const DEFAULT_MODEL_UNKNOWN_TYPE = 'InTuneUnknown';
    private $aCollectedModels = [];
    private $sUnknownType;

    /**
     * @inheritdoc
     */
    public function Init(): void
    {
        parent::Init();

        $this->sUnknownType = Utils::GetConfigurationValue('model_unknown_type', self::DEFAULT_MODEL_UNKNOWN_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function CheckToLaunch(array $aOrchestratedCollectors): bool
    {
        // Check if InTune Datamodel extension is installed
        if (!$this->oCollectionPlan->IsCbdinTuneDMInstalled()) {
            Utils::Log(LOG_INFO, '> '.get_class($this).' will run in downgraded mode as InTune Datamodel extension is not installed on iTop !');
        }

        return true;
    }

    /**
     * Get the model type according to configuration parameters
     *
     * @param $sBrand
     * @param $sOS
     * @return string
     */
    private function GetType($sBrand, $sOS): string
    {
        $sModelType = $this->sUnknownType;
        if (array_key_exists('osfamily_type_default_mapping', $this->aCollectorConfig)) {
            $aDefaultMapping = $this->aCollectorConfig['osfamily_type_default_mapping'];
            if (array_key_exists($sOS, $aDefaultMapping)) {
                if (is_array($aDefaultMapping[$sOS])) {
                    foreach ($aDefaultMapping[$sOS] as $index => $aBrand) {
                        if (array_key_exists('name', $aBrand) && ($aBrand['name'] == $sBrand)) {
                            if (array_key_exists('type', $aBrand)) {
                                $sModelType = $aBrand['type'];
                            }
                            break;
                        }
                    }
                } else {
                    $sModelType = $aDefaultMapping[$sOS];
                }
            }
        }
        return $sModelType;
    }

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
                $sModelType = $this->GetType($sBrand, $aData['type']);
                if (($sModelType != $this->sUnknownType) || $this->oCollectionPlan->IsCbdinTuneDMInstalled()) {
                    $aData['type'] = $sModelType;
                    break;
                }
            }
            $this->iIdx++;
            $aData = parent::Fetch();
        }
        return $aData;

    }
}

