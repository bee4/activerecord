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
 * Define standard behaviours for database connection
 * @package BeeBot\Entity\Connections
 */
interface ConnectionInterface {
	/**
	 * Fetch some entities from a field value
	 * @param string $type Entity type to be manipulated
	 * @param string $term Term name to be processed
	 * @param mixed $value Searched term value
	 * @return \BeeBot\Entity\EntityCollection
	 */
	public function fetchBy($type, $term, $value);
	/**
	 * Count entity number matching criteria
	 * @param string $type Entity type to be manipulated
	 * @param string $term Term name to be processed
	 * @param mixed $value Searched term value
	 * @return integer
	 */
	public function countBy($type, $term, $value);
	
	/**
	 * Run a raw query against current connection
	 * @param string $type Entity type to be manipulated
	 * @param string $query
	 * @return \BeeBot\Entity\EntityCollection
	 */
	public function raw($type, $query);
	
	/**
	 * Save the given entity
	 * @param \BeeBot\Entity\Entity $entity
	 * @return boolean
	 */
	public function save(\BeeBot\Entity\Entity $entity);
	
	/**
	 * Delete the given entity
	 * @param \BeeBot\Entity\Entity $entity
	 * @return boolean
	 */
	public function delete(\BeeBot\Entity\Entity $entity);
	
	/**
	 * Define how the connection will flush a transaction
	 * @param \BeeBot\Entity\Transactions\TransactionInterface $transaction
	 */
	public function flush(\BeeBot\Entity\Transactions\TransactionInterface $transaction);
}
