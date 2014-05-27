<?php
/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2013
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Behaviours
 */

namespace BeeBot\Entity\Behaviours;

/**
 * Entity definition
 * Define methods to build entity from entered data.
 * Data are transformed by a hydrate method after object build
 * @package BeeBot\Entity\Behaviours
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 */
trait FactoryEntity {
	/**
	 * Factory function to build an entity objects from passed data
	 * Data can be anything than the Entity can understand in its "hydrate" method
	 * @param Mixed $mData Something than can be used to populate needed object
	 */
	public static function factory($mData) {
		$sClass = get_called_class();
		$oResult = new $sClass;
		$oResult->hydrate($mData);
		return $oResult;
	}

	/**
	 * Update object details from given Data
	 * @param Mixed $mData The data to use to wake up
	 */
	abstract protected function hydrate( $mData );
}