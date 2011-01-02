<?php
require_once(dirname(__FILE__) . '/simpletest/autorun.php');

require_once(dirname(__FILE__) . '/base_test.php');

class SharedTest extends BaseTest {
    function testDatabaseOperations() {
        $dbh = get_database_handler();
        $this->assertTrue(insert_entry($dbh, "234", "12-12-1999 12:12:12", "1.1.1.2", "user1"));
        $this->assertTrue(insert_entry($dbh, "123", "12-12-1999 12:12:12", "1.1.1.1", "user2"));
        $this->assertTrue(insert_entry($dbh, "345", "12-12-1999 12:12:12", "1.1.1.3", "user3"));
        $entry = find_entry($dbh, "123");
        $this->assertTrue(!empty($entry));
        $this->assertEqual($entry['auth_token'], "123");
        $this->assertEqual($entry['authentication_time'], "12-12-1999 12:12:12");
        $this->assertEqual($entry['ip_address'], "1.1.1.1");
        $this->assertEqual($entry['uid'], "user2");
    }

    function testCleanOldEntries() {
        $dbh = get_database_handler();
        insert_entry($dbh, "123", "1-1-1979 12:12:12", "1.1.1.1", "user1");
        insert_entry($dbh, "234", date("Y-m-d H:i:s", time() - 1), "1.1.1.2", "user2");
        insert_entry($dbh, "345", date("Y-m-d H:i:s"), "1.1.1.3", "user3");
        $this->assertTrue(clean_old_or_duplicate_entries($dbh, "1.1.1.3"));
        $this->assertNull(find_entry($dbh, "123"));
        $this->assertNotNull(find_entry($dbh, "234"));
        $this->assertNull(find_entry($dbh, "345"));
    }
    
    function testDataShouldPersist() {
        $dbh = get_database_handler();
        insert_entry($dbh, "123", "12-12-1999 12:12:12", "1.1.1.1", "user1");
        $dbh2 = get_database_handler();
        $entry = find_entry($dbh2, "123");
        $this->assertTrue(!empty($entry));
    }
    
    function testUniqueAuthToken() {
        $dbh = get_database_handler();
        $this->assertTrue(insert_entry($dbh, "123", "12-12-1999 12:12:12", "1.1.1.1", "user1"));
        $this->assertFalse(insert_entry($dbh, "123", "12-12-1999 12:12:13", "1.1.1.2", "user2"));
    }
}