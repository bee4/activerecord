<?php
/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2013
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Action
 */

namespace BeeBot\Entity;

/**
 * Simple entity definition
 * Base of all the extended entities
 * @package BeeBot\Action
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 */
abstract class Entity extends ActiveRecordModel
{
	/**
	 * Unique identifier for the current entity
	 * In all databases (Document base or relationals), an UID is defined for a document
	 * @var string
	 */
	protected $uid;

	/**
	 * Entity constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->uid = uniqid();
	}

	/**
	 * Retrieve current UID
	 * @return string
	 */
	public function getUID() {
		return $this->uid;
	}

	/**
	 * Magic method to match specific calls and redirect to the right method
	 * @param String $sMethod Method name
	 * @param String $aArguments Argument collection
	 * @return mixed
	 * @throws \BadMethodCallException
	 */
	public static function __callStatic( $sMethod, $aArguments ) {
		$matches = null;

		if( !isset($aArguments[0]) ) {
			throw new \InvalidArgumentException("The function must be call with at least 1 parameter: The searched value!!");
		}

		//Match all fetchByXXX calls
		if( preg_match('/^(fetchBy|fetchOneBy|countBy)(.+)$/', $sMethod, $matches) ) {
			array_unshift($aArguments, strtolower($matches[2]));
			return call_user_func_array(
				array(
					get_called_class(),
					$matches[1]
				),
				$aArguments
			);
		}

		throw new \BadMethodCallException(get_called_class().'::'.$sMethod.' method does not exists!');
	}

	/**
	 * Retrieve a Collection of Document object from a given term value
	 * @param string $sTerm Term name the value will be searched in
	 * @param string $mValue The value of the term to be searched
	 * @param integer $iCount Number of results to get
	 * @param integer $iFrom Document position where we start to retrieve results
	 * @param array $aSort Request sort parameter [name=>order]
	 * @return EntityCollection
	 * @throws \RuntimeException
	 */
	public static function fetchBy($sTerm, $mValue, $iCount = null, $iFrom = null, array $aSort = null) {
		throw new \RuntimeException('This method must be implemented in the selected context');
	}

	/**
	 * Retrieve document count from a given term value
	 * @param String $sTerm Term name the value will be searched in
	 * @param String $mValue The value of the term to be searched
	 * @return Integer
	 * @throws \RuntimeException
	 */
	public static function countBy($sTerm, $mValue) {
		throw new \RuntimeException('This method must be implemented in the selected context');
	}

	/**
	 * Retrieve a Document object from a given term value
	 * Value must match a unique document
	 * @param String $sTerm Term name the value will be searched in
	 * @param String $mValue The value of the term to be searched
	 * @return Entity|null
	 * @throws \LengthException
	 */
	final public static function fetchOneBy( $sTerm, $mValue ) {
		$oCollection = call_user_func(array(get_called_class(),'fetchBy'), $sTerm, $mValue);
		if( count($oCollection) > 1 ) {
			throw new \LengthException('More than one entities have been found by matching criteria...');
		}

		return count($oCollection)==1?$oCollection[0]:null;
	}

	/**
	 * Persist loaded entity inside container
	 * @return Boolean
	 */
	abstract public function save();

	/**
	 * Delete current entity from it's container
	 * @return Boolean
	 */
	abstract public function delete();
}