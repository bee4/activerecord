<?php
/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2013
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Action
 */

namespace BeeBot\Entity;

/**
 * Simple entity definition
 * Base of all the extended entities
 * @package BeeBot\Action
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 */
abstract class Entity extends ActiveRecord
{
	//Define entity state 
	const STATE_NEW = 0;
	const STATE_PERSISTED = 1;
	const STATE_DELETED = 2;
	
	protected $state = self::STATE_NEW;
	
	/**
	 * Unique identifier for the current entity
	 * In all databases (Document base or relationals), an UID is defined for a document
	 * @var string
	 */
	protected $uid;

	/**
	 * Retrieve current UID
	 * @return string
	 */
	public function getUID() {
		if( !isset($this->uid) ) {
			$this->uid = uniqid();
		}
		return $this->uid;
	}
	
	/**
	 * Check if current entity is not already persisted
	 * @return boolean
	 */
	public function isNew() {
		return $this->state === self::STATE_NEW;
	}
	
	/**
	 * Check if current entity is persisted
	 * @return boolean
	 */
	public function isPersisted() {
		return $this->state === self::STATE_PERSISTED;
	}
	
	/**
	 * Check if current entity has been deleted
	 * @return boolean
	 */
	public function isDeleted() {
		return $this->state === self::STATE_DELETED;
	}
	
	/**
	 * Magic method to match specific calls and redirect to the right method
	 * @param string $name Method name
	 * @param string $arguments Argument collection
	 * @return mixed
	 * @throws \BadMethodCallException
	 */
	public static function __callStatic( $name, $arguments ) {
		//Match all fetchByXXX calls
		$matches = null;
		if( preg_match('/^(fetchBy|fetchOneBy|countBy)(.+)$/', $name, $matches) ) {
			if( !isset($arguments[0]) ) {
				throw new \InvalidArgumentException("The function must be call with at least 1 parameter: The searched value!!");
			}
			
			array_unshift($arguments, strtolower($matches[2]));
			return call_user_func_array(
				[	get_called_class(), $matches[1] ],
				$arguments
			);
		}

		//Parent also defined some static magic methods, call it and if there is noting, the BadMethodCallException is triggered
		return parent::__callStatic($name, $arguments);
	}

	/**
	 * Retrieve a Collection of Document object from a given term value
	 * @param string $term Term name the value will be searched in
	 * @param string $value The value of the term to be searched
	 * @param integer $count Number of results to get
	 * @param integer $from Document position where we start to retrieve results
	 * @param array $sort Request sort parameter [name=>order]
	 * @return EntityCollection
	 * @throws \RuntimeException
	 */
	public static function fetchBy($term, $value, $count = null, $from = null, array $sort = null) {
		//Retrieve results from connection
		$results = self::getConnection()->fetchBy(self::getType(), $term, $value);
		
		//Then prepare Entity collection construction
		$name = get_called_class();
		$collection = new EntityCollection;
		//Callback used when an Entity does not use Factory behaviour
		$fillEntity = function($value, $prop, &$entity) {
			$entity->{$prop} = $value;
		};
		//Crawl extracted data and build entities
		foreach( $results as $data ) {
			if( $name::isFactory() ) {
				$tmp = call_user_func([$name, 'factory'],$data);
			} else {
				$tmp = new $name;
				array_walk($data, $fillEntity, $tmp);
			}
			
			$tmp->state = self::STATE_PERSISTED;
			$collection->append($tmp);
		}

		return $collection;
	}

	/**
	 * Retrieve document count from a given term value
	 * @param string $term Term name the value will be searched in
	 * @param mixed $value The value of the term to be searched
	 * @return integer
	 * @throws \RuntimeException
	 */
	public static function countBy($term, $value) {
		//Retrieve number of results from connection
		return self::getConnection()->countBy(self::getType(), $term, $value);
	}

	/**
	 * Retrieve a Document object from a given term value
	 * Value must match a unique document
	 * @param string $term Term name the value will be searched in
	 * @param string $value The value of the term to be searched
	 * @return Entity|null
	 * @throws \LengthException
	 */
	final public static function fetchOneBy( $term, $value ) {
		$collection = call_user_func(array(get_called_class(),'fetchBy'), $term, $value);
		if( count($collection) > 1 ) {
			throw new \LengthException('More than one entities have been found by matching criteria: {term:"'.$term.'", value:"'.$value.'"}');
		}

		return count($collection)==1?$collection[0]:null;
	}

	/**
	 * Persist loaded entity inside container
	 * @return boolean
	 */
	final public function save() {
		if( self::getConnection()->save($this) === true ) {
			$this->state = self::STATE_PERSISTED;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Delete current entity from it's container
	 * @return boolean
	 */
	final public function delete() {
		if( self::getConnection()->delete($this) === true ) {
			$this->state = self::STATE_DELETED;
			return true;
		} else {
			return false;
		}
	}
}