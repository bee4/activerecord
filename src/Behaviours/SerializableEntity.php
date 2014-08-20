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

use BeeBot\Entity\Entity;

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
	 * @return string
	 */
	public function serialize() {
		$vars = get_object_vars($this);

		//Manage private properties
		//Backup UID
		if( $this instanceof Entity ) {
			$vars['_uid'] = $this->getUID();

			//Retrieve valid state and backup value
			if( $this->isNew() ) {
				$vars['_state'] = Entity::STATE_NEW;
			} elseif( $this->isPersisted() ) {
				$vars['_state'] = Entity::STATE_PERSISTED;
			} elseif( $this->isDeleted() ) {
				$vars['_state'] = Entity::STATE_DELETED;
			} else {
				throw new \UnexpectedValueException('Entity state must be a valid one: STATE_NEW, STATE_PERSISTED, STATE_DELETED');
			}
		}

		return serialize($vars);
	}

	/**
	 * Initialize entity instance from serialized data string
	 * @see \Serializable::unserialize
	 * @param string $serialized
	 */
	public function unserialize($serialized) {
		//Call ActiveRecord methods directly to initiate cache and fill object global properties
		self::preload();

		//Initialized the new instance with its default and load class meta
		$data = unserialize($serialized);
		if( $this instanceof Entity ) {
			$this->init($data['_uid'], $data['_state']);
			unset($data['_uid'], $data['_state']);
		} else {
			$this->init();
		}

		//Populate all properties from their unserialized form
		foreach( $data as $name => $value ) {
			$this->{$name} = $value;
		}
	}
}
