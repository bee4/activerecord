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
}
