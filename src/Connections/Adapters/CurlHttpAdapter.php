<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2015
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Connections\Adapters
 */

namespace BeeBot\Entity\Connections\Adapters;

/**
 * Default Curl adapter to use with HTTP connections
 * No events, simple HTTP api...
 * @package BeeBot\Entity\Connections\Adapters
 */
class CurlHttpAdapter extends AbstractHttpAdapter
{
    /**
     * Curl open handle
     * @var resource
     */
    protected $handle;

    /**
     * Initialize the adapter
     * @param Client $adaptee
     */
    public function __construct()
    {
        $this->handle = curl_init();
    }

    /**
     * Release curl resource
     */
    public function __destruct()
    {
        curl_close($this->handle);
    }

    /**
     * Exec the query and retrieve result
     * @param  string     $method  HTTP Method
     * @param  string     $url     URL to call
     * @param  array|null $headers Header collection
     * @param  string     $body    Body to be sent
     * @return string
     */
    private function exec($method, $url, array $headers = null, $body = null)
    {
        curl_reset($this->handle);

        $headers = (null !== $headers)?$headers:[];
        if (array_keys($headers) !== range(0, count($headers) - 1)) {
            array_walk($headers, function (&$item, $key) {
                $item = $key.': '.$item;
            });
            $headers = array_values($headers);
        }

        $options = [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => is_array($body)?http_build_query($body):$body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true
        ];

        switch ($method) {
            case 'GET':
                $options[CURLOPT_HTTPGET] = true;
                break;
            case 'HEAD':
                $options[CURLOPT_NOBODY] = true;
                break;
            case 'POST':
                $options[CURLOPT_POST] = true;
                break;
            case 'PUT':
                if (is_resource($body)) {
                    unset($options[CURLOPT_POSTFIELDS]);
                    $options[CURLOPT_PUT] = true;
                    rewind($body);
                    $options[CURLOPT_INFILE] = $body;
                    break;
                }
                //Else PUT is considered with a string body
            default:
                $options[CURLOPT_CUSTOMREQUEST] = $method;
                break;
        }
        curl_setopt_array($this->handle, $options);
        return curl_exec($this->handle);
    }

    public function get($url, array $headers = null)
    {
        return $this->exec(
            'GET',
            $this->getRoot().$url,
            $headers
        );
    }

    public function post($url, $body = null, array $headers = null)
    {
        return $this->exec(
            'POST',
            $this->getRoot().$url,
            $headers,
            $body
        );
    }

    public function head($url, array $headers = null)
    {
        return $this->exec(
            'HEAD',
            $this->getRoot().$url,
            $headers
        );
    }

    public function put($url, $body = null, array $headers = null)
    {
        return $this->exec(
            'PUT',
            $this->getRoot().$url,
            $headers,
            $body
        );
    }

    public function delete($url, $body = null, array $headers = null)
    {
        return $this->exec(
            'DELETE',
            $this->getRoot().$url,
            $headers,
            $body
        );
    }
}
