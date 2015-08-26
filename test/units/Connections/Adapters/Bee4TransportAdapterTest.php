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

use BeeBot\Entity\Connections\Adapters\Bee4TransportAdapter;
use Bee4\Transport\Client;

/**
 * Check Bee4 transport http adapter
 * @package BeeBot\Entity\Tests\Behaviours
 */
class Bee4TransportAdapterTest extends AbstractHttpAdapterTest
{
	public function setUp() {
		$this->object = new Bee4TransportAdapter(new Client);
	}
}
