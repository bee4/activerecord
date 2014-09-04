<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2014
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Transactions
 */

namespace BeeBot\Entity\Transactions;
use BeeBot\Entity\Entity;

/**
 * Define general canvas for transactions objects
 * @package BeeBot\Entity\Transactions
 */
interface TransactionInterface extends \Countable, \Iterator
{
	/**
	 * Inject an Entity in the current transaction
	 * @param Entity $entity
	 */
	public function persist(Entity $entity);
}
