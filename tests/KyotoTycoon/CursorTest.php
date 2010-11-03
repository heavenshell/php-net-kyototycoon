<?php
/**
 * Spec of \Net\KyotoTycoon\Cursor.
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
 * @link      http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/
 */

namespace Net;
use Net;

/**
 * @see prepare
 */
require_once dirname(__DIR__) . '/prepare.php';

/**
 * @see \Net\KyotoTycoon
 */
require_once 'Net/KyotoTycoon.php';

/**
 * Misc test.
 *
 * @use       Net
 * @category  Net
 * @package   Net\KyotoTycoon
 * @version   $id$
 * @copyright (c) 2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 * @link      http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/
 */
class CursorTest extends \PHPUnit_Framework_TestCase
{
    private $_kt     = null;
    private $_cursor = null;
    private static $_port = null;
    public static function setUpBeforeClass()
    {
        require_once dirname(__DIR__) . '/Util.php';
        Net\KyotoTycoon\Test\Util::run();
        self::$_port = Net\KyotoTycoon\Test\Util::$port;
    }

    public static function tearDownAfterClass()
    {
        Net\KyotoTycoon\Test\Util::shutdown();
    }

    public function setUp()
    {
        $params    = array('port' => self::$_port);
        $this->_kt = new KyotoTycoon($params);
        $this->_cursor = $this->_kt->makeCursor(1);
    }

    public function testShouldReturnFalseWhenEmptyData()
    {
        $this->assertSame($this->_cursor->jump(), false);
    }

    public function testShouldReturnTrueWhenDataExists()
    {
        $this->_kt->setBulk(array('a' => 1, 'b' => '2', 'c' => 3));
        $this->assertSame($this->_cursor->jump('b'), true);
        $this->_kt->clear();
    }

    public function testShouldMoveCursor()
    {
        $this->_kt->setBulk(array('a' => 1, 'b' => '2', 'c' => 3));
        $this->assertSame($this->_cursor->jump('b'), true);
        $this->assertSame($this->_cursor->getKey(), 'b');
        $this->assertSame($this->_cursor->getValue(), '2');

        $this->_cursor->setValue('OK');
        $this->assertSame($this->_cursor->getValue(), 'OK');
        $this->assertSame($this->_cursor->get(1), array('b', 'OK'));
        list($k, $v) = $this->_cursor->get();
        $this->assertNotSame($k, 'b');
        $this->assertSame($this->_kt->get($k), '1');
        $this->_kt->clear();
    }

    public function testShouldRemoveCursor()
    {
        $this->_kt->setBulk(array('a' => 1, 'b' => '2', 'c' => 3));
        $this->assertSame($this->_cursor->jump('b'), true);
        $this->assertSame($this->_cursor->getKey(), 'b');
        list($k, $v) = $this->_cursor->get();
        $this->assertSame($this->_kt->get($k), '2');
        $this->assertSame($this->_cursor->remove(), true);
        $this->assertSame($this->_kt->get($k), null);
        $this->_kt->clear();
    }

    public function testShouldDeleteCursor()
    {
        $this->_kt->setBulk(array('a' => 1, 'b' => '2', 'c' => 3));
        $this->assertSame($this->_cursor->jump('b'), true);
        $this->assertSame($this->_cursor->getKey(), 'b');
        $this->_cursor->remove();
        $this->assertTrue(
            $this->_cursor->delete() instanceof KyotoTycoon\Cursor
        );
        $this->assertSame($this->_cursor->getKey(), null);
        $this->_kt->clear();
    }
}
