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
 * Description of BaseTransaction
 * @package BeeBot\Entity\Transactions
 */
class FileTransaction implements TransactionInterface, CollectionableTransactionInterface
{
    /**
     * Transactionable entities
     * @var resource
     */
    protected $stream;

    /**
     * Number of entities
     * @var integer
     */
    protected $nb;

    /**
     * Number of bytes read in the stream
     * @var integer
     */
    protected $pos;

    /**
     * The current line
     * @var string
     */
    protected $current;

    /**
     * Initialize entity collection
     */
    public function __construct()
    {
        $this->nb = $this->pos = 0;
        if (false === $this->stream = tmpfile()) {
            throw new \RuntimeException("Can't create tmp stream !!");
        }
    }

    /**
     * Destroy the stream
     */
    public function __destruct()
    {
        fclose($this->stream);
    }

    /**
     * Retrieve number of persisted items
     * @return int
     */
    public function count()
    {
        return $this->nb;
    }

    /**
     * Retrieve the current entity
     * @return Entity
     */
    public function current()
    {
        if ($this->current == "") {
            $this->rewind();
        }
        //If the unserialize fail, try to get the next line !
        try {
            return unserialize($this->current);
        } catch (\Exception $error) {
            if (feof($this->stream)) {
                throw new \Exception(
                    'Current item is not a valid serialized Entity: '.
                    PHP_EOL.$this->current
                );
            }
            $this->current .= fgets($this->stream);
            return $this->current();
        }
    }

    public function key()
    {
        return $this->pos;
    }

    public function next()
    {
        $this->pos += strlen($this->current);
        $this->current = fgets($this->stream);
    }

    public function rewind()
    {
        $this->pos = 0;
        fseek($this->stream, $this->pos);
        $this->current = fgets($this->stream);
    }

    public function valid()
    {
        return !feof($this->stream);
    }

    public function persist(Entity $entity)
    {
        if (!$entity::isSerializable()) {
            throw new \InvalidArgumentException(
                'Entity must be Serializable when using FileTransaction'
            );
        }

        $this->nb++;
        $s = serialize($entity).PHP_EOL;
        fseek($this->stream, 0, SEEK_END);
        fwrite($this->stream, $s);
        rewind($this->stream);
    }
}
