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
 * DatedEntity behaviour definition.
 * Simply add date property management which need to be a valid DateTime object
 * @package BeeBot\Entity\Behaviours
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 */
interface DatedEntity {
	/**
	 * Date property setter
	 * @param \DateTime $date Date details to be used
	 */
	public function setDate( \DateTime $date );

	/**
	 * Retrieve date property
	 * @return \DateTime
	 */
	public function getDate();
}