<?php

/**
 * This file is part of the bee4/activerecord package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2014
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Tests\Behaviours
 */

namespace BeeBot\Entity\Tests\Behaviours;

require_once __DIR__.'/../../samples/SampleChildEntity.php';

use \BeeBot\Entity\Tests\Samples;

/**
 * Check the child entity behaviour
 * @package BeeBot\Entity\Tests\Behaviours
 */
class ChildEntityTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Samples\SampleChildEntity
	 */
	private $object;

	public function setUp() {
		$this->object = new Samples\SampleChildEntity;
	}

	public function testBehaviour() {
		$this->assertTrue(Samples\SampleChildEntity::isChild());
		$this->assertNull($this->object->getParent());

		$p = new Samples\SampleEntity();
		$this->object->setParent($p);
		$this->assertEquals($p, $this->object->getParent());
	}
}
