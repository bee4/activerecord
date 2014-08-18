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

require_once __DIR__.'/../../samples/SampleJsonSerializableEntity.php';

use \BeeBot\Entity\Tests\Samples;

/**
 * Check the JsonSerializable entity behaviour
 * @package BeeBot\Entity\Tests\Behaviours
 */
class JsonSerializableEntityTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Samples\SampleSerializableEntity
	 */
	private $object;

	public function setUp() {
		$this->object = new Samples\SampleJsonSerializableEntity;
	}

	public function testBehaviour() {
		$this->assertTrue(Samples\SampleJsonSerializableEntity::isJsonSerializable());
		$this->object->truite = "truite";

		$this->assertJsonStringEqualsJsonString(
			'{"readable":"readable","truite":"truite","uid":"'.$this->object->getUID().'"}',
			json_encode($this->object)
		);
	}
}
