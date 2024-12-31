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

    function testCollectFromForgedJson() {
        // Copy forged json to data directory
        $sJsonFile = APPROOT."collectors/tests/php-unit-tests/InTuneManagedDevices.json";
        $bRes = copy($sJsonFile, $this->sDataPath.basename($sJsonFile));
        if (!$bRes) {
            throw new \Exception("Failed copying $sJsonFile to ".$this->sDataPath.basename($sJsonFile));
        }

        // Run collect
        $this->assertTrue($this->oiTopBrandInTuneCollector->Collect());

        // Compare output csv
        $sExpected_content = file_get_contents(APPROOT."/collectors/tests/php-unit-tests/expected_brand.csv");
        $this->assertEquals($sExpected_content, file_get_contents(APPROOT."/data/iTopBrandInTuneCollector-1.csv"));
    }

}
