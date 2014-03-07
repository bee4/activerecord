<?php
/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2013
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Behaviours
 */
namespace BeeBot\Entity\Behaviours;

use BeeBot\Entity\Entity;

/**
 * BulkLoadingStrategy behaviour definition.
 * Allow to encapsulate a list of entity and choose to persist in the database not one by one,
 * but a huge number of each at once...
 * @package BeeBot\Entity\Behaviours
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 */
interface BulkLoadingStrategy {
	/**
	 * Define the number of items to be loaded at once
	 * @var Integer
	 */
	const LIMIT = 50000;

	/**
	 * Add an item to be loaded
	 * @param Entity $oItem
	 */
	public function add( Entity $oItem );

	/**
	 * Trigger a flush of all items manually.
	 * This method is called automatically when limit is reached
	 */
	public function flush();
}