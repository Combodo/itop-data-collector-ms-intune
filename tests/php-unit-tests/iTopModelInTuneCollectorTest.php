<?php

namespace UnitTestFiles\Test;

use InTuneCollectionPlan;
use iTopModelInTuneCollector;
use utils;

require_once (__DIR__.'/AbstractCollectorTestCase.php');
require_once(APPROOT.'/core/parameters.class.inc.php');
require_once(APPROOT.'/core/utils.class.inc.php');
require_once(APPROOT.'/core/collector.class.inc.php');
require_once(APPROOT.'/core/orchestrator.class.inc.php');
require_once(APPROOT.'/core/jsoncollector.class.inc.php');
require_once(APPROOT.'/collectors/src/iTopModelInTuneCollector.class.inc.php');
require_once(APPROOT.'/core/collectionplan.class.inc.php');
require_once(APPROOT.'/collectors/src/InTuneCollectionPlan.class.inc.php');

class iTopModelInTuneCollectorTest extends AbstractCollectorTestCase
{
    const WRONG_MODEL_UNKNOWN_TYPE = 'NoUnknownConst';
    private iTopModelInTuneCollector $oiTopModelInTuneCollector;
    private string $sUnknownType;

    public function setUp(): void
    {
        // Initialize collection plan
        $oCollectionPlan = new InTuneCollectionPlan();
        $oCollectionPlan->MockInit();

        parent::Setup();
        $this->oiTopModelInTuneCollector = new iTopModelInTuneCollector;
        $this->oiTopModelInTuneCollector->Init();

        $this->sUnknownType = Utils::GetConfigurationValue('model_unknown_type', self::WRONG_MODEL_UNKNOWN_TYPE);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $sFile= $this->sDataPath."iTopModelInTuneCollector-1.csv";
        if (file_exists($sFile)) {
            unlink($sFile);
        }
    }

    function testIsModelUnknownTypeSet() {
        $this->assertFalse($this->sUnknownType == self::WRONG_MODEL_UNKNOWN_TYPE);
    }

    function testGetTypeWithUnknownOS() {
        $sType = $this->InvokeNonPublicMethod('iTopModelInTuneCollector', 'GetType', $this->oiTopModelInTuneCollector, ['', 'MyOwnOS']);
        $this->assertTrue($sType == $this->sUnknownType);
    }

    function testGetTypeWithUnknownBrand() {
        $sType = $this->InvokeNonPublicMethod('iTopModelInTuneCollector', 'GetType', $this->oiTopModelInTuneCollector, ['MyOwnBrand', 'Windows']);
        $this->assertTrue($sType == $this->sUnknownType);
    }

    function testGetTypeWithKnownOS() {
        $sType = $this->InvokeNonPublicMethod('iTopModelInTuneCollector', 'GetType', $this->oiTopModelInTuneCollector, ['', 'Android']);
        $this->assertTrue($sType == 'MobilePhone');
    }

    function testGetTypeWithKnownBrand() {
        $sType = $this->InvokeNonPublicMethod('iTopModelInTuneCollector', 'GetType', $this->oiTopModelInTuneCollector, ['Dell Inc.', 'Windows']);
        $this->assertTrue($sType == 'PC');
    }

    function testCollectFromForgedJson() {
        // Run collect
        $this->assertTrue($this->oiTopModelInTuneCollector->Collect());

        // Compare output csv
        $sExpected_content = file_get_contents(__DIR__ ."/data/expected_model.csv");
        $this->assertEquals($sExpected_content, file_get_contents($this->sDataPath."iTopModelInTuneCollector-1.csv"));
    }

}
