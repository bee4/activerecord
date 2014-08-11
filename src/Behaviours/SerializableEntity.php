<?php
/**
 * This file is part of the bee4/activerecord package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2014
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Behaviours
 */

namespace BeeBot\Entity\Behaviours;

/**
 * Implements the \Serializable interface methods with the default entity behaviour
 * This trait must be used only with ActiveRecord objects
 * @see \Serializable
 * @package BeeBot\Entity\Behaviours
 */
trait SerializableEntity
{
	/**
	 * Serialize an entity by retrieving all accessible properties in the instance
	 * @see \Serializable::serialize
	 * @return array
	 */
	public function serialize() {
		return serialize(get_object_vars($this));
	}

	/**
	 * Initialize entity instance from serialized data string
	 * @see \Serializable::unserialize
	 * @param string $serialized
	 */
	public function unserialize($serialized) {
		//Call ActiveRecord methods directly to initiate cache and fill object global properties
		self::preload();
		$this->init();

		//Populate all properties from their unserialized form
		//This behaviour can be overriden when necessary
		$data = unserialize($serialized);
		foreach( $data as $name => $value ) {
			$this->{$name} = $value;
		}
	}
}
