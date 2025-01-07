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

        $sFile = $this->sDataPath."iTopMobilePhoneInTuneCollector.raw-1.csv";
        if (file_exists($sFile)) {
            unlink($sFile);
        }
    }

    function testCollectFromForgedJson() {
        // Run collect
        $this->assertTrue($this->oiTopMobilePhoneInTuneCollector->Collect());

        // Compare output csv
        $sExpected_content = file_get_contents(APPROOT."/collectors/tests/php-unit-tests/data/expected_mobilephone.raw.csv");
        $this->assertEquals($sExpected_content, file_get_contents($this->sDataPath."iTopMobilePhoneInTuneCollector.raw-1.csv"));
    }

}
