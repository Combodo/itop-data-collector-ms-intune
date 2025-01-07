<?php

namespace UnitTestFiles\Test;

use InTuneCollectionPlan;
use iTopBrandInTuneCollector;

require_once (__DIR__.'/AbstractCollectorTestCase.php');
require_once(APPROOT.'/core/parameters.class.inc.php');
require_once(APPROOT.'/core/utils.class.inc.php');
require_once(APPROOT.'/core/collector.class.inc.php');
require_once(APPROOT.'/core/orchestrator.class.inc.php');
require_once(APPROOT.'/core/jsoncollector.class.inc.php');
require_once(APPROOT.'/collectors/src/iTopBrandInTuneCollector.class.inc.php');
require_once(APPROOT.'/core/collectionplan.class.inc.php');
require_once(APPROOT.'/collectors/src/InTuneCollectionPlan.class.inc.php');

class iTopBrandInTuneCollectorTest extends AbstractCollectorTestCase
{
    private iTopBrandInTuneCollector $oiTopBrandInTuneCollector;

    public function setUp(): void
    {
        // Initialize collection plan
        $oCollectionPlan = new InTuneCollectionPlan();
        $oCollectionPlan->MockInit();

        parent::Setup();
        $this->oiTopBrandInTuneCollector = new iTopBrandInTuneCollector;
        $this->oiTopBrandInTuneCollector->Init();
        $this->oiTopBrandInTuneCollector->SetTestMode(true);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $sFile = $this->sDataPath."iTopBrandInTuneCollector-1.csv";
        if (file_exists($sFile)) {
            unlink($sFile);
        }
    }

    function testCollectFromForgedJson() {
        // Run collect
        $this->assertTrue($this->oiTopBrandInTuneCollector->Collect());

        // Compare output csv
        $sExpected_content = file_get_contents(APPROOT."/collectors/tests/php-unit-tests/data/expected_brand.csv");
        $this->assertEquals($sExpected_content, file_get_contents($this->sDataPath."iTopBrandInTuneCollector-1.csv"));
    }

}
