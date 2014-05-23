<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2013
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\ElasticSearch
 */

namespace BeeBot\Entity\ElasticSearch;

/**
 * Define a visit extracted from log
 * @package BeeBot\Entity\ElasticSearch
 */
class Visit extends AbstractDocument {
	/**
	 * Visit origin
	 * @var String
	 */
	private $origin = 'anywhere';

	/**
	 * URL entity constructor
	 */
	public function __construct() {
		//Initialize elasticsearch details
		parent::__construct();

		$this->data->ip = null;
		$this->data->useragent = null;
		$this->data->referer = null;
		$this->data->method = null;
		$this->data->status = null;
		$this->data->size = null;
		$this->data->version = null;
	}

	/**
	 * Set visit IP address
	 * @param String $ip
	 */
	public function setIp( $ip ) {
		$this->data->ip = $ip;
	}

	/**
	 * IP property accessor
	 * @return String
	 */
	public function getIp() {
		return $this->data->ip;
	}

	/**
	 * Set visit UserAgent
	 * @param Nested\UserAgent $useragent
	 */
	public function setUserAgent( Nested\UserAgent $useragent ) {
		$this->data->useragent = $useragent;
	}

	/**
	 * UserAgent property accessor
	 * @return Nested\UserAgent
	 */
	public function getUserAgent() {
		return $this->data->useragent;
	}

	/**
	 * Set visit method
	 * @param String $method
	 */
	public function setMethod( $method ) {
		$this->data->method = $method;
	}

	/**
	 * Method property accessor
	 * @return String
	 */
	public function getMethod() {
		return $this->data->method;
	}

	/**
	 * Set visit Status code
	 * @param String $status
	 */
	public function setStatus( $status ) {
		$this->data->status = $status;
	}

	/**
	 * Status property accessor
	 * @return String
	 */
	public function getStatus() {
		return $this->data->status;
	}

	/**
	 * Set visit size
	 * @param String $size
	 */
	public function setSize( $size ) {
		$this->data->size = $size;
	}

	/**
	 * Size property accessor
	 * @return String
	 */
	public function getSize() {
		return $this->data->size;
	}

	/**
	 * Set visit HTTP version
	 * @param String $version
	 */
	public function setVersion( $version ) {
		$this->data->version = $version;
	}

	/**
	 * Version property accessor
	 * @return String
	 */
	public function getVersion() {
		return $this->data->version;
	}

	/**
	 * Set visit Referer url
	 * @param Nested\URL $referer
	 */
	public function setReferer( Nested\URL $referer ) {
		$this->data->referer = $referer;
	}

	/**
	 * Referer property accessor
	 * @return Nested\URL
	 */
	public function getReferer() {
		return $this->data->referer;
	}

	/**
	 * Set the visit origin (log file)
	 * @param String $origin
	 */
	public function setOrigin( $origin ) {
		$this->origin = $origin;
	}

	/**
	 * Origin property accessor
	 * @return String
	 */
	public function getOrigin() {
		return $this->origin;
	}

	/**
	 * Build a check sum string to be used to search duplicate in the index
	 * @return String
	 */
	public function getChecksum() {
		if( !isset($this->parent) || !isset($this->data->ip) || !isset($this->date) ) {
			throw new \Exception('parent, ip and date must be set before build Visit checkum string...');
		}

		if( !isset($this->data->checksum) ) {
			$this->data->checksum = sha1(
				$this->getParent().'-'.
				$this->data->ip.'-'.
				$this->getDate()->format('YmdHisZ')
			);
		}

		return $this->data->checksum;
	}

	/**
	 * If $oDate is given, only dated statistics will be retrieved, else, all Statistics object are returned as Array
	 * @return Boolean False if it's a new one, true if not
	 * @throws \Exception
	 */
	public function alreadyExists() {
		//Search in page index all document that have current statistics object as child
		$aRequest = array(
			'query' => array(
				'bool' => array(
					"should" => array(
						array( "term" => array( "checksum" => $this->getChecksum() ) ),
						array( "term" => array( "ip" => $this->data->ip ) )
					),
					"minimum_should_match" => 2
				)
			)
		);

		$aAnswer = self::search($aRequest);
		if( $aAnswer['hits']['total'] > 0 ) {
			return true;
		}

		return false;
	}

	/**
	 * Go a little deeper than the default hydrate method because of
	 * Nested\URL and Nested\UserAgent object initialisation
	 * @param Mixed $mData
	 */
	protected function hydrate( $mData ) {
		parent::hydrate($mData);

		$this->data->referer = Nested\URL::factory($this->data->referer);
		$this->data->useragent = Nested\UserAgent::factory($this->data->useragent);

		$this->origin = $this->data->origin;
		unset($this->data->origin);
	}

	/**
	 * Transform current object to JSON
	 * @return String
	 * @throws \Exception
	 */
	public function jsonSerialize() {
		$this->getChecksum();
		$oTmp = parent::jsonSerialize();
		$oTmp->origin = $this->origin;
		return $oTmp;
	}
}