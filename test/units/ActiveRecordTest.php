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

require_once __DIR__.'/../samples/SampleEntity.php';

/**
 * Tests on ActiveRecord object
 * @package BeeBot\Entity\Tests
 */
class ActiveRecordTest extends \PHPUnit_Framework_TestCase
{
	private $object;

	protected function setUp() {
		$this->object = $this->getMockForAbstractClass("\BeeBot\Entity\ActiveRecord", [], 'MockedRecord');
	}

	public function testBehaviour() {
		$this->assertInstanceOf("\ArrayIterator", $this->object->getIterator());
		$this->assertEquals('mockedrecord', call_user_func([$this->object, 'getType']));

		$object = new Samples\SampleEntity();
		$this->assertFalse(isset($object->editable));
		$object->editable = "truite";
		$this->assertTrue(isset($object->editable));
		unset($object->editable);
		$this->assertFalse(isset($object->editable));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidSet() {
		$this->object->property = "value";
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidGet() {
		echo $this->object->property;
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testInvalidConnection() {
		$this->object->getConnection();
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testInvalidStaticMethod() {
		$o = $this->object;
		$o::invalidMethod();
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testUnreadableGet() {
		$object = new Samples\SampleEntity();
		$object->writable;
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testUnwritableSet() {
		$object = new Samples\SampleEntity();
		$object->readable = "truite";
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidIs() {
		$object = new Samples\SampleEntity();
		$object::isChild("unwantedargument");
	}
}
