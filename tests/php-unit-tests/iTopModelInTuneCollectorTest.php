<?php

namespace UnitTestFiles\Test;

use iTopModelInTuneCollector;
use PHPUnit\Framework\TestCase;

@define('APPROOT', dirname(__FILE__, 4).'/');

require_once(APPROOT.'/core/parameters.class.inc.php');
require_once(APPROOT.'/core/utils.class.inc.php');
require_once(APPROOT.'/core/collector.class.inc.php');
require_once(APPROOT.'/core/orchestrator.class.inc.php');
require_once(APPROOT.'/core/jsoncollector.class.inc.php');
require_once(APPROOT.'/collectors/src/iTopModelInTuneCollector.class.inc.php');
require_once(APPROOT.'/core/collectionplan.class.inc.php');
require_once(APPROOT.'/collectors/src/InTuneCollectionPlan.class.inc.php');

class iTopModelInTuneCollectorTest extends TestCase
{
    function testToto() {
        $oiTopModelInTuneCollector = new iTopModelInTuneCollector;
        $oiTopModelInTuneCollector->Init();
        $this->assertTrue(true);
    }

}
