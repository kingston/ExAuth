<?php

require_once(dirname(__FILE__) . '/lib/authlib.php');
require_once(dirname(__FILE__) . '/lib/shared.php');

try {
    $dbh = get_database_handler();
    $ip_address = $_SERVER['REMOTE_ADDR'];

    //Check referrer
    $referrer = $_SERVER['HTTP_REFERER'];
    if (!verify_referrer($referrer))
        error_redirect("Invalid referrer");

    clean_old_or_duplicate_entries($dbh, $ip_address);

    $uid = defined("TEST_USER") ? TEST_USER : $_SERVER["WEBAUTH_USER"];
    if (empty($uid))
        error_redirect("No user found");
    
    $token = generate_and_store_token($dbh, $ip_address, $uid);

    if (empty($token))
        error_redirect("Unable to generate token");
    
    header("Location: " . RETURN_URL . "?token=" . urlencode($token));
} catch (Exception $e) {
    error_redirect("Uncaught exception");
}