<?php
require_once(APPROOT.'collectors/msbase/src/MSCollectionPlan.class.inc.php');

class InTuneCollectionPlan extends MSCollectionPlan
{
	/**
	 * @inheritdoc
	 */
	public function AddCollectorsToOrchestrator(): bool
	{
		Utils::Log(LOG_INFO, "---------- InTune Collectors to launched ----------");

		return parent::AddCollectorsToOrchestrator();
	}
}
