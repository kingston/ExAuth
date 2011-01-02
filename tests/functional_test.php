<?php
require_once(dirname(__FILE__) . '/simpletest/autorun.php');

require_once(dirname(__FILE__) . '/base_test.php');
require_once(dirname(__FILE__) . '/../auth/lib/shared.php');

class AuthFunctionalTest extends BaseWebTest{
    function testValidAuth() {
        $this->setMaximumRedirects(0);
        $this->get($this->authUrl());
        $this->assertResponse(302);
        $this->assertHeader("Location", new PatternExpectation("/" . preg_quote(RETURN_URL, "/") . "/"));
        $this->assertHeader("Location", new PatternExpectation("/token=[a-f0-9]{32}/"));
    }
    
    function testValidAuthAndVerify() {
        $params = $this->prepareAuth();
        $page = $this->post($this->verifyUrl(), $params);
        $this->assertTrue($page);
        $result = json_decode($page);
        $this->assertEqual($result->uid[0], TEST_USER);
        $this->assertEqual($result->mail[0], TEST_USER . "@stanford.edu");
    }
    
    function testBadTokenAuthAndVerify() {
        $params = $this->prepareAuth();
        $params['user_token'] = "badtoken";
        $page = $this->post($this->verifyUrl(), $params);
        $this->assertTrue($page);
        $result = json_decode($page);
        $this->assertEqual($result->error, 1);
    }
    
    function testBadSecretAuthAndVerify() {
        $params = $this->prepareAuth();
        $params['secret'] = "invalid";
        $page = $this->post($this->verifyUrl(), $params);
        $this->assertTrue($page);
        $result = json_decode($page);
        $this->assertEqual($result->error, 1);
    }
    
    function testBadIPAuthAndVerify() {
        $params = $this->prepareAuth();
        $params['user_ip'] = "1.1.1.1";
        $page = $this->post($this->verifyUrl(), $params);
        $this->assertTrue($page);
        $result = json_decode($page);
        $this->assertEqual($result->error, 1);
    }
    
    protected function prepareAuth() {
        $this->setMaximumRedirects(0);
        $this->get($this->authUrl());
        preg_match("/token=([a-f0-9]{32})/", $this->getBrowser()->getHeaders(), $tokenMatches, PREG_OFFSET_CAPTURE);
        $this->assertTrue(count($tokenMatches[1]) > 0);
        $token = $tokenMatches[1][0];
        return array("user_token" => $token, "user_ip" => "127.0.0.1", secret => "secret");
    }
}