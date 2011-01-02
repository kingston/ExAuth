<?php
require_once(dirname(__FILE__) . '/simpletest/autorun.php');
    
class AllTests extends TestSuite {
    function AllTests() {
        parent::__construct();
        $this->addFile(dirname(__FILE__) . '/shared_test.php');
        $this->addFile(dirname(__FILE__) . '/auth_test.php');
        $this->addFile(dirname(__FILE__) . '/verify_test.php');
        $this->addFile(dirname(__FILE__) . '/functional_test.php');
    }
}
?>