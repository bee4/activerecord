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
 * Test on ActiveRecord property items
 * @package BeeBot\Entity\Tests
 */
class PropertyTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * List of loaded properties for the SampleEntity
	 * @var array
	 */
	protected $props;

	/**
	 * Built entity
	 * @var Samples\SampleEntity
	 */
	protected $entity;

	protected function setUp() {
		$this->entity = new Samples\SampleEntity();

		$tmp = (new \ReflectionClass("\BeeBot\Entity\ActiveRecord"))->getProperty("properties");
		$tmp->setAccessible(true);
		$this->props = $tmp->getValue($this->entity);
	}

	public function testProperty() {
		//Check readable state
		$this->assertTrue($this->props['writable']->isWritable());
		$this->assertFalse($this->props['writable']->isReadable());
		$this->assertFalse($this->props['readable']->isWritable());
		$this->assertTrue($this->props['readable']->isReadable());

		//Check value setter / getter
		$this->props['editable']->set('truite', $this->entity);
		$this->props['truite']->set('trout', $this->entity);
		$this->assertEquals('truite', $this->props['editable']->get($this->entity));
		$this->assertEquals('trout', $this->props['truite']->get($this->entity));
		$this->assertEquals('hidden', $this->props['hidden']->getName());
	}
}
