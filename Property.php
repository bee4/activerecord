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

use \ReflectionProperty;

/**
 * Define an entity property with its specific rules
 * @package BeeBot\Entity
 */
class Property {
	/**
	 * @var string
	 */
	private $name;

	/**
	 * Setter function name that will be used to set this property
	 * @var string
	 */
	private $setter;

	/**
	 * Getter function name that will be used to retrieve property value
	 * @var string
	 */
	private $getter;

	/**
	 * @var array
	 */
	private $attributes;

	/**
	 * True if property is public
	 * @var boolean
	 */
	private $public = false;

	/**
	 * True if property is readable
	 * @var boolean
	 */
	private $readable = false;

	/**
	 * True if property is writable
	 * @var boolean
	 */
	private $writable = false;

	/**
	 * Define property
	 * @param ReflectionProperty $property
	 */
	public function __construct(ReflectionProperty $property) {
		$this->name = $property->getName();
		$this->public = $property->isPublic();
		$this->setter = 'set'.ucfirst($this->name);
		$this->getter = 'get'.ucfirst($this->name);

		$this->parseDocComment($property->getDocComment());

		//Get property access (check visibility and accessor
		$class = $property->getDeclaringClass();
		if( $property->isPublic() ) {
			$this->readable = true;
			$this->writable = true;
		} else {
			if( $class->hasMethod($this->getter) && $class->getMethod($this->getter)->isPublic()) {
				$this->readable = true;
			}
			if( $class->hasMethod($this->setter) && $class->getMethod($this->setter)->isPublic() ) {
				$this->writable = true;
			}
		}
	}

	/**
	 * @param string $comment
	 */
	private function parseDocComment($comment) {
		$this->attributes = [];
		if(strlen($comment) > 0) {
			$parser = new DocBlockParser();
			$this->attributes = $parser->parse($comment);
		}
	}

	/**
	 * Retrieve property name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return boolean
	 */
	public function isReadable() {
		return $this->readable;
	}

	/**
	 * @return boolean
	 */
	public function isWritable() {
		return $this->writable;
	}

	/**
	 * Set the property value on an AbstractRecordModel instance
	 * @param mixed $value
	 * @param \BeeBot\Entity\ActiveRecordModel $model
	 * @return \BeeBot\Entity\ActiveRecordModel
	 * @throws \InvalidArgumentException
	 */
	public function set($value, ActiveRecordModel $model) {
		if( !$this->writable ) {
			throw new \InvalidArgumentException("You can't set this property because it is not writable: ".$this->name);
		}

		if( $this->public ) {
			$model->{$this->name} = $value;
		} else {
			call_user_func([$model, $this->setter], $value);
		}
		return $model;
	}

	/**
	 * Get property value from an AbstractRecordModel instance
	 * @param \BeeBot\Entity\ActiveRecordModel $model
	 * @return mixed Depends on the property value
	 * @throws \InvalidArgumentException
	 */
	public function get(ActiveRecordModel $model) {
		if( !$this->readable ) {
			throw new \InvalidArgumentException("You can't get this property because it is not readable: ".$this->name);
		}

		if( $this->public ) {
			return $model->{$this->name};
		} else {
			return call_user_func([$model, $this->getter]);
		}
	}
}
