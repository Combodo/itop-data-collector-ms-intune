<?php

class iTopOSVersionInTuneCollector extends JsonCollector
{
    /**
     * @inheritdoc
     */
    public function Fetch(): array | false
    {
        $aData = parent::Fetch();
        if ($aData !== false) {
            // Set primary_key
            $aData['primary_key'] = $aData['osfamily_id'].'-'.$aData['name'];
        }

        return $aData;

    }

}

