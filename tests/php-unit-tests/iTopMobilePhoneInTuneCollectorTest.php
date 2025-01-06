<?php

namespace UnitTestFiles\Test;

use InTuneCollectionPlan;
use iTopMobilePhoneInTuneCollector;

require_once (__DIR__.'/AbstractCollectorTestCase.php');
require_once(APPROOT.'/core/parameters.class.inc.php');
require_once(APPROOT.'/core/utils.class.inc.php');
require_once(APPROOT.'/core/collector.class.inc.php');
require_once(APPROOT.'/core/lookuptable.class.inc.php');
require_once(APPROOT.'/core/orchestrator.class.inc.php');
require_once(APPROOT.'/core/jsoncollector.class.inc.php');
require_once(APPROOT.'/collectors/src/iTopMobilePhoneInTuneCollector.class.inc.php');
require_once(APPROOT.'/core/collectionplan.class.inc.php');
require_once(APPROOT.'/collectors/src/InTuneCollectionPlan.class.inc.php');

class iTopMobilePhoneInTuneCollectorTest extends AbstractCollectorTestCase
{
    private iTopMobilePhoneInTuneCollector $oiTopMobilePhoneInTuneCollector;

    public function setUp(): void
    {
        // Initialize collection plan
        $oCollectionPlan = new InTuneCollectionPlan();
        $oCollectionPlan->MockInit();

        parent::Setup();
        $this->oiTopMobilePhoneInTuneCollector = new iTopMobilePhoneInTuneCollector;
        $this->oiTopMobilePhoneInTuneCollector->Init();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unlink($this->sDataPath."InTuneManagedDevices.json");
        unlink($this->sDataPath."iTopMobilePhoneInTuneCollector.raw-1.csv");
    }

    function testCollectFromForgedJson() {
        // Copy forged json to data directory
        $sJsonFile = APPROOT."collectors/tests/php-unit-tests/data/InTuneManagedDevices.json";
        $bRes = copy($sJsonFile, $this->sDataPath.basename($sJsonFile));
        if (!$bRes) {
            throw new \Exception("Failed copying $sJsonFile to ".$this->sDataPath.basename($sJsonFile));
        }

        // Run collect
        $this->assertTrue($this->oiTopMobilePhoneInTuneCollector->Collect());

        // Compare output csv
        $sExpected_content = file_get_contents(APPROOT."/collectors/tests/php-unit-tests/data/expected_mobilephone.raw.csv");
        $this->assertEquals($sExpected_content, file_get_contents(APPROOT."/data/iTopMobilePhoneInTuneCollector.raw-1.csv"));
    }

}
