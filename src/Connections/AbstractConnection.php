<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2014
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Connections
 */

namespace BeeBot\Entity\Connections;

/**
 * Define a global canvas for connection adapters.
 * @package BeeBot\Entity\Connections
 */
abstract class AbstractConnection implements ConnectionInterface
{
	//Add Event dispatcher behaviour to allow events
	use \BeeBot\Event\EventDispatcherAwareTrait;
}
