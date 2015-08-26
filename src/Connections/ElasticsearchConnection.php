<?php

/**
 * This file is part of the BeeBot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2015
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Connections
 */

namespace BeeBot\Entity\Connections;

use BeeBot\Entity\Entity;
use BeeBot\Entity\Transactions\TransactionInterface;
use BeeBot\Entity\Connections\Events\ConnectionEvent;

/**
 * ElasticSearch connection adapter
 * Allow to perform operations on ES indexes
 * @package BeeBot\Entity\Connections
 * @see ConnectionInterface
 */
class ElasticsearchConnection extends AdaptableHttpConnection
{
    /**
     * @param string $type
     * @param string $term
     * @param mixed $value
     * @return int
     */
    public function countBy($type, $term, $value)
    {
        $response = $this->run(
            $type,
            ["query" => self::buildQuery($term, $value)],
            '_count'
        );
        return $response['count'];
    }

    /**
     * @param string $type
     * @param string $term
     * @param mixed $value
     * @param integer $count
     * @param integer $from
     * @param array $sort
     * @return array
     */
    public function fetchBy(
        $type,
        $term,
        $value,
        $count = null,
        $from = null,
        array $sort = null
    ) {
        $query = [
            "query" => self::buildQuery($term, $value),
            "size" => $count,
            "from" => $from,
            "sort" => $sort,
            "fields" => ['_source','_parent','_timestamp']
        ];
        $response = $this->handleResponse(
            $this->run($type, array_filter($query))
        );

        return $this->extractResults($response);
    }

    /**
     * @param string $type
     * @param string $query
     * @return array
     */
    public function raw($type, $query)
    {
        $response = $this->handleResponse(
            $this->run($type, $query)
        );
        return $this->extractResults($response);
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function save(Entity $entity)
    {
        parent::save($entity);

        if (!$entity::isJsonSerializable()) {
            throw new \InvalidArgumentException(
                'Given entity must use JsonSerializable behaviour'
            );
        }

        $url = '/'.$entity::getType().'/'.$entity->getUID();
        if ($entity::isChild() && $entity->getParent() !== null) {
            if (!$entity->getParent()->isPersisted()) {
                throw new \RuntimeException(
                    'Parent entity is not persisted'
                );
            }

            $url.='?parent='.$entity->getParent()->getUID();
        }

        $response = $this->getAdapter()->put(
            $url,
            json_encode($entity)
        );

        if ($this->handleResponse($response)) {
            $this->getAdapter()->post('/_refresh');
            return true;
        }
        return false;
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function delete(Entity $entity)
    {
        parent::delete($entity);

        if (!$entity::isJsonSerializable()) {
            throw new \InvalidArgumentException(
                'Given entity must use JsonSerializable behaviour'
            );
        }

        $response = $this->getAdapter()
            ->delete(
                '/'.$entity::getType().'/'.$entity->getUID()
            );

        $response = $this->handleResponse($response);
        if ($response['found'] === false) {
            throw new \InvalidArgumentException(
                'Given entity does not exists in ElasticSearch'
            );
        }
        $this->getAdapter()->post('/_refresh');
        return $response['found'];
    }

    /**
     * @param  TransactionInterface $transaction
     * @return bool
     */
    public function flush(TransactionInterface $transaction)
    {
        //Make bulk loading more powerful (by disabling auto refreshing)
        $this->getAdapter()
            ->put(
                '/_settings',
                '{ index: { refresh_interval: "-1" }}'
            );

        //Then start the import
        $string = "";
        foreach ($transaction as $entity) {
            $type = "index";
            if ($entity->isDeleted()) {
                $type = "delete";
            } elseif ($entity->isPersisted()) {
                $type = "update";
            }

            $template = <<<JSON
{ "%s": { _type:"%s", _id:"%s"%s} }
JSON;
            $string .= sprintf(
                $template,
                $type,
                $entity::getType(),
                $entity->getUID(),
                $entity::isChild()?
                ', "_parent": "'.$entity->getParent()->getUID().'"':
                ''
            );

            if ($type === 'create' || $type === 'index') {
                $string .= PHP_EOL.json_encode($entity).PHP_EOL;
            } elseif ($type === 'update') {
                $string .= PHP_EOL.'{"doc": '.json_encode($entity).' }'.PHP_EOL;
            }
        }
        $this->getAdapter()->post('/_bulk', $string);

        //When done restore standard parameters and trigger a refresh
        $this->getAdapter()
            ->post('/_refresh');
        $this->getAdapter()
            ->put('/_settings', '{ index: { refresh_interval: "1s" }}');
        return true;
    }

    /**
     * Make a search request on requested documents
     * @param string $type Document type to be searched
     * @param array $request Request array to be used for search
     * @param string $endpoint ElasticSearch endpoint
     * @return array|bool|float|int|string
     * @throws \RuntimeException
     */
    protected function run($type, array $request, $endpoint = "_search")
    {
        //Always return Parent and timestamp property!!
        if (($json = json_encode($request)) === false) {
            throw new \RuntimeException(sprintf(
                'Error during parameters JSON encoding: %s',
                $request
            ));
        }
        $response = $this->getAdapter()->post(
            '/'.$type.'/'.$endpoint,
            $json
        );

        //It's a search answer, we extract only the needed document
        return $this->handleResponse($response);
    }

    /**
     * Check if the current response contain invalid codes
     * @param array $response
     * @return boolean
     * @throws \RuntimeException
     */
    protected function handleResponse($response)
    {
        if( is_string($response) ) {
            if( null === $response = json_decode($response, true) ) {
                throw new \RuntimeException(
                    'Response is not a valid JSON string'
                );
            }
        }

        if (isset($response['error']) || (
                isset($response['status']) &&
                $response['status']!==200)
        ) {
            throw new \RuntimeException(sprintf(
                'Current request give an invalid response: %s',
                print_r($response, true)
            ));
        }
        if (isset($response['_shards']) && $response['_shards']['failed'] > 0) {
            throw new \RuntimeException(sprintf(
                'Some shards failed to give result: %s',
                print_r($response, true)
            ));
        }

        return $response;
    }

    /**
     * Extract data from given ES result (hits array)
     * Make some adjustement (uid) and prepare for Entity building
     * @param array $response
     * @return array
     */
    protected function extractResults(array $response)
    {
        $result = [];
        foreach ($response['hits']['hits'] as $hit) {
            $hit['_source']['uid'] = $hit['_id'];
            $result[] = $hit['_source'];
        }
        return $result;
    }

    /**
     * Compute a valid elasticsearch query from term and value
     * This method is a helper for search methods (count, fetch, ...)
     * @param Mixed $term The terme to be search
     * @param Mixed $value The value to be searched
     * @return Array
     */
    private static function buildQuery($term, $value)
    {
        //If value is an array of 2 elements
        if (is_array($value)) {
            if (count($value) === 1) {
                return self::buildQuery($term, $value[0]);
            } else {
                return [ 'terms' => [ $term => array_values($value) ] ];
            }
        } elseif (is_string($value)) {
            $parts = [];
            //Regexp or wildcard or prefix queries
            // => Warning about which Regexp are expensives
            // => Wildcard: * match multi characters and ? match single ones
            // => Prefix used to search terms that starts with $aParts[1]
            if (preg_match('/^(regexp|wildcard|prefix):(.*)$/', $value, $parts) === 1) {
                return [ $parts[1] => [$term => $parts[2]] ];
            //Specific range queries lesser than, greater than, lesser or equal and greater or equal
            } elseif (preg_match('/^(lt|gt|gte|lte):(.*)$/', $value, $parts) === 1) {
                return [
                    'range' => [ $term => [$parts[1] => $parts[2]] ]
                ];
            //Or complete range with specific values given as JSON array
            } elseif (preg_match('/^range:(.*)$/', $value, $parts) === 1) {
                $dates = json_decode($parts[1]);
                return self::buildQuery($term, $dates===null?[$parts[1],$parts[1]]:$dates);
            }
        }

        //Standard one is the term query
        return [ 'term' => [ $term => $value ] ];
    }
}
