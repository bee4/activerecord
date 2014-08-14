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

/**
 * Description of PdoConnection
 * @package BeeBot\Entity\Connections
 */
class PdoConnection extends AbstractConnection {
	/**
	 * PDO connection handler
	 * @var \PDO
	 */
	protected $client;

	/**
	 * Initiate a new PDO connection from given data source name
	 * @param string $dsn
	 */
	public function __construct( $dsn ) {
		$this->client = new \PDO($dsn);
	}

	public function __destruct() {
		$this->client = null;
	}

	public function countBy($type, $term, $value) {
		$st = $this->client->prepare("
			SELECT COUNT(*)
			FROM $type
			WHERE $term = :term");
		$st->execute(['term' => $value]);
		return $st->fetchColumn();
	}

	public function delete(\BeeBot\Entity\Entity $entity) {
		$st = $this->client->prepare("
			DELETE FROM {$entity->getType()}
			WHERE uid = :uid");
		return $st->execute([
			'uid' => $entity->getUID()
		]);
	}

	public function fetchBy($type, $term, $value) {
		$st = $this->client->prepare("
			SELECT *
			FROM $type
			WHERE $term = :term");
		$st->execute(['term' => $value]);
		return $st->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function flush(\BeeBot\Entity\Transactions\TransactionInterface $transaction) {
		$this->client->beginTransaction();
		foreach( $transaction as $entity ) {
			if( $entity->isDeleted() ) {
				$status = $this->delete($entity);
			} else {
				$status = $this->save($entity);
			}

			if( !$status ) {
				$this->client->rollBack();
				return false;
			}
		}
		
		$this->client->commit();
		return true;
	}

	public function raw($type, $query) {
		$st = $this->client->prepare("
			SELECT *
			FROM $type
			WHERE ".$query);
		$st->execute();
		return $st->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function save(\BeeBot\Entity\Entity $entity) {
		$props = $entity->getIterator()->getArrayCopy();
		$props['uid'] = $entity->getUID();

		if( $entity->isNew() ) {
			$st = $this->client->prepare("
				INSERT INTO {$entity->getType()} (".implode(', ', array_keys($props)).")
				VALUES (:".implode(', :', array_keys($props)).")
			");
		} else {
			$set = [];
			array_walk($props, function($value, $key) use (&$set) {
				$set[] = $key."=:".$key;
			});
			$st = $this->client->prepare("
				UPDATE {$entity->getType()}
				SET ".implode(', ', $set)."
				WHERE uid=:uid
			");
		}
		return $st->execute($props);
	}
}
