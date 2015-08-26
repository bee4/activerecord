<?php

/**
 * This file is part of the bee4/activerecord package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2015
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Tests
 */

namespace BeeBot\Entity\Tests;

require_once __DIR__.'/../samples/SampleNestedEntity.php';

use \BeeBot\Entity\Tests\Samples;

/**
 * Description of NestedEntityTest
 * @package BeeBot\Entity\Tests
 */
class NestedEntityTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass("\BeeBot\Entity\NestedEntity", [], 'MockedNested');
    }

    public function testBehaviour()
    {
        $class = "MockedNested";
        $this->assertTrue($class::isFactory());
    }

    /**
     * Check that a NestedEntity can be serialized
     */
    public function testSerialize()
    {
        $entity = new Samples\SampleNestedEntity();
        $entity->truite = "truite";
        $entity->editable = "editable";

        $this->assertJsonStringEqualsJsonString('{"truite":"truite", "editable":"editable"}', json_encode($entity));
        $waked = unserialize(serialize($entity));
        $this->assertEquals("truite", $waked->truite);
        $this->assertEquals("editable", $waked->editable);
    }
}
