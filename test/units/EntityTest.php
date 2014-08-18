<?php

/**
 * This file is part of the bee4/activerecord package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2014
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Tests
 */

namespace BeeBot\Entity\Tests;

/**
 * Test on Entity object
 * @package BeeBot\Entity\Tests
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Mocked object
	 * @var Entity
	 */
	private $object;

	/**
	 *
	 * @var \BeeBot\Entity\Connections\AbstractConnection
	 */
	private $connexion;

	protected function setUp() {
		//Build a valid mocked connection
		$this->connexion = $this->getMock("\BeeBot\Entity\Connections\AbstractConnection");

		//Initiate the ActiveRecord connection instance
		\BeeBot\Entity\ActiveRecord::setConnection($this->connexion);
		$this->object = $this->getMockForAbstractClass("\BeeBot\Entity\Entity", [], "MockedEntity");
	}

	/**
	 * Test that entity state reflect connexion state
	 */
	public function testBehaviour() {
		$this->connexion
			->method("save")
			->will($this->onConsecutiveCalls(true, false));
		$this->connexion
			->method("delete")
			->will($this->onConsecutiveCalls(true, false));

		$this->assertTrue($this->object->isNew());
		$this->assertTrue($this->object->save());
		$this->assertTrue($this->object->isPersisted());
		$this->assertTrue($this->object->delete());
		$this->assertTrue($this->object->isDeleted());
		$this->assertFalse($this->object->save());
		$this->assertFalse($this->object->delete());
	}

	/**
	 * Test that countBy method match the connexion query result
	 */
	public function testCountBy() {
		$this->connexion
			->method("countBy")
			->will($this->onConsecutiveCalls(1, 100));

		$o = "MockedEntity";
		$this->assertEquals(1, $o::countBy('uid', 'XXX'));
		$this->assertEquals(100, $o::countByUID('XXX'));
	}

	/**
	 * Test that fetchBy methods return valid results
	 * @expectedException \LengthException
	 */
	public function testFetchBy() {
		$this->connexion
			->method("fetchBy")
			->will($this->onConsecutiveCalls(
				[],
				[['uid'=>"XXX"], ['uid'=>"YYY"]],
				[['uid'=>"ZZZ"]],
				[],
				[['uid'=>"XXX"], ['uid'=>"YYY"]]
			));

		$o = "MockedEntity";
		$this->assertInstanceOf("\BeeBot\Entity\EntityCollection", $o::fetchBy('uid', 'XXX'));
		$collection = $o::fetchByUID('XXX');
		$this->assertCount(2, $collection);
		$this->assertEquals("XXX", $collection[0]->getUID());
		$this->assertEquals("YYY", $collection[1]->getUID());

		$item = $o::fetchOneBy('uid', 'XXX');
		$this->assertEquals("ZZZ", $item->getUID());
		$this->assertNull($o::fetchOneByUID('XXX'));

		//Multiple entities found with given criteria
		$o::fetchOneByUID('XXX');
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidMagicStatic() {
		$o = "MockedEntity";
		$o::fetchByUID();
	}
}
