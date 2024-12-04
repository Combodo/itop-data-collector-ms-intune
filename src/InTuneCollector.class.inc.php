<?php

class InTuneCollector extends JsonCollector
{
	protected $oCollectionPlan;

	/**
	 * @inheritdoc
	 */
	public function Init(): void
	{
		parent::Init();

		$this->oCollectionPlan = InTuneCollectionPlan::GetPlan();
	}

    /**
     * @inheritdoc
     */
    public function AttributeIsOptional($sAttCode)
    {
        if ($sAttCode == 'services_list') return true;
        if ($sAttCode == 'providercontracts_list') return true;

        if ($this->oCollectionPlan->IsCbdinTuneDMInstalled()) {
            if ($sAttCode == 'intuneid') return false;
        } else {
            if ($sAttCode == 'intuneid') return true;
        }
        return parent::AttributeIsOptional($sAttCode);
    }

}
