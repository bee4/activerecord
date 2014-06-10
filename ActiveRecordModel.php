<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2014
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity
 */

namespace BeeBot\Entity;

use ReflectionClass;
use ReflectionProperty;

/**
 * Global canvas for all models define property setter and getter logic
 * @package BeeBot\Entity
 * @method boolean isDated()
 * @method boolean isChild()
 * @method boolean isFactory()
 */
abstract class ActiveRecordModel
{
	/**
	 * Cache property for loaded properties
	 * When a new entity type is built, its property meta data are extracted
	 * @var array
	 */
	private static $CACHE = [];

	/**
	 * Property collection used in the current entity
	 * @var array
	 */
	private $properties;
	
	/**
	 * Behaviour collection for the current entity
	 * Behaviours are defined by traits and extend Entity capacities
	 * @var array
	 */
	private $behaviours;

	/**
	 * Read object properties with ReflectionClass
	 * Only public, protected and private are extracted, not static
	 */
	public function __construct() {
		$name = get_called_class();
		if( !isset(self::$CACHE[$name]) ) {
			self::generateCache($this);
		}
		$this->properties = self::$CACHE[$name]->properties;
		$this->behaviours = self::$CACHE[$name]->behaviours;
	}
	
	/**
	 * Generate the entity meta data cache for the given ActiveRecordModel
	 * @param \BeeBot\Entity\ActiveRecordModel $model
	 */
	protected static function generateCache(ActiveRecordModel $model) {
		$meta = new \stdClass();
		$class = new ReflectionClass($model);

		//Extract properties
		$meta->properties = [];
		foreach( $class->getProperties(ReflectionProperty::IS_PUBLIC|ReflectionProperty::IS_PROTECTED|ReflectionProperty::IS_PRIVATE) as $property ) {
			//Static are not considered as entity props because of their global scope
			if( $property->isStatic() ) { continue; }
			$item = new Property($property);
			$meta->properties[$property->getName()] = $item;
		}

		//Extract traits
		$meta->traits = $class->getTraitNames();
		while(($class = $class->getParentClass()) instanceof \ReflectionClass) {
			$meta->traits = array_merge($class->getTraitNames(),$meta->traits);
		}
		
		//Extract behaviours from traits
		$meta->behaviours = [];
		foreach( $meta->traits as $trait ) {
			$parts = [];
			if(preg_match('/Behaviours.([A-Za-z]*)Entity$/', $trait, $parts)===1) {
				$meta->behaviours[] = strtolower($parts[1]);
			}
		}
		self::$CACHE[get_called_class()] = $meta;
	}

	/**
	 * Add a new property to the current entity
	 * @param string $name property name
	 * @param mixed $value value to be set
	 */
	public function __set($name, $value) {
		if( !isset($this->properties[$name]) ) {
			throw new \InvalidArgumentException(
				'Property name given does not exists in the current object: '.$name
			);
		}

		return $this->properties[$name]->set($value, $this);
	}

	/**
	 * Return property value
	 * @param string $name Property name
	 * @return mixed
	 */
	public function __get($name) {
		if( !isset($this->properties[$name]) ) {
			throw new \InvalidArgumentException(
				'Property name given does not exists or is not a writable one: '.$name
			);
		}
		return $this->properties[$name]->get($this);
	}

	/**
	 * Check if the current property exists and is not null
	 * @param string $name
	 * @return boolean
	 */
	public function __isset($name) {
		if(!isset($this->properties[$name]) || $this->properties[$name]->get($this) === null) {
			return false;
		}
		return true;
	}

	/**
	 * Unset the given attribute value
	 * @param string $name
	 */
	public function __unset($name) {
		if( isset($this->properties[$name]) ) {
			$this->properties[$name]->set(null,$this);
		}
	}
	
	/**
	 * Generic wrapper to emulate behaviour detection: isDated, isChild, isFactory, ...
	 * @param string $name
	 * @param array $arguments
	 * @return boolean
	 * @throws \InvalidArgumentException
	 * @throws \BadMethodCallException
	 */
	public function __call($name, $arguments) {
		$parts = [];
		if(preg_match('/^is([A-Za-z]+)/', $name, $parts) === 1) {
			if( count($arguments) > 0) {
				throw new \InvalidArgumentException("This method does not required any argument: ".$name);
			}
			
			return $this->is(strtolower($parts[1]));
		}
		
		throw new \BadMethodCallException('Unknown method: '.get_called_class().'::'.$name);
	}
	
	/**
	 * Check if the current entity has a given behaviour
	 * @param string $behaviour
	 * @return boolean
	 */
	protected function is($behaviour) {
		return in_array($behaviour, $this->behaviours);
	}
}
