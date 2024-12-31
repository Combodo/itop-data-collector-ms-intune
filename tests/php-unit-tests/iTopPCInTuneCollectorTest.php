<?php

namespace UnitTestFiles\Test;

use InTuneCollectionPlan;
use iTopPCInTuneCollector;

require_once (__DIR__.'/AbstractCollectorTestCase.php');
require_once(APPROOT.'/core/parameters.class.inc.php');
require_once(APPROOT.'/core/utils.class.inc.php');
require_once(APPROOT.'/core/collector.class.inc.php');
require_once(APPROOT.'/core/lookuptable.class.inc.php');
require_once(APPROOT.'/core/orchestrator.class.inc.php');
require_once(APPROOT.'/core/jsoncollector.class.inc.php');
require_once(APPROOT.'/collectors/src/iTopPCInTuneCollector.class.inc.php');
require_once(APPROOT.'/core/collectionplan.class.inc.php');
require_once(APPROOT.'/collectors/src/InTuneCollectionPlan.class.inc.php');

class iTopPCInTuneCollectorTest extends AbstractCollectorTestCase
{
    private iTopPCInTuneCollector $oiTopPCInTuneCollector;

    public function setUp(): void
    {
        // Initialize collection plan
        $oCollectionPlan = new InTuneCollectionPlan();
        $oCollectionPlan->MockInit();

        parent::Setup();
        $this->oiTopPCInTuneCollector = new iTopPCInTuneCollector;
        $this->oiTopPCInTuneCollector->Init();
    }

    function testCollectFromForgedJson() {
        // Copy forged json to data directory
        $sJsonFile = APPROOT."collectors/tests/php-unit-tests/InTuneManagedDevices.json";
        $bRes = copy($sJsonFile, $this->sDataPath.basename($sJsonFile));
        if (!$bRes) {
            throw new \Exception("Failed copying $sJsonFile to ".$this->sDataPath.basename($sJsonFile));
        }

        // Run collect
        $this->assertTrue($this->oiTopPCInTuneCollector->Collect());

        // Compare output csv
        $sExpected_content = file_get_contents(APPROOT."/collectors/tests/php-unit-tests/expected_pc.raw.csv");
        $this->assertEquals($sExpected_content, file_get_contents(APPROOT."/data/iTopPCInTuneCollector.raw-1.csv"));
    }

}
