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
trait FactoryEntity
{
	/**
	 * Factory function to build an entity objects from passed data
	 * Data can be anything than the Entity can understand in its "hydrate" method
	 * @param mixed $data Something than can be used to populate needed object
	 * @return \BeeBot\Entity\Entity|\BeeBot\Entity\NestedEntity
	 */
	public static function factory($data) {
		$class = get_called_class();
		$result = new $class;
		$result->hydrate($data);

		return $result;
	}

	/**
	 * Update object details from given Data
	 * @param mixed $data The data to use to wake up
	 * @idea Maybe use Traversable as data type is a good idea ...
	 */
	abstract protected function hydrate( $data );
}
