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
namespace BeeBot\Entity\Behaviours\ElasticSearch;

use BeeBot\Config;
use BeeBot\Entity\Behaviours\BulkLoadingStrategy;
use BeeBot\Entity\Entity;
use BeeBot\Entity\ElasticSearch\AbstractDocument;

use BeeBot\Tools\Logger\LoggerFactory;
use BeeBot\Tools\Native\JsonTransformer;
use BeeBot\Tools\File\Stream\TmpStream;
use BeeBot\Tools\Configurable;

use Bee4\Http\Client;

/**
 * Specific ElasticSearch bulk loader implementation
 * @package BeeBot\Entity\Behaviours\ElasticSearch
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 */
class ElasticSearchBulkLoader extends Configurable implements BulkLoadingStrategy {
	/**
	 * Bulk action collection
	 * @var Array
	 */
	private $bulk = array();

	/**
	 * BeeBot Http client instance
	 * @var Client
	 */
	private $client;

	/**
	 * Boolean to define if an ES optimize call must be done after all imports or not
	 * @var boolean
	 */
	private $optimize = false;

	/**
	 * @var int
	 */
	private $count = 0;

	/**
	 * ElasticSearchBulkLoader constructor
	 */
	public function __construct() {
		//Build a valid URL
		$ws = Config::getGlobal('elasticsearch-url');
		if( strrpos($ws, '/') !== strlen($ws)-1 )
			$ws .= '/';
		$this->client = new Client($ws.Config::getGlobal('elasticsearch-index').'/');

		//Build configurable behaviour and use retrieved properties
		parent::__construct();
		$aConfig = $this->getConfiguredProperties();
		$this->optimize = $aConfig['optimize']==="true"?true:false;
	}

	/**
	 * Trigger an optimize request on the loaded index
	 */
	public function __destruct() {
		if( $this->optimize )
			$this->client->post('_optimize?max_num_segments=5')->send();
	}

	/**
	 * Add an item to be loaded
	 * @param Entity $oItem
	 */
	public function add( Entity $oItem ) {
		if( !($oItem instanceof AbstractDocument) )
			throw new \BeeBot\Exception\Native\InvalidArgumentException(
				'Given item must be an ElasticSearch\AbstractDocument entity'
			);

		if( $this->count%self::LIMIT == 0 ) {
			$this->bulk[] = new TmpStream();
		}

		$this->bulk[count($this->bulk)-1]->write(
			'{ "index" : { "_type": "'.$oItem::getElasticSearchType().'", "_id": "'.$oItem->getUID().'"'.(null !== $oItem->getParent()?', "_parent": "'.$oItem->getParent().'"':'').' } }'.
			PHP_EOL.
			JsonTransformer::encode($oItem).PHP_EOL
		);
		$this->count++;
	}

	/**
	 * Trigger a save on all objects using elasticsearch bulkloader, when done clean all properties to have bulkloader empty
	 *
	 * @see \BeeBot\Entity\Behaviours\BulkLoadingStrategy::flush Interface method
	 */
	public function flush() {
		//Make bulk loading more powerful (by disabling autorefreshing)
		$this->client->put('_settings')->setBody('{ index: { refresh_interval: "-1" }}')->send();

		//Then start the import
		foreach( $this->bulk as $oStream ) {
			$this->client
				->post('_bulk')
				->addCurlOption(CURLOPT_TIMEOUT, 120)
				->setBody($oStream->getContent())
				->send();
			unset($oStream);
		}

		//When done restore standard parameters
		$this->client->put('_settings')->setBody('{ index: { refresh_interval: "1s" }}')->send();

		//And reinit object properties...
		$this->bulk = array();
		$this->count = 0;
	}

	/**
	 * Define an object configurable property to define if the optimize must be set at __destruct call
	 */
	protected function getProperties() {
		return array('optimize');
	}
}