<?php

namespace UnitTestFiles\Test;

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
    const string WRONG_MODEL_UNKNOWN_TYPE = 'NoUnknownConst';
    private iTopModelInTuneCollector $oiTopModelInTuneCollector;
    private string $sUnknownType;

    public function setUp(): void
    {
        parent::Setup();

        $this->oiTopModelInTuneCollector = new iTopModelInTuneCollector;
        $this->oiTopModelInTuneCollector->Init();

        $this->sUnknownType = Utils::GetConfigurationValue('model_unknown_type', self::WRONG_MODEL_UNKNOWN_TYPE);
    }

    function testIsModelUnknownTypeSet() {
        $this->assertFalse($this->sUnknownType == self::WRONG_MODEL_UNKNOWN_TYPE);
    }

    function testGetTypeWithUnknownOS() {
        $sType = $this->InvokeNonPublicMethod('iTopModelInTuneCollector', 'GetType', $this->oiTopModelInTuneCollector, ['MyOwnOS', '']);
        $this->assertTrue($sType == $this->sUnknownType);
    }

}
