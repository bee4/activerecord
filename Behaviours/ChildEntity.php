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

use BeeBot\Entity\Entity;

/**
 * ChildEntity behaviour definition.
 * Simply add parent property management which need to be a valid Entity object
 * @package BeeBot\Entity\Behaviours
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 */
trait ChildEntity {
	/**
	 * Parent UID
	 * @var String
	 */
	protected $parent;

	/**
	 * Parent property setter
	 * @param Entity $oParent Parent entity to be used
	 */
	public function setParent( Entity $oParent ) {
		$this->parent = $oParent->getUID();
	}

	/**
	 * Retrieve parent property
	 * @return Mixed depends on the parent implementation
	 */
	public function getParent() {
		return $this->parent;
	}
}