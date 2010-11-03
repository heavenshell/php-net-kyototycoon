<?php
/**
 * KyotoTycoon client library.
 *
 * PHP version 5.3
 *
 * Copyright (c) 2010 Shinya Ohyanagi, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Shinya Ohyanagi nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @use       Net
 * @category  Net
 * @package   Net\KyotoTycoon
 * @version   $id$
 * @copyright (c) 2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 * @link      http://fallabs.com/kyototycoon/
 * @link      http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/
 */
namespace Net;
use Net\KyotoTycoon\StatusCode,
    Net\KyotoTycoon\Exception,
    Net\KyotoTycoon\Cursor,
    HTTP\TsvRpc\Client;

/**
 * KyotoTycoon
 *
 * <pre>
 *   This module is port from Perl Cache::KyotoTycoon.
 *   See also @link.
 * </pre>
 *
 * @use       Net
 * @category  Net
 * @package   Net\KyotoTycoon
 * @version   $id$
 * @copyright 2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 * @link      http://fallabs.com/kyototycoon/
 * @link      http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/
 */
class KyotoTycoon
{
    /**
     * Version.
     */
    const VERSION = '0.0.1';

    /**
     * Status code.
     *
     * @var    mixed
     * @access private
     */
    private $_statusCode = null;

    /**
     * Client.
     *
     * @var    mixed
     * @access private
     */
    private $_client = null;

    /**
     * Db.
     *
     * @var    mixed
     * @access private
     */
    private $_db = null;

    /**
     * Error message.
     *
     * @param  mixed $code Status code.
     * @access private
     * @return String Error message.
     */
    private function _errmsg($code)
    {
        return $this->_statusCode->errmsg($code);
    }

    /**
     * Create client to access KyotoTycoon.
     *
     * @param  array $args
     * @access public
     * @return void
     */
    public function __construct(array $args = array())
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
        $host    = isset($args['host']) ? $args['host'] : '127.0.0.1';
        $port    = isset($args['port']) ? $args['port'] : 1978;
        $base    = "http://$host:$port/rpc/";
        $timeout = isset($args['timeout']) ? $args['timeout'] : 1;
        $client  = new Client(
            array(
                'timeout' => $timeout,
                'base'    => $base
            )
        );

        $this->_db         = 0;
        $this->_client     = $client;
        $this->_statusCode = new StatusCode();
    }

    /**
     * Set TSV-RPC client.
     *
     * @param  mixed $client TSV-RPC client
     * @access public
     * @return \Net\KyotoTycoon Fluent interface
     */
    public function setClient($client)
    {
        $this->_client = $client;
        return $this;
    }

    /**
     * Set db.
     *
     * @param  mixed $db
     * @access public
     * @return \Net\KyotoTycoon Fluent interface.
     */
    public function db($db)
    {
        $this->_db = $db;
        return $this;
    }

    /**
     * Make cursor
     *
     * @param  mixed $id
     * @access public
     * @return \Net\KyotoTycoon\Cursor
     */
    public function makeCursor($id)
    {
        $cursor = new Cursor($id, $this->_db, $this->_client);
        return $cursor;
    }

    /**
     * Call echo.
     *
     * <pre>
     *   Echo back the input data as the output data
     *   echo() is reserved name in PHP. This method is same as echo().
     * </pre>
     *
     * @param  mixed $args
     * @access public
     * @return mixed Response of RPC call
     */
    public function echoBack($args)
    {
        $response = $this->_client->call('echo', $args);
        if ($response['code'] !== 200) {
            return $this->_errmsg($response['code']);
        }

        return $response['body'];
    }

    /**
     * Call report.
     *
     * <pre>
     *   Get the report of the server information.
     * </pre>
     *
     * @access public
     * @return mixed Response of RPC call
     */
    public function report()
    {
        $response = $this->_client->call('report');
        if ($response['code'] !== 200) {
            return $this->_errmsg($response['code']);
        }

        return $response['body'];
    }

    /**
     * Call play_script.
     *
     * <pre>
     *   Call a procedure of the scripting extension.
     * </pre>
     *
     * @param  mixed $name
     * @param  array $input
     * @access public
     * @return mixed Response of RPC call
     */
    public function playScript($name, array $input)
    {
        $args = array('name' => $name);
        foreach ($input as $k => $v) {
            $args["_$k"] = $v;
        }
        $response = $this->_client->call('play_script', $args);
        if ($response['code'] !== 200) {
            return $this->_errmsg($response['code']);
        }
        $body = $response['body'];
        $res  = array();
        foreach ($body as $k => $v) {
            $res[ltrim($k, '_')] = $v;
        }

        return $res;
    }

    /**
     * Call status.
     *
     * <pre>
     *   Get the miscellaneous status information.
     * </pre>
     *
     * @access public
     * @return mixed Response of RPC call
     */
    public function status()
    {
        $response = $this->_client->call('status', array('DB' => $this->_db));
        if ($response['code'] !== 200) {
            return $this->_errmsg($response['code']);
        }

        return $response['body'];
    }

    /**
     * Call clear
     *
     * <pre>
     *   Remove all records.
     * </pre>
     *
     * @access public
     * @return mixed Fluent interface when clear success or error message.
     */
    public function clear()
    {
        $response = $this->_client->call('clear', array('DB' => $this->_db));
        if ($response['code'] !== 200) {
            return $this->_errmsg($response['code']);
        }

        return $this;
    }

    /**
     * Call synchronize
     *
     * <pre>
     *   Synchronize updated contents with the file and the device.
     * </pre>
     *
     * @param  mixed $hard
     * @param  mixed $command
     * @access public
     * @return mixed Status 200 return true, status 450 return false
     */
    public function synchronize($hard = null, $command = null)
    {
        $args = array('DB' => $this->_db);
        $args['hard'] = $hard;
        if (!is_null($command)) {
            $args['command'] = $command;
        }

        $response = $this->_client->call('synchronize', $args);
        if ($response['code'] === 200) {
            return true;
        } else if ($response['code'] === 450) {
            return false;
        }

        return $this->_errmsg($response['code']);
    }

    /**
     * Call set.
     *
     * <pre>
     *   Set the value of a record.
     * </pre>
     *
     * @param  mixed $key
     * @param  mixed $value
     * @param  mixed $xt
     * @access public
     * @return mixed Fluent interface or error message
     */
    public function set($key, $value, $xt = null)
    {
        $args = array('DB' => $this->_db, 'key' => $key, 'value' => $value);
        if (!is_null($xt)) {
            $args['xt'] = $xt;
        }

        $response = $this->_client->call('set', $args);
        if ($response['code'] !== 200) {
            return $this->_errmsg($response['code']);
        }

        return $this;
    }

    /**
     * Call add.
     *
     * <pre>
     *   Add a record.
     * </pre>
     *
     * @param  mixed $key
     * @param  mixed $value
     * @param  mixed $xt
     * @access public
     * @return mixed true:Success to add, false: Fail to add or error message
     */
    public function add($key, $value, $xt = null)
    {
        $args = array('DB' => $this->_db, 'key' => $key, 'value' => $value);
        if (!is_null($xt)) {
            $args['xt'] = $xt;
        }

        $response = $this->_client->call('add', $args);
        if ($response['code'] === 200) {
            return true;
        } else if ($response['code'] === 450) {
            return false;
        }

        return $this->_errmsg($response['code']);
    }

    /**
     * Call replace.
     *
     * <pre>
     *   Replace a record.
     * </pre>
     *
     * @param  mixed $key
     * @param  mixed $value
     * @param  mixed $xt
     * @access public
     * @return mixed True:success, False:fail or error message.
     */
    public function replace($key, $value, $xt = null)
    {
        $args = array('DB' => $this->_db, 'key' => $key, 'value' => $value);
        if (!is_null($xt)) {
            $args['xt'] = $xt;
        }

        $response = $this->_client->call('replace', $args);
        if ($response['code'] === 200) {
            return true;
        } else if ($response['code'] === 450) {
            return false;
        }

        return $this->_errmsg($response['code']);
    }

    /**
     * Call append.
     *
     * <pre>
     *   Set the value of a record.
     * </pre>
     *
     * @param  mixed $key
     * @param  mixed $value
     * @param  mixed $xt
     * @access public
     * @return mixed Fluent interface or error message
     */
    public function append($key, $value, $xt = null)
    {
        $args = array('DB' => $this->_db, 'key' => $key, 'value' => $value);
        if (!is_null($xt)) {
            $args['xt'] = $xt;
        }
        $response = $this->_client->call('append', $args);
        if ($response['code'] !== 200) {
            return $this->_errmsg($response['code']);
        }

        return $this;
    }

    /**
     * Call increment.
     *
     * <pre>
     *   Add a number to the numeric integer value of a record.
     * </pre>
     *
     * @param  mixed $key
     * @param  mixed $num
     * @param  mixed $xt
     * @access public
     * @return mixed Response of RPC call
     */
    public function increment($key, $num, $xt = null)
    {
        $args = array('DB' => $this->_db, 'key' => $key, 'num' => $num);
        if (!is_null($xt)) {
            $args['xt'] = $xt;
        }
        $response = $this->_client->call('increment', $args);
        if ($response['code'] !== 200) {
            return $this->_errmsg($response['code']);
        }

        return intval($response['body']['num']);
    }

    /**
     * Call incrementDouble.
     *
     * <pre>
     *   Add a number to the numeric double value of a record.
     * </pre>
     *
     * @param  mixed $key
     * @param  mixed $num
     * @param  mixed $xt
     * @access public
     * @return mixed Response of RPC call
     */
    public function incrementDouble($key, $num, $xt = null)
    {
        $args = array('DB' => $this->_db, 'key' => $key, 'num' => $num);
        if (!is_null($xt)) {
            $args['xt'] = $xt;
        }
        $response = $this->_client->call('increment_double', $args);
        if ($response['code'] !== 200) {
            return $this->_errmsg($response['code']);
        }

        return doubleval($response['body']['num']);
    }

    /**
     * Call cas.
     *
     * <pre>
     *   Perform compare-and-swap.
     * </pre>
     *
     * @param  mixed $key
     * @param  mixed $oval
     * @param  mixed $nval
     * @param  mixed $xt
     * @access public
     * @throws \Net\KyotoTycoon\Exception
     * @return Bool True: Status code is 200, False: Status code is 450
     */
    public function cas($key, $oval = null, $nval = null, $xt = null)
    {
        $args = array('DB' => $this->_db, 'key' => $key);
        $list = array('oval' => $oval, 'nval' => $nval, 'xt' => $xt);

        foreach ($list as $k => $v) {
            if (!is_null($v)) {
                $args[$k] = $v;
            }
        }
        $response = $this->_client->call('cas', $args);
        if ($response['code'] === 200) {
            return true;
        } else if ($response['code'] === 450) {
            return false;
        }

        throw new Exception($this->_errmsg($response['code']));
    }

    /**
     * Call remove.
     *
     * <pre>
     *   Remove a record.
     * </pre>
     *
     * @param  mixed $key
     * @access public
     * @throws \Net\KyotoTycoon\Exception
     * @return Bool True: Status code is 200, False: Status code is 450.
     */
    public function remove($key)
    {
        $args = array('DB' => $this->_db, 'key' => $key);
        $response = $this->_client->call('remove', $args);

        if ($response['code'] === 200) {
            return true;
        } else if ($response['code'] === 450) {
            return false;
        }

        throw new Exception($this->_errmsg($response['code']));
    }

    /**
     * Call get.
     *
     * <pre>
     *   Retrieve the value of a record.
     * </pre>
     *
     * @param  mixed $key
     * @access public
     * @throws \Net\KyotoTycoon\Exception
     * @return mixed Response of RPC call
     */
    public function get($key)
    {
        $args = array('DB' => $this->_db, 'key' => $key);
        $response = $this->_client->call('get', $args);

        if ($response['code'] === 200) {
            return $response['body']['value'];
        } else if ($response['code'] === 450) {
            return null;
        }

        throw new Exception($this->_errmsg($response['code']));
    }

    /**
     * Call set_bulk.
     *
     * <pre>
     *   Store records at once.
     * </pre>
     *
     * @param  array $vals
     * @param  mixed $xt
     * @access public
     * @throws \Net\KyotoTycoon\Exception
     * @return mixed Response of RPC call
     */
    public function setBulk(array $vals, $xt = null)
    {
        $args = array('DB' => $this->_db);
        foreach ($vals as $k => $v) {
            $args["_$k"] = $v;
        }
        if (!is_null($xt)) {
            $args['xt'] = $xt;
        }
        $response = $this->_client->call('set_bulk', $args);
        if ($response['code'] !== 200) {
            throw new Exception($this->_errmsg($response['code']));
        }

        return intval($response['body']['num']);
    }

    /**
     * Call remove_bulk.
     *
     * <pre>
     *   Store records at once.
     * </pre>
     *
     * @param  mixed $keys
     * @access public
     * @throws \Net\KyotoTycoon\Exception
     * @return mixed
     */
    public function removeBulk($keys)
    {
        $args = array('DB' => $this->_db);
        foreach ($keys as $k => $v) {
            $args["_$k"] = '';
        }

        $response = $this->_client->call('remove_bulk', $args);
        if ($response['code'] !== 200) {
            throw new Exception($this->_errmsg($response['code']));
        }

        return intval($response['body']['num']);
    }

    /**
     * Call get_bulk.
     *
     * <pre>
     *   Retrieve records at once.
     * </pre>
     *
     * @param  array $keys
     * @access public
     * @throws \Net\KyotoTycoon\Exception
     * @return mixed Response of RPC call
     */
    public function getBulk(array $keys)
    {
        $args = array('DB' => $this->_db);
        foreach ($keys as $k) {
            $args["_$k"] = '';
        }

        $response = $this->_client->call('get_bulk', $args);
        if ($response['code'] !== 200) {
            throw new Exception($this->_errmsg($response['code']));
        }
        $body = $response['body'];
        $ret  = array();
        foreach ($body as $k => $v) {
            if (preg_match('/^_(.+)$/', $k, $match)) {
                $ret[$match[1]] = $v;
            }
        }

        if (intval($body['num']) !== count($ret)) {
            throw new Exception('Fatal error');
        }

        return $ret;
    }

    /**
     * Call vacuum.
     *
     * <pre>
     *   Scan the database and eliminate regions of expired records.
     * </pre>
     *
     * @param  mixed $step
     * @access public
     * @return \Net\KyotoTycoon Fluent interface.
     */
    public function vacuum($step = null)
    {
        $args = array('DB' => $this->_db);
        if (!is_null($step)) {
            $args['step'] = $step;
        }
        $response = $this->_client->call('vacuum', $args);
        if ($response['code'] !== 200) {
            throw new Exception($this->_errmsg($response['code']));
        }

        return $this;
    }

    /**
     * Get last content.
     *
     * @access public
     * @return mixed Last content or null
     */
    public function getLastContent()
    {
        return $this->_client->getLastContent();
    }

    /**
     * Autoload class.
     *
     * @param  mixed $class
     * @access public
     * @return void
     */
    public static function autoload($className)
    {
        // Autoload class.
        // http://groups.google.com/group/php-standards/web/psr-0-final-proposal
        if (!class_exists($className, false)) {
            $className = ltrim($className, '\\');
            $fileName  = '';
            $namespace = '';
            if ($lastNsPos = strripos($className, '\\')) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

            require_once $fileName;
        }
    }
}
