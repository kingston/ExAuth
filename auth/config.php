<?php

//Contains the configuration settings for ExAuth
//See the README file for more information

define("TIME_OUT", 60);

define("ALLOWED_REFERRERS", "http://www.example.com/auth"); //separate with commas

define("RETURN_URL", "http://www.example.com/verify");

define("ERROR_URL", "http://www.example.com/error");

define("SECRET_HASH", "e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4");

define("CHECK_VERIFY_SOURCE", true);

define("ALLOWED_IPS", "127.0.0.1"); //separate with commas

define("ALLOWED_DOMAINS", "");

define("REQUESTED_DATA", "uid,mail");

define("TEST_USER", ""); //You will need to fill this in with a valid SUNetID if running automated tests.  Otherwise, leave blank.

define("LDAP_SERVER", "ldap.stanford.edu"); //Don't need to change typically

define("LDAP_CN", "cn=people,dc=stanford,dc=edu"); //Don't need to change typically
