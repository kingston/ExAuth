<?php

//Contains shared code
require_once(dirname(__FILE__) . '/../config.php');
/**
 * Gets the location of the database
 * @return string The path to the database
 */
function get_database_location() {
    return dirname(__FILE__) . '/../db/data.db';
}

/**
 * Gets a handler to the database and initializes it if needed 
 *
 * @return PDO $dbh The database handler
 */
function get_database_handler() {
    $db_path = get_database_location();
    $db_inited = file_exists($db_path);
    
    $dbh = new PDO("sqlite:" . $db_path);
    
    if (!$db_inited)
        initialize_database($dbh);

    return $dbh;
}

/**
 * Creates the tables in the database 
 *
 * @param PDO $dbh The database handler
 */
function initialize_database($dbh) {
    $table = "CREATE TABLE auth_tokens ( auth_token VARCHAR(256) NOT NULL PRIMARY KEY,
    authentication_time DATETIME NOT NULL,
    ip_address VARCHAR(128) NOT NULL,
    uid VARCHAR(256) NOT NULL
    );";
    $dbh->exec($table);
}

/**
 * Inserts an entry into the database
 * @param PDO    $dbh        The database handler
 * @param string $token      The token of the entry
 * @param string $time       The token entry time
 * @param string $ip_address The IP address associated with the token
 * @param string $uid        The SUNet ID of the authenticated user
 * @return object The result of the operation (false if failed)
 */
function insert_entry($dbh, $token, $time, $ip_address, $uid) {
    $sql = "INSERT INTO auth_tokens(auth_token, authentication_time, ip_address, uid) VALUES (:authtoken, :authtime, :authip, :uid)";
    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $args = array(':authtoken' => $token, ':authtime' => $time, ':authip' => $ip_address, ":uid" => $uid);
    return $sth->execute($args);
}

/**
 * Finds an entry in the database with the given token
 * @param PDO    $dbh   The database handler
 * @param string $token The token of the entry to search for
 * @return array The token entry if found, otherwise NULL
 */
function find_entry($dbh, $token) {
    $sql = "SELECT auth_token, authentication_time, ip_address, uid FROM auth_tokens WHERE auth_token = ?";
    $sth = $dbh->prepare($sql);
    $result = $sth->execute(array($token));
    if ($result == false)
        return null;
    $obj = $sth->fetch(PDO::FETCH_ASSOC);
    return $obj == false ? null : $obj;
}

/**
 * Deletes timed-out entries and entries that have the same IP address
 * @param PDO    $dbh        The database handler
 * @param string $ip_address The IP address to look for (can leave blank)
 * @return object False if unsuccessful
 */
function clean_old_or_duplicate_entries($dbh, $ip_address) {
    $sql = "DELETE FROM auth_tokens WHERE authentication_time < ? OR ip_address = ?";
    $sth = $dbh->prepare($sql);
    $time = date("Y-m-d H:i:s", time() - TIME_OUT);
    return $sth->execute(array($time, $ip_address));
}
