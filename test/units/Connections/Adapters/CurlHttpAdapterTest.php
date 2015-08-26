<?php

/**
 * This file is part of the bee4/activerecord package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2015
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Tests\Behaviours
 */

namespace BeeBot\Entity\Tests\Connections\Adapters;

require_once 'AbstractHttpAdapterTest.php';

use BeeBot\Entity\Connections\Adapters\CurlHttpAdapter;

/**
 * Check curl Http adapter
 * @package BeeBot\Entity\Tests\Behaviours
 */
class CurlHttpAdapterTest extends AbstractHttpAdapterTest
{
	public function setUp() {
		$this->object = new CurlHttpAdapter;
	}
}
