<?php

require_once(dirname(__FILE__) . '/lib/verifylib.php');
require_once(dirname(__FILE__) . '/lib/shared.php');

try {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    if(!verify_ip_and_domain($ip_address, explode(",", ALLOWED_IPS), explode(",", ALLOWED_DOMAINS)))
        error_verify("Invalid request");
    
    $secret = $_POST["secret"];
    if (sha1($secret) != SECRET_HASH)
        error_verify("Invalid request");
    
    $dbh = get_database_handler();
    
    clean_old_or_duplicate_entries($dbh, "");

    $auth_token = $_POST["user_token"];
    $user_ip = $_POST["user_ip"];
    $entry = find_entry($dbh, $auth_token);
    if ($entry == NULL)
        error_verify("Unable to retrieve user token");
    
    if ($entry['ip_address'] != $_POST["user_ip"])
        error_verify("IP addresses do not match");
    
    if (!delete_entry($dbh, $auth_token))
        error_verify("Unable to delete entry");
    
    $result = query_ldap(LDAP_SERVER, LDAP_CN, $entry['uid']);
    $result = filter_query_result($result, explode(",", REQUESTED_DATA));
    echo json_encode($result);
} catch (Exception $e) {
    error_verify("Uncaught exception");
}