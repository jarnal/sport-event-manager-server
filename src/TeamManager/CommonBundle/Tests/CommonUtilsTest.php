<?php
namespace TeamManager\CommonBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use TeamManager\CommonBundle\Utils\CommonUtils;

class CommonUtilsTest extends WebTestCase {

    /**
     * Test the result of the season list returned.
     */
    public function testSeasonListAction()
    {
        $seasons = CommonUtils::getSeasonList(2014, 2018);
        $this->assertTrue(array_pop($seasons)=='2018-2019');
    }

}
