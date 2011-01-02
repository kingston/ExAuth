<?php

require_once(dirname(__FILE__) . '/shared.php');

/**
 * Verifies the IP against a list of valid IPs and domain names (that are looked up)
 * @param string $ip            The IP address to verify
 * @param array  $valid_ips     An array of allowed IP addresses
 * @param array  $valid_domains An array of valid domain names
 * @return boolean True if the IP is valid, otherwise false
 */
function verify_ip_and_domain($ip, $valid_ips, $valid_domains) {
    if (in_array($ip, $valid_ips))
        return true;
    
    //TODO: Should log/alert someone that IP address is no longer valid
    
    foreach ($valid_domains as $valid_domain) {
        if ($ip == gethostbyname($valid_domain))
            return true;
    }
    return false;
}

/**
 * Deletes an entry with the specified token
 * @param PDO    $dbh   The database handler
 * @param string $token The token of the entry
 * @return object False if unsuccessful
 */
function delete_entry($dbh, $token) {
    $sql = "DELETE FROM auth_tokens WHERE auth_token = ?";
    $sth = $dbh->prepare($sql);
    return $sth->execute(array($token));
}

/**
 * Queries the LDAP database for a specific uid
 * @param PDO    $ldap_server The name of the server to query
 * @param string $ldap_cn     The path to search
 * @param string $uid         The uid of the user
 * @return array The result of the query, otherwise null
 */
function query_ldap($ldap_server, $ldap_cn, $uid) {
    $conn = ldap_connect($ldap_server);
    if (!$conn) return null;
    if (!ldap_bind($conn)) return null;
    $result = ldap_search($conn, $ldap_cn, "(uid=" . $uid . ")");
    if (!result) return null;
    $info = ldap_get_entries($conn, $result);
    if ($info['count'] == 0) return null;
    ldap_close($conn);
    return $info[0];
}

/**
 * Filters the result, getting only the requested items
 * @param array $result          The result of the LDAP query
 * @param array $requested_items An array of the requested keys
 * @return array An array of the requested items
 */
function filter_query_result($result, $requested_keys) {
    $data = array();
    foreach ($requested_keys as $key) {
        $data[$key] = $result[$key];
        unset($data[$key]['count']);
    }
    return $data;
}

/**
 * Returns an error message JSON formated and stops processing the request
 * @param string $message The message of the error
 */
function error_verify($message) {
    echo json_encode(array("error" => 1, "message" => $message));
    exit();
}
