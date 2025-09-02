<?php

class InTuneCollector extends JsonCollector
{
	protected $oCollectionPlan;
    protected $bCaseSensitiveLookups;
    protected $bIgnoreMappingErrors;

	/**
	 * @inheritdoc
	 */
	public function Init(): void
	{
		parent::Init();

		$this->oCollectionPlan = InTuneCollectionPlan::GetPlan();
        $this->bCaseSensitiveLookups = (Utils::GetConfigurationValue('case_sensitive_lookups', 'yes') == 'yes');
        // Due to the way InTune objects are typed, don't overload logs with a warning message if no mapping is found
        // for the CI's model as the CI certainly belongs to another class that is collected as well.
        $this->bIgnoreMappingErrors = true;
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
