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

use BeeBot\Entity\Entity;
use BeeBot\Entity\Transactions\TransactionInterface;

/**
 * Define standard behaviours for database connection
 * @package BeeBot\Entity\Connections
 */
interface ConnectionInterface
{
    /**
     * Fetch some entities from a field value
     * @param  string  $type Entity type to be manipulated
     * @param  string  $term Term name to be processed
     * @param  mixed   $value Searched term value
     * @param  integer $count Number of results to retrieve
     * @param  integer $from Position of the first result to retrieve
     * @param  array   $sort Sort definition as [field=>order]
     * @return array
     */
    public function fetchBy(
        $type,
        $term,
        $value,
        $count,
        $from,
        array $sort
    );

    /**
     * Count entity number matching criteria
     * @param  string $type Entity type to be manipulated
     * @param  string $term Term name to be processed
     * @param  mixed  $value Searched term value
     * @return integer
     */
    public function countBy($type, $term, $value);

    /**
     * Run a raw query against current connection
     * @param  string $type Entity type to be manipulated
     * @param  string $query
     * @return array
     */
    public function raw($type, $query);

    /**
     * Save the given entity
     * @param  Entity $entity
     * @return boolean
     */
    public function save(Entity $entity);

    /**
     * Delete the given entity
     * @param  Entity $entity
     * @return boolean
     */
    public function delete(Entity $entity);

    /**
     * Define how the connection will flush a transaction
     * @param  TransactionInterface $transaction
     * @return boolean
     */
    public function flush(TransactionInterface $transaction);
}
