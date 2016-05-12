<?php

/**
 * This file is part of the bee4/activerecord package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2015
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Tests\Behaviours
 */

namespace BeeBot\Entity\Tests\Connections;

require_once __DIR__.'/../../samples/SampleJsonSerializableEntity.php';

use BeeBot\Entity\Connections\Adapters\Bee4TransportAdapter;
use BeeBot\Entity\Connections\ElasticsearchConnection;
use BeeBot\Entity\ActiveRecord;
use BeeBot\Entity\Transactions\MemoryTransaction;
use BeeBot\Entity\Tests\Samples\SampleJsonSerializableEntity;
use Bee4\Transport\Client;

/**
 * Check the child entity behaviour
 * @package BeeBot\Entity\Tests\Behaviours
 * @group connection
 */
class ElasticsearchConnectionTest extends \PHPUnit_Framework_TestCase
{
    protected static $data = [
        ['truite' => 'saumon', 'editable' => 'value'],
        ['truite' => 'saumon', 'editable' => 'value'],
        ['truite' => 'saumon'],
        ['truite' => 'saumon'],
        ['truite' => 'saumon'],
        ['truite' => 'saumon'],
        ['truite' => 'saumon'],
        ['truite' => 'saumon'],
        ['truite' => 'saumon'],
        ['truite' => 'saumon'],
        ['truite' => 'fish'],
        ['truite' => 'fish'],
        ['truite' => 'fish'],
        ['truite' => 'fish'],
        ['truite' => 'abcd'],
    ];

    /**
     * ElasticSearch connection instance
     * @var ConnectionInterface
     */
    protected static $connection;

    /**
     * Count the number of saved Entities
     * @var integer
     */
    protected static $saved = [
        'truite' => [],
        'editable' => []
    ];

    /**
     * Build connection + Tmp index
     * If Elasticsearch is not available, just mark test as skipped
     */
    public static function setUpBeforeClass()
    {
        $index = uniqid('tmp_test_');

        self::$connection = new ElasticsearchConnection();
        self::$connection->setRoot(ELASTICSEARCH_SERVER);
        self::$connection->setAdapter(new Bee4TransportAdapter(new Client));
        if (false === self::$connection->getAdapter()->head('/')) {
            self::markTestSkipped(
                'ElasticSearch server is not available'
            );
        }
        self::$connection->setRoot(ELASTICSEARCH_SERVER.'/'.$index);
        self::$connection
            ->getAdapter()
                ->put('/');
    }

    /**
     * Remove tmp index and shutdown connection
     */
    public static function tearDownAfterClass()
    {
        self::$connection
            ->getAdapter()
                ->delete('/');
        self::$connection = null;
    }

    //----------------------------------------------------------------------------

    public function testFlush()
    {
        $transaction = new MemoryTransaction();
        foreach (self::$data as $fields) {
            $entity = new SampleJsonSerializableEntity;
            foreach ($fields as $name => $value) {
                $entity->{$name} = $value;

                $this->handleStat($name, $value);
            }

            $transaction->persist($entity);
        }
        $this->assertTrue(
            self::$connection->flush($transaction)
        );
    }

    public function testSave()
    {
        $entity = new SampleJsonSerializableEntity;
        $entity->truite = uniqid('fish_', true);
        $this->assertTrue(
            self::$connection->save($entity)
        );

        $this->handleStat('truite', $entity->truite);
    }

    public function testCountBy()
    {
        foreach (self::$saved as $name => $values) {
            foreach ($values as $value => $count) {
                $result = self::$connection->countBy(
                    SampleJsonSerializableEntity::getType(),
                    $name,
                    $value
                );

                $this->assertEquals($count, $result);
            }
        }
    }

    public function testFetchBy()
    {
        foreach (self::$saved as $name => $values) {
            foreach ($values as $value => $count) {
                $results = self::$connection->fetchBy(
                    SampleJsonSerializableEntity::getType(),
                    $name,
                    $value
                );

                $this->assertTrue(is_array($results));
                $this->assertEquals($count, count($results));
                foreach ($results as $result) {
                    $this->assertEquals($value, $result[$name]);
                }
            }
        }
    }

    public function testFetchByAndCount()
    {
        $results = self::$connection->fetchBy(
            SampleJsonSerializableEntity::getType(),
            'readable',
            'readable',
            1
        );
        $this->assertEquals(1, count($results));
    }

    public function testCreateDelete()
    {
        $truite = uniqid('fish_', true);
        $entity = new SampleJsonSerializableEntity;
        $entity->truite = $truite;
        $this->assertTrue(
            self::$connection->save($entity)
        );
        $results = self::$connection->fetchBy(
            SampleJsonSerializableEntity::getType(),
            'truite',
            $truite
        );
        $this->assertEquals(1, count($results));
        $this->assertEquals($truite, $results[0]['truite']);

        $this->assertTrue(
            self::$connection->delete($entity)
        );

        $results = self::$connection->fetchBy(
            SampleJsonSerializableEntity::getType(),
            'truite',
            $truite
        );
        $this->assertTrue(count($results) === 0);
    }

    //----------------------------------------------------------------------------

    private function handleStat($name, $value)
    {
        self::$saved[$name][$value] =
            isset(self::$saved[$name][$value])?
                self::$saved[$name][$value]+=1:
                1;
    }
}
