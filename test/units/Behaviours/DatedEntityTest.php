<?php

/**
 * This file is part of the bee4/activerecord package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2015
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Tests\Behaviours
 */

namespace BeeBot\Entity\Tests\Behaviours;

require_once __DIR__.'/../../samples/SampleDatedEntity.php';

use \BeeBot\Entity\Tests\Samples;

/**
 * Check the dated entity behaviour
 * @package BeeBot\Entity\Tests\Behaviours
 */
class DatedEntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Samples\SampleDatedEntity
     */
    private $object;

    public function setUp()
    {
        $this->object = new Samples\SampleDatedEntity;
    }

    public function testBehaviour()
    {
        $this->assertTrue(Samples\SampleDatedEntity::isDated());
        $this->assertNull($this->object->getDate());

        $date = new \DateTime;
        $this->object->setDate($date);
        $this->assertEquals($date, $this->object->getDate());
    }
}
