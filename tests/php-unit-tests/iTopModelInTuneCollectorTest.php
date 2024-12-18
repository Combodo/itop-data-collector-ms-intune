<?php

namespace UnitTestFiles\Test;

use iTopModelInTuneCollector;

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
    function testToto() {
        $oiTopModelInTuneCollector = new iTopModelInTuneCollector;
        $oiTopModelInTuneCollector->Init();
        $this->assertTrue(true);
    }

}
