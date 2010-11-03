<?php
/**
 * Spec of \Net\KyotoTycoon.
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
 * Basic test.
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
class BasicTest extends \PHPUnit_Framework_TestCase
{
    private $_kt = null;
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
        $params = array('port' => self::$_port);
        $this->_kt = new KyotoTycoon($params);
    }

    public function testShouldSetPlainData()
    {
        $this->_kt->set('test', 'ok');
        $this->assertSame($this->_kt->get('test'), 'ok');
        $this->_kt->remove('test');
    }

    public function testShouldGetPlainData()
    {
        $this->_kt->set('test', 'ok');
        $this->assertSame($this->_kt->get('test'), 'ok');
        $this->_kt->remove('test');
    }

    public function testShouldReturnTrueRemoveData()
    {
        $this->_kt->set('test', 'ok');
        $this->assertSame($this->_kt->remove('test'), true);
    }

    public function testShouldReturnFalseWhenFailToRemoveData()
    {
        $this->_kt->remove('test');
        $this->assertSame($this->_kt->remove('test'), false);
    }

    public function testShouldReturnNullWhenDataNotSet()
    {
        $this->_kt->remove('test');
        $this->assertSame( $this->_kt->get('test'), null);
    }

    public function testShouldSetBinaryData()
    {
        $this->_kt->remove("te\x00st");
        $this->_kt->set("te\x00st", "o\x015\x00k");
        $this->assertSame($this->_kt->get("te\x00st"), "o\x015\x00k");
        $this->_kt->remove("te\x00st");
    }

    public function testShouldGetBinaryData()
    {
        $this->_kt->remove("te\x00st");
        $this->_kt->set("te\x00st", "o\x015\x00k");
        $this->assertSame($this->_kt->get("te\x00st"), "o\x015\x00k");
        $this->_kt->remove("te\x00st");
    }

    public function testShouldReturnTrueWhenRemoveBinaryData()
    {
        $this->_kt->remove("te\x00st");
        $this->_kt->set("te\x00st", "o\x015\x00k");
        $this->assertSame($this->_kt->remove("te\x00st"), true);
    }

    public function testShouldAddData()
    {
        $this->_kt->remove('add_t1');
        $this->assertSame($this->_kt->add('add_t1', 'ok'), true);
        $this->assertSame($this->_kt->get('add_t1'), 'ok');
        $this->_kt->remove('add_t1');
    }

    public function testShouldReturnFalseWhenDataAlreadyAdded()
    {
        $this->_kt->remove('add_t1');
        $this->_kt->add('add_t1', 'ok');
        $this->assertSame($this->_kt->add('add_t1', 'ng'), false);
        $this->assertSame($this->_kt->get('add_t1'), 'ok');
        $this->_kt->remove('add_t1');
    }

    public function testShouldReplaceData()
    {
        $this->_kt->remove('rep_t1');
        $this->_kt->set('rep_t1', 'ng');
        $this->assertSame($this->_kt->replace('rep_t1', 'ok'), true);
        $this->assertSame($this->_kt->get('rep_t1'), 'ok');
        $this->_kt->remove('rep_t1');
    }

    public function testShouldReturnFalseWhenReplaceableDataDataExist()
    {
        $this->_kt->remove('rep_t1');
        $this->assertSame($this->_kt->replace('rep_t1', 'ok'), false);
        $this->assertSame($this->_kt->get('rep_t1'), null);
    }

    public function testShouldAppendData()
    {
        $this->_kt->remove('app_t1');
        $this->_kt->append('app_t1', 'o1');
        $this->assertSame($this->_kt->get('app_t1'), 'o1');
        $this->_kt->append('app_t1', 'o2');
        $this->assertSame($this->_kt->get('app_t1'), 'o1o2');
        $this->_kt->remove('app_t1');
    }

    public function testShouldIncrementData()
    {
        $this->_kt->remove('inc_t1');
        $this->assertSame($this->_kt->increment('inc_t1', '25'), 25);
        $this->assertSame($this->_kt->increment('inc_t1', '11'), 36);
        $this->_kt->remove('inc_t1');
    }

    public function testShouldIncrementDoubleData()
    {
        $this->_kt->remove('inc_t2');
        $this->assertSame($this->_kt->incrementDouble('inc_t2', '2.5'), 2.5);
        $this->assertSame($this->_kt->incrementDouble('inc_t2', '1.1'), 3.6);
        $this->_kt->remove('inc_t2');
    }

    public function testShouldComplareAndSwapData()
    {
        $this->_kt->remove('cas_t1');
        $this->assertSame($this->_kt->cas('cas_t1', 'a', 'b'), false);
        $this->assertSame($this->_kt->cas('cas_t1', null, 'b'), true);
        $this->assertSame($this->_kt->cas('cas_t1', 'a', 'b'), false);
        $this->assertSame($this->_kt->cas('cas_t1', null, 'c'), false);
        $this->assertSame($this->_kt->cas('cas_t1', 'b', 'c'), true);
        $this->assertSame($this->_kt->cas('cas_t1', 'b', null), false);
        $this->assertSame($this->_kt->cas('cas_t1', 'c', null), true);
        $this->assertSame($this->_kt->get('cas_t1'), null);
        $this->_kt->remove('cas_t1');
    }

    public function testShouldSetBulkData()
    {
        $this->_kt->clear();
        $data = array('a' => 1, 'b' => 2, 'c' => 3);
        $this->assertSame($this->_kt->setBulk($data), 3);
        foreach ($data as $k => $v) {
            $this->assertSame($this->_kt->get($k), strval($v));
        }
        $this->_kt->removeBulk(array(1, 2, 3));
    }

    public function testShouldGetBulkData()
    {
        $this->_kt->clear();
        $data = array('a' => 1, 'b' => 2, 'c' => 3);
        $this->assertSame($this->_kt->setBulk($data), 3);
        $this->assertSame(
            $this->_kt->getBulk(array('a', 'b', 'c')),
            array('a' => '1', 'b' => '2', 'c' => '3')
        );
        $this->_kt->removeBulk($data);
    }

    public function testShouldRemoveBulkData()
    {
        $this->_kt->clear();
        $data = array('a' => 1, 'b' => 2, 'c' => 3);
        $this->assertSame($this->_kt->setBulk($data), 3);
        $this->_kt->removeBulk($data);
        $this->assertSame($this->_kt->getBulk(array('a', 'b', 'c')), array());
    }
}
