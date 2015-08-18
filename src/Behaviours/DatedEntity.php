<?php
/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2013
 * @author  Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Behaviours
 */
namespace BeeBot\Entity\Behaviours;

/**
 * DatedEntity behaviour definition.
 * Simply add date property management which need to be a valid DateTime object
 * @package BeeBot\Entity\Behaviours
 * @author  Stephane HULARD <s.hulard@chstudio.fr>
 */
trait DatedEntity
{
    /**
     * Document creation date
     * @var \DateTime
     */
    protected $date;

        /**
         * Set creation date
         * @param \DateTime $date
         */
        public function setDate(\DateTime $date)
        {
        $this->date = $date;
        }

    /**
     * Get creation date
     * @return \DateTime
     */
        public function getDate()
        {
            return $this->date;
        }
}
