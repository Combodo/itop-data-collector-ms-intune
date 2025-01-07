<?php

namespace UnitTestFiles\Test;

use InTuneCollectionPlan;
use iTopOSFamilyInTuneCollector;

require_once (__DIR__.'/AbstractCollectorTestCase.php');
require_once(APPROOT.'/core/parameters.class.inc.php');
require_once(APPROOT.'/core/utils.class.inc.php');
require_once(APPROOT.'/core/collector.class.inc.php');
require_once(APPROOT.'/core/orchestrator.class.inc.php');
require_once(APPROOT.'/core/jsoncollector.class.inc.php');
require_once(APPROOT.'/collectors/src/iTopOSFamilyInTuneCollector.class.inc.php');
require_once(APPROOT.'/core/collectionplan.class.inc.php');
require_once(APPROOT.'/collectors/src/InTuneCollectionPlan.class.inc.php');

class iTopOSFamilyInTuneCollectorTest extends AbstractCollectorTestCase
{
    private iTopOSFamilyInTuneCollector $oiTopOSFamilyInTuneCollector;

    public function setUp(): void
    {
        // Initialize collection plan
        $oCollectionPlan = new InTuneCollectionPlan();
        $oCollectionPlan->MockInit();

        parent::Setup();
        $this->oiTopOSFamilyInTuneCollector = new iTopOSFamilyInTuneCollector;
        $this->oiTopOSFamilyInTuneCollector->Init();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $sFile = $this->sDataPath."iTopOSFamilyInTuneCollector-1.csv";
        if (file_exists($sFile)) {
            unlink($sFile);
        }
    }

    function testCollectFromForgedJson() {
        // Run collect
        $this->assertTrue($this->oiTopOSFamilyInTuneCollector->Collect());

        // Compare output csv
        $sExpected_content = file_get_contents(APPROOT."/collectors/tests/php-unit-tests/data/expected_osfamily.csv");
        $this->assertEquals($sExpected_content, file_get_contents($this->sDataPath."iTopOSFamilyInTuneCollector-1.csv"));
    }

}
