<?php
/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2013
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity
 */

namespace BeeBot\Entity;

use BeeBot\Entity\Behaviours\FactoryEntity;

/**
 * Nested Entity definition
 * Used to encapsulate data inside an entity as object
 * @package BeeBot\Entity
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 */
abstract class NestedEntity extends ActiveRecord
{
	use FactoryEntity;
}