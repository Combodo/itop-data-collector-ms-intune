<?php
require_once(APPROOT.'collectors/msbase/src/MSCollectionPlan.class.inc.php');

class InTuneCollectionPlan extends MSCollectionPlan
{
    private bool $bInTuneDatamodelIsInstalled;

    /**
     * @inheritdoc
     */
    public function Init(): void
    {
        parent::Init();

        // Check if InTune Datamodel extension is installed
        Utils::Log(LOG_INFO, '---------- Check InTune Datamodel extension installation ----------');
        if ($this->bInTuneDatamodelIsInstalled = utils::CheckModuleInstallation('combodo-intune-datamodel')) {
            Utils::Log(LOG_INFO, 'Extension InTune Datamodel has been installed on iTop');
        } else {
            Utils::Log(LOG_INFO, 'Extension InTune Datamodel has not been installed on iTop');
        }
    }

    public function MockInit($bInTuneDatamodelIsInstalled = true): void
    {
        $this->bInTuneDatamodelIsInstalled = $bInTuneDatamodelIsInstalled;
    }

    /**
     * Check if Combodo InTune Datamodel is installed
     *
     * @return bool
     */
    public function IsCbdinTuneDMInstalled(): bool
    {
        return $this->bInTuneDatamodelIsInstalled;
    }

    /**
	 * @inheritdoc
	 */
	public function AddCollectorsToOrchestrator(): bool
	{
		Utils::Log(LOG_INFO, "---------- InTune Collectors to launched ----------");

		return parent::AddCollectorsToOrchestrator();
	}
}
