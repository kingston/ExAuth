<?php
require_once(dirname(__FILE__) . '/simpletest/autorun.php');

require_once(dirname(__FILE__) . '/base_test.php');
require_once(dirname(__FILE__) . '/../auth/lib/authlib.php');

class AuthTest extends BaseTest {
    function testReferrerVerification() {
        $valid_referrers = array("http://www.good.com/auth",
                                 "http://www.alsogood.com/auth",
                                 "www.goodie.com/foo"
                                 );
        $good_referrers = array("http://www.good.com/auth",
                                "http://www.good.com/auth?foo",
                                "www.goodie.com/foo?aoaoa"
                                );
        $bad_referrers = array("www.good.com/auth",
                               "http://www.bad.com/auth?foo",
                               "www.goodie.com/fo"
                               );
        foreach ($good_referrers as $referrer)
            $this->assertTrue(verify_referrer($referrer, $valid_referrers));
            
        foreach ($bad_referrers as $referrer)
            $this->assertFalse(verify_referrer($referrer, $valid_referrers));
    }
    
    function testGenerateAndStoreToken() {
        $dbh = get_database_handler();
        $token1 = generate_and_store_token($dbh, "1.1.1.1", "user1");
        $token2 = generate_and_store_token($dbh, "1.1.1.2", "user2");
        $entry1 = find_entry($dbh, $token1);
        $entry2 = find_entry($dbh, $token2);
        $this->assertNotNull($entry1);
        $this->assertNotNull($entry2);
        //Test we're within a reasonable time
        $this->assertTrue(time() - strtotime($entry1['authentication_time']) < 1000);
        $this->assertTrue(time() - strtotime($entry2['authentication_time']) < 1000);
        $this->assertEqual($entry1['ip_address'], "1.1.1.1");
        $this->assertEqual($entry2['ip_address'], "1.1.1.2");
        $this->assertEqual($entry1['uid'], "user1");
        $this->assertEqual($entry2['uid'], "user2");
    }
}