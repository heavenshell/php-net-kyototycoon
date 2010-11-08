<?php
/**
 * Start ktserver.
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
 * @category  Net
 * @package   Net\KyotoTycoon
 * @version   $id$
 * @copyright (c) 2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
namespace Net\KyotoTycoon\Test;

/**
 * Util
 *
 * @category  Net
 * @package   Net\KyotoTycoon
 * @version   $id$
 * @copyright (c) 2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Util
{
    /**
     * Pid.
     */
    private static $_pid = null;

    /**
     * KyotoTycoon port.
     */
    public static $port = null;

    /**
     * Run ktserver.
     *
     * @access public
     * @return int Pid
     */
    public static function run($option = null)
    {
        $port = 10000 + rand(0, 1000);
        while ($port < 20000) {
            if (self::checkPort($port) === false) {
                break;
            }
            $port ++;
        }
        $ktserver = exec('which ktserver');
        if ($ktserver === '') {
            throw new \Exception('This test requires "ktserver".');
        }
        if (is_null($option)) {
            $command = sprintf(
                '%s -port %s > /dev/null 2>&1 & echo $!',
                $ktserver, $port
            );
        } else {
            $command = sprintf(
                '%s -port %s %s > /dev/null 2>&1 & echo $!',
                $ktserver, $port, $option
            );
        }
        exec($command, $op);
        $pid = intval($op[0]);
        self::$_pid = $pid;
        self::$port = $port;

        return $pid;
    }

    /**
     * Get status running.
     *
     * @access public
     * @return bool true:Process running, false:Process stop
     */
    public static function status()
    {
        if (self::$_pid === null) {
            return false;
        }

        $command = 'ps -p ' . self::$_pid;
        exec($command, $op);
        if (!isset($op[1])) {
            return false;
        }

        return true;
    }

    /**
     * Shutdown process.
     *
     * @access public
     * @return bool true:Shutdown success, false:Shutdown fail
     */
    public static function shutdown()
    {
        if (self::$_pid === null) {
            return;
        }

        if (self::status() === false) {
            return;
        }
        $command = 'kill -9 ' . self::$_pid;
        exec($command);
        if (self::status() == false) {
            return true;
        }

        return false;
    }

    /**
     * checkPort
     *
     * @param  mixed $port Port number
     * @access public
     * @return bool true:Port availble, Port already used.
     */
    public static function checkPort($port)
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $remote = @socket_connect($socket, '127.0.0.1', $port);
        socket_close($socket);

        return $remote;
    }
}
