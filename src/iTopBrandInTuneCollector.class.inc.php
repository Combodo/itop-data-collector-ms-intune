<?php
require_once(APPROOT.'collectors/msbase/src/MSJsonCollector.class.inc.php');

class iTopBrandInTuneCollector extends MSJsonCollector
{
    private array $aCollectedBrands = [];

    /**
     * @inheritdoc
     */
    public function AttributeIsOptional($sAttCode): bool
    {
        if ($sAttCode == 'azuretags') return true;

        return parent::AttributeIsOptional($sAttCode);
    }

    /**
	 * @inheritdoc
	 */
	protected function BuildUrl($aParameters): string
	{
		$sUrl = $this->sResource.'/v'.$this->sApiVersion.'/deviceManagement/managedDevices';

		return $sUrl;
	}

    /**
     * @inheritdoc
     */
    public function Fetch() {
        // Exclude duplicate entries, if any
        $aData = parent::Fetch();
        while ($aData !== false) {
            if (($aData['primary_key'] != "") && !in_array($aData, $this->aCollectedBrands)) {
                $this->aCollectedBrands[] = $aData;
                break;
            } else {
                $this->iIdx++;
                $aData = parent::Fetch();
            }
        }
        return $aData;
    }

}



