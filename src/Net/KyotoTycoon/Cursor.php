<?php
/**
 * Cursor class for KyotoTycoon.
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
 * @use       Net\KyotoTycoon
 * @category  Net
 * @package   Net\KyotoTycoon
 * @version   $id$
 * @copyright (c) 2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 * @link      http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/
 */

namespace Net\KyotoTycoon;
use Net\KyotoTycoon;

/**
 * Cursor class for KyotoTycoon.
 *
 * <pre>
 *   This module is port from Perl Cache::KyotoTycoon.
 *   See also @link.
 * </pre>
 *
 * @use       Net\KyotoTycoon
 * @category  Net
 * @package   Net\KyotoTycoon
 * @version   $id$
 * @copyright (c) 2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 * @link      http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/

 */
class Cursor
{
    /**
     * Db.
     *
     * @var    mixed
     * @access private
     */
    private $_db = null;

    /**
     * Cursor.
     *
     * @var    mixed
     * @access private
     */
    private $_cursor = null;

    /**
     * Client.
     *
     * @var    mixed
     * @access private
     */
    private $_client = null;

    /**
     * Status Code.
     *
     * @var    mixed
     * @access private
     */
    private $_statusCode = null;

    /**
     * Construct.
     *
     * @param  mixed $id
     * @param  mixed $db
     * @param  mixed $client
     * @access public
     * @return void
     */
    public function __construct($id, $db, $client)
    {
        $this->_db     = $db;
        $this->_client = $client;
        $this->_cursor = $id;

        // Status code.
        $this->_statusCode = new StatusCode();
    }

    /**
     * Get error message.
     *
     * @param  mixed $code Status code
     * @access private
     * @return String Error message
     */
    private function _errmsg($code)
    {
        return $this->_statusCode->errmsg($code);
    }

    /**
     * Call jump.
     *
     * <pre>
     *   Jump the cursor to the first record for forward scan.
     * </pre>
     *
     * @param  mixed $key
     * @access public
     * @return mixed
     */
    public function jump($key = null)
    {
        $args = array('DB' => $this->_db, 'CUR' => $this->_cursor);
        if (!is_null($key)) {
            $args['key'] = $key;
        }
        $response = $this->_client->call('cur_jump', $args);

        if ($response['code'] === 200) {
            return true;
        } else if ($response['code'] === 450) {
            return false;
        }

        return $this->_errmsg($response['code']);
    }

    /**
     * Call jump_back.
     *
     * <pre>
     *   Jump the cursor to the last record for backward scan.
     * </pre>
     *
     * @param  mixed $key
     * @access public
     * @return mixed
     */
    public function jumpBack($key = null)
    {
        $args = array('DB' => $this->_db, 'CUR' => $this->_cursor);
        if (!is_null($key)) {
            $args['key'] = $key;
        }
        $response = $this->_client->call('cur_jump_back', $args);

        if ($response['code'] === 200) {
            return true;
        } else if ($response['code'] === 450) {
            return false;
        }

        return $this->_errmsg($response['code']);
    }

    /**
     * Call step.
     *
     * <pre>
     *   Step the cursor to the next record.
     * </pre>
     *
     * @access public
     * @return mixed
     */
    public function step()
    {
        $args     = array('CUR' => $this->_cursor);
        $response = $this->_client->call('cur_step', $args);

        if ($response['code'] === 200) {
            return true;
        } else if ($response['code'] === 450) {
            return false;
        }

        return $this->_errmsg($response['code']);
    }

    /**
     * Call step_back.
     *
     * <pre>
     *   Step the cursor to the previous record.
     * </pre>
     *
     * @access public
     * @return mixed
     */
    public function stepBack()
    {
        $args     = array('CUR' => $this->_cursor);
        $response = array('code' => 0, 'body' => null);
        $response = $this->_client->call('cur_step_back', $args);

        if ($response['code'] === 200) {
            return true;
        } else if ($response['code'] === 450) {
            return false;
        }

        return $this->_errmsg($response['code']);
    }

    /**
     * Call set_value.
     *
     * <pre>
     *   Set the value of the current record.
     * </pre>
     *
     * @param  mixed $value
     * @param  mixed $xt
     * @param  mixed $step
     * @access public
     * @return mixed
     */
    public function setValue($value, $xt = null, $step = null)
    {
        $args = array('CUR' => $this->_cursor, 'value' => $value);
        if (!is_null($xt)) {
            $args['xt'] = $xt;
        }
        if (!is_null($step)) {
            $args['step'] = '';
        }

        $response = $this->_client->call('cur_set_value', $args);
        if ($response['code'] === 200) {
            return true;
        } else if ($response['code'] === 450) {
            return false;
        }

        return $this->_errmsg($response['code']);
    }

    /**
     * Call remove.
     *
     * <pre>
     *   Remove the current record.
     * </pre>
     *
     * @access public
     * @return mixed
     */
    public function remove()
    {
        $args     = array('CUR' => $this->_cursor);
        $response = $this->_client->call('cur_remove', $args);
        if ($response['code'] === 200) {
            return true;
        } else if ($response['code'] === 450) {
            return false;
        }

        return $this->_errmsg($response['code']);
    }

    /**
     * Call get_key.
     *
     * <pre>
     *   Get the key of the current record.
     * </pre>
     *
     * @param  mixed $step
     * @access public
     * @return mixed
     */
    public function getKey($step = null)
    {
        $args = array('CUR' => $this->_cursor);
        if (!is_null($step)) {
            $args['step'] = '';
        }
        $response = $this->_client->call('cur_get_key', $args);

        if ($response['code'] === 200) {
            return $response['body']['key'];
        } else if ($response['code'] === 450) {
            return null;
        }

        return $this->_errmsg($response['code']);
    }

    /**
     * Call get_value.
     *
     * <pre>
     *   Get the value of the current record.
     * </pre>
     *
     * @param  mixed $step
     * @access public
     * @return mixed
     */
    public function getValue($step = null)
    {
        $args = array('CUR' => $this->_cursor);
        if (!is_null($step)) {
            $args['step'] = '';
        }
        $response = $this->_client->call('cur_get_value', $args);

        if ($response['code'] === 200) {
            return $response['body']['value'];
        } else if ($response['code'] === 450) {
            return null;
        }

        return $this->_errmsg($response['code']);
    }

    /**
     * Call get.
     *
     * <pre>
     *   Get a pair of the key and the value of the current record.
     * </pre>
     *
     * @param  mixed $step
     * @access public
     * @return mixed
     */
    public function get($step = null)
    {
        $args = array('CUR' => $this->_cursor);
        if (!is_null($step)) {
            $args['step'] = '';
        }

        $response = $this->_client->call('cur_get', $args);
        if ($response['code'] === 200) {
            return array($response['body']['key'], $response['body']['value']);
        } else if ($response['code'] === 450) {
            return null;
        }

        return $this->_errmsg($response['code']);
    }

    /**
     * Call delete.
     *
     * <pre>
     *   Delete the cursor immidiately.
     * </pre>
     *
     * @access public
     * @return mixed Fluent interface or error message.
     */
    public function delete()
    {
        $args     = array('CUR' => $this->_cursor);
        $response = $this->_client->call('cur_delete', $args);
        if ($response['code'] === 200) {
            return $this;
        }

        return $this->_errmsg($response['code']);
    }
}
