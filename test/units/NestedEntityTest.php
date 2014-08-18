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
 * Description of NestedEntityTest
 * @package BeeBot\Entity\Tests
 */
class NestedEntityTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp() {
		$this->object = $this->getMockForAbstractClass("\BeeBot\Entity\NestedEntity", [], 'MockedNested');
	}

	public function testBehaviour() {
		$class = "MockedNested";
		$this->assertTrue($class::isFactory());
	}
}
