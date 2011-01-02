<?php
require_once(dirname(__FILE__) . '/simpletest/autorun.php');
require_once(dirname(__FILE__) . '/simpletest/web_tester.php');

require_once(dirname(__FILE__) . '/../auth/lib/shared.php');

class BaseTest extends UnitTestCase {
    function setUp() {
        @unlink(get_database_location());
    }
    
    function tearDown() {
        @unlink(get_database_location());
    }
}

class BaseWebTest extends WebTestCase {
    protected $testurl = "http://127.0.0.1:8080/auth/";
    
    function authUrl() {
        return $this->testurl . "auth.php";
    }
    
    function verifyUrl() {
        return $this->testurl . "verify.php";
    }

    function setUp() {
        @unlink(get_database_location());
    }
    
    function tearDown() {
        @unlink(get_database_location());
    }
}