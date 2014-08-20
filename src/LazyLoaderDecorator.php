<?php

/**
 * This file is part of the bee4/activerecord package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2014
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity
 */

namespace BeeBot\Entity;

/**
 * Perform a lazy loading on an entity by using the fetchOneBy method
 * @package BeeBot\Entity
 */
final class LazyLoaderDecorator
{
	/**
	 * The lazy loaded entity
	 * @var Entity
	 */
	private $entity;

	/**
	 * Entity type to be loaded
	 * @var string
	 */
	private $type;

	/**
	 * The property name used for generation
	 * @var string
	 */
	private $property;

	/**
	 * Property value
	 * @var mixed
	 */
	private $value;

	public function __construct($type, $property, $value) {
		if( !is_subclass_of($type, "\BeeBot\Entity\Entity") ) {
			throw new \RuntimeException("You can't lazy load an object which is not an \BeeBot\Entity\Entity");
		}

		$this->type = $type;
		$this->property = $property;
		$this->value = $value;
	}

	private function invoke() {
		if( !isset($this->entity) ) {
			$type = $this->type;
			$this->entity = $type::fetchOneBy($this->property, $this->value);
		}
	}

	public function __call($name, $arguments) {
		$this->invoke();

		return $this->entity->$name($arguments);
	}

	public static function __callStatic($name, $arguments) {
		/**
		$this->invoke();

		$type = $this->type;
		return $type::$name($arguments);
		 */
	}

	public function __get($name) {
		$this->invoke();

		return $this->entity->__get($name);
	}

	public function __set($name, $value) {
		$this->invoke();

		return $this->entity->__set($name, $value);
	}
}
