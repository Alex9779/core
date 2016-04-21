<?php
/**
 *    Copyright (C) 2016 Deciso B.V.
 *
 *    All rights reserved.
 *
 *    Redistribution and use in source and binary forms, with or without
 *    modification, are permitted provided that the following conditions are met:
 *
 *    1. Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *
 *    2. Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 *    THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
 *    INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 *    AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *    AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 *    OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 *    SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 *    INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 *    CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 *    ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 *    POSSIBILITY OF SUCH DAMAGE.
 *
 */

namespace tests\OPNsense\Base\FieldTypes;

// @CodingStandardsIgnoreStart
require_once 'Field_Framework_TestCase.php';
// @CodingStandardsIgnoreEnd

use \OPNsense\Base\FieldTypes\AuthenticationServerField;
use \Phalcon\DI\FactoryDefault;
use OPNsense\Core\Config;

class AuthenticationServerFieldTest extends Field_Framework_TestCase
{

    /**
     * test construct
     */
    public function testCanBeCreated()
    {
        $this->assertInstanceOf('\OPNsense\Base\FieldTypes\AuthenticationServerField', new AuthenticationServerField());
        // switch config to test set for this type
        FactoryDefault::getDefault()->get('config')->globals->config_path = __DIR__ .'/AuthenticationServerFieldTest/';
        Config::getInstance()->forceReload();
    }

    /**
     * Local database should always be there
     * @depends testCanBeCreated
     */
    public function testLocalExists()
    {
        // init field
        $field = new AuthenticationServerField();
        $field->eventPostLoading();

        $this->assertContains('Local Database', array_keys($field->getNodeData()));
    }

    /**
     *
     * @depends testCanBeCreated
     */
    public function testConfigItemsExists()
    {
        // init field
        $field = new AuthenticationServerField();
        $field->eventPostLoading();

        $this->assertContains('testcase 1', array_keys($field->getNodeData()));
        $this->assertContains('testcase 2', array_keys($field->getNodeData()));
    }

    /**
     * @depends testCanBeCreated
     * @expectedException \Phalcon\Validation\Exception
     * @expectedExceptionMessage CsvListValidator
     */
    public function testSelectSetWithUnknownValue()
    {
        // init field
        $field = new AuthenticationServerField();
        $field->eventPostLoading();
        $field->setMultiple("Y");
        $field->setValue('testcase 1,testcase 2,testcase X');
        $this->validateThrow($field);
    }

    /**
     *
     * @depends testCanBeCreated
     */
    public function testSelectSetWithoutUnknownValue()
    {
        // init field
        $field = new AuthenticationServerField();
        $field->eventPostLoading();
        $field->setMultiple("Y");
        $field->setValue('testcase 1,testcase 2,Local Database');
        $this->assertEmpty($this->validate($field));
    }

    /**
     * @depends testCanBeCreated
     * @expectedException \Phalcon\Validation\Exception
     * @expectedExceptionMessage InclusionIn
     */
    public function testSelectSetOnSingleValue()
    {
        // init field
        $field = new AuthenticationServerField();
        $field->eventPostLoading();
        $field->setMultiple("N");
        $field->setValue('testcase 1,testcase 2,Local Database');
        $this->validateThrow($field);
    }

    /**
     * @depends testCanBeCreated
     */
    public function testSelectSingleValue()
    {
        // init field
        $field = new AuthenticationServerField();
        $field->eventPostLoading();
        $field->setMultiple("N");
        $field->setValue('testcase 1');
        $this->assertEmpty($this->validate($field));
    }


    /**
     * type is not a container
     */
    public function testIsContainer()
    {
        $field = new AuthenticationServerField();
        $this->assertFalse($field->isContainer());
    }
}