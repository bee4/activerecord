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

use BeeBot\Entity\ActiveRecord;
use BeeBot\Entity\Connections\AbstractConnection;
use BeeBot\Entity\LazyLoaderDecorator;

require_once __DIR__.'/../samples/SampleMultipleBehavioursEntity.php';

/**
 * Description of LazyLoaderDecoratorTest
 * @package BeeBot\Entity\Tests
 */
class LazyLoaderDecoratorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Mocked connection
	 * @var \BeeBot\Entity\Connections\AbstractConnection
	 */
	private $connexion;

	protected function setUp() {
		//Build a valid mocked connection
		$this->connexion = $this->getMock(AbstractConnection::class);

		//Initiate the ActiveRecord connection instance
		ActiveRecord::setConnection($this->connexion);
	}

	public function testLoad() {
		$entityProperty = (new \ReflectionClass(LazyLoaderDecorator::class))->getProperty('entity');
		$entityProperty->setAccessible(true);

		$lazy = new \BeeBot\Entity\LazyLoaderDecorator(
			"BeeBot\Entity\Tests\Samples\SampleMultipleBehavioursEntity",
			"uid",
			"abcd"
		);
		$this->connexion
			->expects($this->any())
			->method("fetchBy")
			->with('samplemultiplebehavioursentity', 'uid', 'abcd')
			->willReturn([['truite'=>'truite','editable'=>'editable', 'uid'=>'abcd']]);

		$this->assertNull($entityProperty->getValue($lazy));
		$this->assertEquals('editable', $lazy->editable);
		$lazy->editable = "updated";
		$this->assertEquals('updated', $lazy->editable);
		$this->assertEquals('truite', $lazy->truite);
		$this->assertEquals('abcd', $lazy->getUID());
		$this->assertEquals(true, $lazy::isSerializable());
		$this->assertInstanceOf("BeeBot\Entity\Tests\Samples\SampleMultipleBehavioursEntity", $entityProperty->getValue($lazy));
	}
}
