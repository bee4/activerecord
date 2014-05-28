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
	 * Read object properties by using ReflectionClass
	 */
	public function __construct() {
		if( !isset(self::$CACHE[get_called_class()]) ) {
			$class = new ReflectionClass($this);
			$tmp = [];
			foreach( $class->getProperties(ReflectionProperty::IS_PUBLIC|ReflectionProperty::IS_PROTECTED|ReflectionProperty::IS_PRIVATE) as $property ) {
				$item = new Property($property);
				$tmp[$property->getName()] = $item;
			}
			self::$CACHE[get_called_class()] = $tmp;
		}
		$this->properties = self::$CACHE[get_called_class()];
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
}
