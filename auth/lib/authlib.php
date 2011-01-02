<?php

require_once(dirname(__FILE__) . '/shared.php');

function verify_referrer($referrer, $allowed_referrers = null) {
    if (empty($referrer))
        return true;
    if ($allowed_referrers == null)
        $allowed_referrers = explode(",", ALLOWED_REFERRERS);
    foreach ($allowed_referrers as $allowed_referrer) {
        if (preg_match("/^" . preg_quote($allowed_referrer, "/") . "/", $referrer))
            return true;
    }
    return false;
}

function generate_and_store_token($dbh, $ip_address, $uid) {
    $token = md5(uniqid(SECRET_HASH, true));
    if (!insert_entry($dbh, $token, date("Y-m-d H:i:s"), $ip_address, $uid))
        return null;
    return $token;
}

function error_redirect($message) {
    header("Location: " . ERROR_URL . "?message=" . urlencode($message));
    die;
}