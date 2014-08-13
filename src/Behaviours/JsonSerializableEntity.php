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
 * Implements the \JsonSerializable interface methods with the default entity behaviour
 * This trait must be used only with ActiveRecord objects
 * @see \JsonSerializable
 * @package BeeBot\Entity\Behaviours
 */
trait JsonSerializableEntity
{
	/**
	 * Transform current object to JSON
	 * @return stdClass|Array
	 */
	public function jsonSerialize()
	{
		$tmp = new \stdClass();
		foreach( $this->getIterator() as $name => $value ) {
			if( is_null($value) ) {
				continue;
			}
			$tmp->{$name} = $value;
		}
		$tmp->uid = $this->getUID();

		return $tmp;
	}
}
