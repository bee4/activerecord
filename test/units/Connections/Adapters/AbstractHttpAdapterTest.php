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

namespace BeeBot\Entity\Tests\Connections\Adapters;

/**
 * Check the child entity behaviour
 * @package BeeBot\Entity\Tests\Behaviours
 */
abstract class AbstractHttpAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractHttpAdapter
     */
    protected $object;

    private function check($result, $url, $body)
    {
        $result = json_decode($result, true);

        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('args', $result);
        $this->assertArrayHasKey('headers', $result);

        $this->assertEquals($url, $result['url']);

        if (is_array($body)) {
            foreach ($body as $key => $val) {
                $this->assertArrayHasKey($key, $result['form']);
                $this->assertEquals($val, $result['form'][$key]);
            }
        }
    }

    public function contextProvider()
    {
        return [
            ['get', 'https://httpbin.org/get'],
            ['post', 'https://httpbin.org/post', ['PostBody'=>'Toto']],
            ['put', 'https://httpbin.org/put', '{"PutBody":"Tata"}'],
            ['delete', 'https://httpbin.org/delete', '{"DeleteBody":"Tutu"}']
        ];
    }

    /**
     * @dataProvider contextProvider
     */
    public function testMethod($method, $url, $body = null)
    {
        $result = call_user_func(
            [$this->object, $method],
            $url,
            $body
        );
        $this->check($result, $url, $body);
    }

    public function testInlineHeaders()
    {
        $result = json_decode($this->object->get(
            'https://httpbin.org/headers',
            ['X-Test: Value']
        ), true);
        $this->assertArrayHasKey('X-Test', $result['headers']);
        $this->assertEquals('Value', $result['headers']['X-Test']);
    }

    public function testAssocHeaders()
    {
        $result = json_decode($this->object->get(
            'https://httpbin.org/headers',
            ['X-Test' => 'Value']
        ), true);
        $this->assertArrayHasKey('X-Test', $result['headers']);
        $this->assertEquals('Value', $result['headers']['X-Test']);
    }
}
