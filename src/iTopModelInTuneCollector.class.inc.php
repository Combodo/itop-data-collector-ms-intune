<?php

class iTopModelInTuneCollector extends JsonCollector
{
    /**
     * @inheritdoc
     */
    public function Fetch(): array | false
    {
        $aData = parent::Fetch();
        if ($aData !== false) {
            $aData['primary_key'] = $aData['brand_id'].'-'.$aData['name'];
            // Set type according to OSFamily / Model mapping
            if (array_key_exists('osfamily_type_default_mapping', $this->aCollectorConfig)) {
                $sType = $aData['type'];
                if (array_key_exists($sType, $this->aCollectorConfig['osfamily_type_default_mapping'])) {
                    $aData['type'] = $this->aCollectorConfig['osfamily_type_default_mapping'][$sType];
                }
            }
        }

        return $aData;

    }
}

