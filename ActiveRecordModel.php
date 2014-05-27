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

/**
 * Global canvas for all models
 * @package BeeBot\Entity
 */
abstract class ActiveRecordModel
{
	/**
	 * All already loaded properties
	 * @var array
	 */
	private static $PROPERTIES = [];
	/**
	 * Property collection used in the current entity
	 * @var array
	 */
	private $_properties;

	/**
	 * Read object properties by using ReflectionClass
	 */
	public function __construct() {
		if( !isset(self::$PROPERTIES[get_called_class()]) ) {
			$class = new \ReflectionClass($this);
			$tmp = [];
			foreach( $class->getProperties() as $property ) {
				$item = new Property($property);
				$tmp[$property->getName()] = $item;
			}
			self::$PROPERTIES[get_called_class()] = $tmp;
		}
		$this->_properties = self::$PROPERTIES[get_called_class()];
	}

	/**
	 * Add a new property to the current entity
	 * @param string $name property name
	 * @param mixed $value value to be set
	 */
	public function __set($name, $value) {
		if( !isset($this->_properties[$name]) ) {
			throw new \InvalidArgumentException(
				'Property name given does not exists in the current object: '.$name
			);
		}

		return $this->_properties[$name]->set($value, $this);
	}

	/**
	 * Return property value
	 * @param string $name Property name
	 * @return mixed
	 */
	public function __get($name) {
		if( !isset($this->_properties[$name]) ) {
			throw new \InvalidArgumentException(
				'Property name given does not exists or is not a writable one: '.$name
			);
		}
		return $this->_properties[$name]->get($this);
	}
}
