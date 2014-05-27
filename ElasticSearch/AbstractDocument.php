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

namespace BeeBot\Entity\ElasticSearch;


use BeeBot\Config;
use BeeBot\Entity\Entity;
use BeeBot\Entity\Behaviours\ChildEntity;
use BeeBot\Entity\Behaviours\DatedEntity;
use BeeBot\Entity\Behaviours\FactoryEntity;
use BeeBot\Entity\EntityCollection;

use BeeBot\Tools\Native\JsonTransformer;
use Bee4\Http\Client;

/**
 * Define basic elasticsearch document
 * @package BeeBot\Entity\ElasticSearch
 */
abstract class AbstractDocument extends Entity implements \JsonSerializable {
	use DatedEntity, ChildEntity, FactoryEntity;

	/**
	 * ElasticSearch index name came from XML configuration
	 * @var String
	 * @static
	 */
	protected static $index;

	/**
	 * ElasticSearch webservice URL came from XML configuration
	 * @var String
	 * @static
	 */
	protected static $ws;

	/**
	 * Object data
	 * @var \stdClass
	 */
	protected $data;

	/**
	 * ElasticSearch Document constructor
	 */
	public function __construct() {
		$this->data = new \stdClass();
		$this->setDate(new \DateTime);

		parent::__construct();
	}

	/**
	 * Type property accessor
	 * @return String
	 */
	public static function getElasticSearchType() {
		$aClass = explode('\\',get_called_class());
		return strtolower(array_pop($aClass));
	}

	/**
	 * Index property accessor
	 * @return String
	 */
	public static function getElasticSearchIndex() {
		if( !isset(self::$index) ) {
			self::$index = Config::getGlobal('elasticsearch-index');
		}

		return self::$index;
	}

	/**
	 * Index property accessor
	 * @return String
	 */
	protected static function getWS() {
		if( !isset( self::$ws ) ) {
			//Build a valid URL
			self::$ws = Config::getGlobal('elasticsearch-url');
			if( strrpos(self::$ws, '/') !== strlen(self::$ws)-1 ) {
				self::$ws .= '/';
			}
		}

		return self::$ws;
	}

	/**
	 * Persist document modification in the index
	 * @return bool
	 * @throws \Exception
	 */
	public function save() {
		$oClient = new Client();

		$sURL = self::getWS().self::getElasticSearchIndex().'/'.self::getElasticSearchType().'/'.$this->uid;
		$sJSON = JsonTransformer::encode($this);

		//If it's a creation and document is defined as a child of another one
		if( trim($this->parent) != '' ) {
			$sURL .= '?parent='.$this->parent;
		}

		$oResponse = $oClient->put($sURL)->setBody($sJSON)->send();
		if( substr($oResponse->getStatus(), 0, 1) !== "2" ) {
			throw new \BeeBot\Exception\Native\RuntimeException('Document can\'t be saved ('.$oResponse->getStatus().'): '.$sJSON);
		}

		$aResponse = $oResponse->json();
		$this->uid = $aResponse['_id'];

		return true;
	}

	/**
	 * Delete current document from ElasticSearch index
	 * @throws \Exception
	 */
	public function delete() {
		$oClient = new Client();
		$oClient->delete(
			self::getWS().self::getElasticSearchIndex().'/'.self::getElasticSearchType().'/'.$this->uid
		)->send();

		$this->uid = uniqid();
	}

	/**
	 * Make a search request on requested documents
	 * @param Array $aRequest Request array to be used for search
	 * @param string $sType Document type to be searched
	 * @param string $sEndPoint ElasticSearch endpoint used for the current request
	 *
	 * @return array|bool|float|int|string
	 * @throws \Exception
	 */
	protected static function search( Array $aRequest, $sType = "", $sEndPoint = "_search" ) {
		$oClient = new Client();
		$oRequest = $oClient->post(
			self::getWS().self::getElasticSearchIndex().'/'.($sType !== ""?$sType:self::getElasticSearchType()).'/'.$sEndPoint.'?pretty'
		);

		//Check if it's a standard search query or if it's a custom (like _count need)
		//If it's standard we add fields else we do noting
		if( isset( $aRequest['query'] ) ) {
			//Always return Parent and timestamp property!!
			if( !isset( $aRequest['fields'] ) ) {
				$aRequest['fields'] = array('_parent','_source','_timestamp');
			}
			if( !in_array('_parent', $aRequest['fields']) ) {
				$aRequest['fields'][] = '_parent';
			}
			if( !in_array('_timestamp', $aRequest['fields']) ) {
				$aRequest['fields'][] = '_timestamp';
			}
		}

		$sJSON = JsonTransformer::encode($aRequest);
		if( $sJSON === false ) {
			throw new \Exception('An error occured during JSON encoding of the given parameters');
		}
		$oRequest->setBody($sJSON);

		$oResponse = $oRequest->send();

		//It's a search answer, we extract only the needed document
		return $oResponse->json();
	}

	/**
	 * Retrieve a Collection of Document object from a given term value
	 * @param String $sTerm Term name the value will be searched in
	 * @param String $sValue The value of the term to be searched that will be regexp on the index
	 * @param Integer $iCount Number of results to get
	 * @param Integer $iFrom Document position where we start to retrieve results
	 * @return EntityCollection
	 * @throws \Exception
	 */
	public static function fetchBy($sTerm, $mValue, $iCount = null, $iFrom = null, array $aSort = null) {
		$aQuery = array(
			'size' => isset($iCount)?$iCount:0,
			'from' => isset($iFrom)?$iFrom:0,
			'query' => self::buildQuery($sTerm, $mValue)
		);

		if( isset($aSort) ) {
			$aQuery['sort'] = [];
			foreach( $aSort as $field => $order ) {
				$aQuery['sort'][] = [$field => $order];
			}
		}

		$aAnswer = self::search($aQuery);
		$oResult = new EntityCollection();

		if( $aAnswer['hits']['total'] !== 0 && !isset($iCount) ) {
			$aQuery['size'] = $aAnswer['hits']['total'];
			$aAnswer = self::search($aQuery);
		}
		//It's a search answer, we extract only the needed document
		foreach( $aAnswer['hits']['hits'] as $oAnswer ) {
			$oResult->append(call_user_func(array(get_called_class(), 'factory'), $oAnswer));
		}

		return $oResult;
	}

	/**
	 * Execute a raw query as array on the index
	 * @param array $query The query to execute
	 * @return null|EntityCollection
	 */
	public static function rawQuery( array $query ) {
		$aAnswer = self::search($query);
		if( isset($aAnswer['hits']) && isset($aAnswer['hits']['hits']) ) {
			$oResult = new EntityCollection();
			foreach( $aAnswer['hits']['hits'] as $oAnswer ) {
				$oResult->append(call_user_func(array(get_called_class(), 'factory'), $oAnswer));
			}
			return $oResult;
		}
		return null;
	}

	/**
	 * Retrieve document count from a given term value
	 * @param String $sTerm Term name the value will be searched in
	 * @param String $sValue The value of the term to be searched
	 * @return Integer
	 * @throws \Exception
	 */
	public static function countBy($sTerm, $mValue) {
		$aAnswer = self::search(
			//['query' => self::buildQuery($sTerm, $mValue)],
			self::buildQuery($sTerm, $mValue),
			"",
			'_count'	//Use specific count endpoint
		);

		return $aAnswer['count'];
	}

	/**
	 * Transform current object to JSON
	 * @return stdClass|Array
	 */
	public function jsonSerialize() {
		$oTmp = clone $this->data;

		$oTmp->_id = $this->uid;
		$oTmp->_timestamp = $this->date->format('Y-m-d\TH:i:sP');

		return $oTmp;
	}

	/**
	 * Update object details from decoded JSON
	 * @param Mixed $mData The data to use to wake up
	 */
	protected function hydrate( $mData ) {
		$data = $mData['_source'];
		unset($data['_id']);
		unset($data['_timestamp']);

		$this->uid = $mData['_id'];

		foreach( $data as $sKey => $sValue ) {
			$this->data->{$sKey} = $sValue;
		}

		$this->parent = $this->date = null;
		if( isset($mData['fields']) ) {
			$this->parent = isset($mData['fields']['_parent'])?$mData['fields']['_parent']:null;
			$this->date = isset($mData['fields']['_timestamp'])?\DateTime::createFromFormat('Y-m-d\TH:i:sP', $mData['fields']['_timestamp']):null;
		}
	}

	/**
	 * Compute a valid elasticsearch query from term and value
	 * This method is just a helper for the search methods (count, fetch, ...)
	 * @param Mixed $mTerm The terme to be search
	 * @param Mixed $mValue The value to be searched
	 * @return Array
	 */
	private static function buildQuery( $mTerm, $mValue ) {
		//If value is an array of 2 elements
		if( is_array($mValue) ) {
			if( count($mValue) == 2 ) {
				return array(
					'range' => array(
						$mTerm => array(
							'from' => $mValue[0],
							'to' => $mValue[1]
						)
					)
				);
			} elseif( count($mValue) === 1 ) {
				return self::buildQuery($mTerm, $mValue[0]);
			}
		} elseif( is_string( $mValue ) ) {
			$aParts = array();
			//Regexp query used to search terms that match a pattern (warning about performances)
			if( preg_match('/^regexp:(.*)$/', $mValue, $aParts ) === 1 ) {
				return array(
					'regexp' => array( $mTerm => $aParts[1] )
				);
			//Prefix query used to search terms that starts with $aParts[1]
			} elseif( preg_match('/^prefix:(.*)$/', $mValue, $aParts ) === 1 ) {
				return array(
					'prefix' => array( $mTerm => $aParts[1] )
				);
			//Wildcard query used to retrieve items by wildcard * match mutli characters and ? match single ones
			} elseif( preg_match('/^wildcard:(.*)$/', $mValue, $aParts ) === 1 ) {
				return array(
					'wildcard' => array( $mTerm => $aParts[1] )
				);
			//Specific range queries lesser than, greater than, lesser or equal and greater or equal
			} elseif( preg_match('/^(lt|gt|gte|lte):(.*)$/', $mValue, $aParts ) === 1 ) {
				return array(
					'range' => array(
						$mTerm => array(
							$aParts[1] => $aParts[2]
						)
					)
				);
			} elseif( preg_match('/^range:(.*)$/', $mValue, $aParts ) === 1 ) {
				$aDates = json_decode($aParts[1]);
				if( $aDates === null ) {
					$aDates = array($aParts[1],$aParts[1]);
				}
				return self::buildQuery($mTerm, $aDates);
			}
		}

		//Standard one is the term query
		return array(
			'term' => array( $mTerm => $mValue )
		);
	}
}
