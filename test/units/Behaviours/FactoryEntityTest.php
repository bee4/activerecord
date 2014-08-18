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

require_once __DIR__.'/../../samples/SampleFactoryEntity.php';

use \BeeBot\Entity\Tests\Samples;

/**
 * Check the factory entity behaviour
 * @package BeeBot\Entity\Tests\Behaviours
 */
class FactoryEntityTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Samples\SampleFactoryEntity
	 */
	private $object;

	public function setUp() {
		$this->object = new Samples\SampleFactoryEntity;
	}

	public function testBehaviour() {
		$this->assertTrue(Samples\SampleFactoryEntity::isFactory());
	}

	public function testHydrate() {
		$o = Samples\SampleFactoryEntity::factory([
			'readable' => "saumon",
			'writable' => "truite",
			'editable' => "trout",
			'hidden' => "salmon"
		]);

		$this->assertEquals("trout", $o->getEditable());
		$this->assertEquals("saumon", $o->getReadable());
	}
}
