<?php

namespace UnitTestFiles\Test;

use InTuneCollectionPlan;
use iTopOSVersionInTuneCollector;

require_once (__DIR__.'/AbstractCollectorTestCase.php');
require_once(APPROOT.'/core/parameters.class.inc.php');
require_once(APPROOT.'/core/utils.class.inc.php');
require_once(APPROOT.'/core/collector.class.inc.php');
require_once(APPROOT.'/core/orchestrator.class.inc.php');
require_once(APPROOT.'/core/jsoncollector.class.inc.php');
require_once(APPROOT.'/collectors/src/iTopOSVersionInTuneCollector.class.inc.php');
require_once(APPROOT.'/core/collectionplan.class.inc.php');
require_once(APPROOT.'/collectors/src/InTuneCollectionPlan.class.inc.php');

class iTopOSVersionInTuneCollectorTest extends AbstractCollectorTestCase
{
    private iTopOSVersionInTuneCollector $oiTopOSVersionInTuneCollector;

    public function setUp(): void
    {
        // Initialize collection plan
        $oCollectionPlan = new InTuneCollectionPlan();
        $oCollectionPlan->MockInit();

        parent::Setup();
        $this->oiTopOSVersionInTuneCollector = new iTopOSVersionInTuneCollector;
        $this->oiTopOSVersionInTuneCollector->Init();
    }

    function testCollectFromForgedJson() {
        // Copy forged json to data directory
        $sJsonFile = APPROOT."collectors/tests/php-unit-tests/InTuneManagedDevices.json";
        $bRes = copy($sJsonFile, $this->sDataPath.basename($sJsonFile));
        if (!$bRes) {
            throw new \Exception("Failed copying $sJsonFile to ".$this->sDataPath.basename($sJsonFile));
        }

        // Run collect
        $this->assertTrue($this->oiTopOSVersionInTuneCollector->Collect());

        // Compare output csv
        $sExpected_content = file_get_contents(APPROOT."/collectors/tests/php-unit-tests/expected_osversion.csv");
        $this->assertEquals($sExpected_content, file_get_contents(APPROOT."/data/iTopOSVersionInTuneCollector-1.csv"));
    }

}
