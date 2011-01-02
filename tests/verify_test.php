<?php
require_once(dirname(__FILE__) . '/simpletest/autorun.php');

require_once(dirname(__FILE__) . '/base_test.php');
require_once(dirname(__FILE__) . '/../auth/lib/verifylib.php');

class VerifyTest extends BaseTest {
    function testIpDomainVerification() {
        $valid_ips = array("12.12.12.12",
                                 "13.13.13.13",
                                 "14.14.14.14"
                                 );
        $valid_domains = array("localhost"
                                 );
        $good_ips = array("12.12.12.12",
                          "14.14.14.14",
                          "127.0.0.1"
                          );
        $bad_ips = array("1.1.1.1",
                         "2.2.2.2",
                         "3.3.3.3"
                         );
        foreach ($good_ips as $ip)
            $this->assertTrue(verify_ip_and_domain($ip, $valid_ips, $valid_domains));
            
        foreach ($bad_ips as $ip)
            $this->assertFalse(verify_ip_and_domain($ip, $valid_ips, $valid_domains));
    }

    function testDeleteToken() {
        $dbh = get_database_handler();
        insert_entry($dbh, "123", "1-1-1979 12:12:12", "1.1.1.1", "user1");
        insert_entry($dbh, "234", date("Y-m-d H:i:s"), "1.1.1.2", "user2");
        insert_entry($dbh, "345", date("Y-m-d H:i:s"), "1.1.1.3", "user3");
        $this->assertTrue(delete_entry($dbh, "123"));
        $this->assertTrue(delete_entry($dbh, "345"));
        $this->assertNull(find_entry($dbh, "123"));
        $this->assertNotNull(find_entry($dbh, "234"));
        $this->assertNull(find_entry($dbh, "345"));
    }
    
    function testInvalidLDAPQuery() {
        $result = query_ldap(LDAP_SERVER, LDAP_CN, "invalid_user");
        $this->assertNull($result);
    }
    
    function testValidLDAPQuery() {
        $result = query_ldap(LDAP_SERVER, LDAP_CN, TEST_USER);
        $this->assertNotNull($result);
        $this->assertEqual($result['uid'][0], TEST_USER);
    }
    
    function testFilterQuery() {
        $sample_data = array("attr1" => array("count" => 2, 0 => "value1", 1 => "value2"),
                             "attr2" => array("count" => 1, 0 => "value3"),
                             "attr3" => array("count" => 1, 0 => "value4"),
                             "attr4" => array("count" => 2, 0 => "value5", 1 => "value6"),
                             0 => "attr1",
                             1 => "attr2"
                            );
        $requested_attributes = array("attr4", "attr3");
        $data = filter_query_result($sample_data, $requested_attributes);
        $this->assertTrue(isset($data["attr3"][0]));
        $this->assertTrue(isset($data["attr4"][1]));
        $this->assertFalse(isset($data["attr4"]['count']));
        $this->assertFalse(isset($data["attr1"]));
        $this->assertFalse(isset($data["attr2"]));
    }
}