<?php

/**
 * This file is part of the bee4/activerecord package.
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
 * Description of PdoConnection
 * @package BeeBot\Entity\Connections
 */
class PdoConnection extends AbstractConnection
{
    /**
     * PDO connection handler
     * @var \PDO
     */
    protected $client;

    /**
     * Initiate a new PDO connection from given data source name
     * @param string $dsn
     */
    public function __construct($dsn)
    {
        $this->client = new \PDO($dsn);
    }

    /**
     * Close PDO connection when finished
     */
    public function __destruct()
    {
        $this->client = null;
    }

    /**
     * @param string $type
     * @param string $term
     * @param mixed $value
     * @return string
     */
    public function countBy($type, $term, $value)
    {
        $st = $this->prepare("
			SELECT COUNT(*)
			FROM $type
			WHERE $term = :term");
        $st->execute(['term' => $value]);
        return $st->fetchColumn();
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function delete(Entity $entity)
    {
        parent::delete($entity);

        $st = $this->prepare("
			DELETE FROM {$entity->getType()}
			WHERE uid = :uid");
        return $st->execute([
            'uid' => $entity->getUID()
        ]);
    }

    /**
     * @param string $type
     * @param string $term
     * @param mixed $value
     * @param integer $count
     * @param integer $from
     * @param array $sort
     * @return array
     * TODO: Handle sort
     */
    public function fetchBy(
        $type,
        $term,
        $value,
        $count = null,
        $from = null,
        array $sort = null
    ) {
        $query = <<<SQL
            SELECT *
            FROM $type
            WHERE $term = :term
SQL;
        if (isset($count)) {
            $query .= "\nLIMIT :count";
        }
        if (isset($from)) {
            $query .= "\nOFFSET :from";
        }

        $st = $this->prepare($query);
        $st->execute([
            'term' => $value,
            'count' => $count,
            'from' => $from
        ]);
        return $st->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param TransactionInterface $transaction
     * @return bool
     */
    public function flush(TransactionInterface $transaction)
    {
        $this->client->beginTransaction();
        foreach ($transaction as $entity) {
            if ($entity->isDeleted()) {
                $status = $this->delete($entity);
            } else {
                $status = $this->save($entity);
            }

            if (!$status) {
                $this->client->rollBack();
                return false;
            }
        }

        $this->client->commit();
        return true;
    }

    /**
     * @param string $type
     * @param string $query
     * @return array
     */
    public function raw($type, $query)
    {
        $st = $this->prepare("
			SELECT *
			FROM $type
			WHERE ".$query);
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function save(Entity $entity)
    {
        parent::save($entity);

        $props = $entity->getIterator()->getArrayCopy();
        $props['uid'] = $entity->getUID();

        if ($entity->isNew()) {
            $st = $this->prepare("
				INSERT INTO {$entity->getType()} (".implode(', ', array_keys($props)).")
				VALUES (:".implode(', :', array_keys($props)).")
			");
        } else {
            $set = [];
            array_walk($props, function ($value, $key) use (&$set) {
                $set[] = $key."=:".$key;
            });
            $st = $this->prepare("
				UPDATE {$entity->getType()}
				SET ".implode(', ', $set)."
				WHERE uid=:uid
			");
        }

        return $st->execute($props);
    }

    private function prepare($query)
    {
        $st = $this->client->prepare($query);
        if ($st === false) {
            $details = $this->client->errorInfo();
            throw new \RuntimeException(sprintf(
                'Error during statement creation: %s',
                $details[2]
            ));
        }

        return $st;
    }
}
