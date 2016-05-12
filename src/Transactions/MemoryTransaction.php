<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2015
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Transactions
 */

namespace BeeBot\Entity\Transactions;

use BeeBot\Entity\Entity;

/**
 * Entity transaction based on a memory storage
 * @package BeeBot\Entity\Transactions
 */
class MemoryTransaction implements TransactionInterface
{
    /**
     * Transactionable entities
     * @var array
     */
    protected $entities;

    /**
     * Current iterator index
     * @var integer
     */
    protected $index;

    /**
     * Initialize entity collection
     */
    public function __construct()
    {
        $this->entities = [];
    }

    public function count()
    {
        return count($this->entities);
    }

    public function current()
    {
        return $this->entities[$this->index];
    }

    public function key()
    {
        return $this->index;
    }

    public function next()
    {
        $this->index++;
    }

    public function rewind()
    {
        $this->index = 0;
    }

    public function valid()
    {
        return isset($this->entities[$this->index]);
    }

    /**
     * @param Entity $entity
     */
    public function persist(Entity $entity)
    {
        $this->entities[] = $entity;
    }

    public function remove(Entity $entity)
    {
        if( false === $pos = array_search($entity, $this->entities) ) {
            throw new \RuntimeException('Entity is not part of the current transaction.');
        }
        unset($this->entities[$pos]);
    }
}
