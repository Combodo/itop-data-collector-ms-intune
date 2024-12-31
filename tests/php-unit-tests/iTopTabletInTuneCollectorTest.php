<?php

namespace UnitTestFiles\Test;

use InTuneCollectionPlan;
use iTopTabletInTuneCollector;

require_once (__DIR__.'/AbstractCollectorTestCase.php');
require_once(APPROOT.'/core/parameters.class.inc.php');
require_once(APPROOT.'/core/utils.class.inc.php');
require_once(APPROOT.'/core/collector.class.inc.php');
require_once(APPROOT.'/core/lookuptable.class.inc.php');
require_once(APPROOT.'/core/orchestrator.class.inc.php');
require_once(APPROOT.'/core/jsoncollector.class.inc.php');
require_once(APPROOT.'/collectors/src/iTopTabletInTuneCollector.class.inc.php');
require_once(APPROOT.'/core/collectionplan.class.inc.php');
require_once(APPROOT.'/collectors/src/InTuneCollectionPlan.class.inc.php');

class iTopTabletInTuneCollectorTest extends AbstractCollectorTestCase
{
    private iTopTabletInTuneCollector $oiTopTabletInTuneCollector;

    public function setUp(): void
    {
        // Initialize collection plan
        $oCollectionPlan = new InTuneCollectionPlan();
        $oCollectionPlan->MockInit();

        parent::Setup();
        $this->oiTopTabletInTuneCollector = new iTopTabletInTuneCollector;
        $this->oiTopTabletInTuneCollector->Init();
    }

    function testCollectFromForgedJson() {
        // Copy forged json to data directory
        $sJsonFile = APPROOT."collectors/tests/php-unit-tests/InTuneManagedDevices.json";
        $bRes = copy($sJsonFile, $this->sDataPath.basename($sJsonFile));
        if (!$bRes) {
            throw new \Exception("Failed copying $sJsonFile to ".$this->sDataPath.basename($sJsonFile));
        }

        // Run collect
        $this->assertTrue($this->oiTopTabletInTuneCollector->Collect());

        // Compare output csv
        $sExpected_content = file_get_contents(APPROOT."/collectors/tests/php-unit-tests/expected_tablet.raw.csv");
        $this->assertEquals($sExpected_content, file_get_contents(APPROOT."/data/iTopTabletInTuneCollector.raw-1.csv"));
    }

}
