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

require_once __DIR__.'/../../samples/SampleMultipleBehavioursEntity.php';

use \BeeBot\Entity\Tests\Samples;

/**
 * Description of AllBehavioursTest
 * @package BeeBot\Entity\Tests\Behaviours
 */
class MultipleBehavioursTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Samples\SampleMultipleBehavioursEntity
	 */
	private $object;

	public function setUp() {
		$this->object = new Samples\SampleMultipleBehavioursEntity;
	}

	public function testBehaviour() {
		$this->assertTrue(Samples\SampleMultipleBehavioursEntity::isChild());
		$this->assertTrue(Samples\SampleMultipleBehavioursEntity::isFactory());
		$this->assertTrue(Samples\SampleMultipleBehavioursEntity::isDated());
		$this->assertTrue(Samples\SampleMultipleBehavioursEntity::isJsonSerializable());
		$this->assertTrue(Samples\SampleMultipleBehavioursEntity::isSerializable());
	}
}
