<?php
require_once(APPROOT.'collectors/msbase/src/MSJsonCollector.class.inc.php');

class iTopBrandInTuneCollector extends MSJsonCollector
{
    /**
	 * @inheritdoc
	 */
	protected function BuildUrl($aParameters): string
	{
		$sUrl = $this->sResource.'/v'.$this->sApiVersion.'/deviceManagement/managedDevices';

		return $sUrl;
	}

}



