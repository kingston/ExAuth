ExAuth
======

ExAuth is a small PHP script to allow external websites to securely authenticate against Stanford's WebAuth.  At the moment, it is per-application, meaning only one application can use one instance, in part for security reasons.  This instance must be hosted within Stanford's subdomain.

Alternatives
------------
There are other ways to authenticate users via Stanford WebAuth in external web sites:
* Reverse Proxy - You can reverse proxy your site in the .htaccess and WebAuth-protect the Stanford instance AFAIK
* Host it on Stanford servers - Just host it on Stanford's own servers (or I believe technically any server within Stanford's network)
* Use a different/simpler script - You could probably write a script that forwards the info simply enough.  This version has been somewhat customized for extended usage.

Requirements
------------
* Web space under the stanford.edu subdomain
* A webserver that has the PHP LDAP module enabled (Stanford servers have this enabled)

Installation
-------------
1. Go through config.php, customizing it for your application
2. Copy the auth folder to a CGI-enabled web-space you own in the stanford.edu domain
3. Secure your web-space as described here: https://itservices.stanford.edu/serivce/web/centralhosting/webauth/unix
4. Set up your own external website to interface with ExAuth

Configuration
-------------
Found in config.php:

* $time_out: The duration (in seconds) before the entry expires
* $allowed_referrers: An array of referrers that are allowed to access the auth page
* $return_url: The URL to return the user when authenticated
* $error_url: The URL to send the user if an error occurs authenticating
* $secret_hash: The SHA1 hash of the secret
* $check_verify_source: True to check IP/domain of accessors of verify, false to not check
* $allowed_ips: An array of IPs that are allowed to access the verify.php script
* $allowed_domains: An array of domain names that are allowed to access the verify.php script
* $requested_data: An array of LDAP attributes to be pulled about the user (leave blank for just the email)

Process
-------
A behind-the-scenespeek at what's going on:

/auth.php (GET from user)
1. Verifies referrer is from allowed app (if set)
2. Deletes any database entries with timed-out entries and tokens with the same IP
3. Stores a random token in the database along with IP, time of entry, and email
4. Forwards the request back to the original site's verification URL with the random token (verify_url?token=<token>)

If auth.php encounters an error, it will forward the user to <error_url>?message=<error_message>

/verify.php (POST { user_token => <token>, user_ip => <user_ip>, secret => <secret> } from site's server)

1. Verifies the IP requestor is from a valid set of IPs
2. Verifies the SHA(secret) is valid
3. Deletes any database entries with timed-out entries
4. Retrieves the token from the database
5. Checks the IP address of the requestor matches
6. Deletes the entry from the database
7. Queries the LDAP server for the requested data
8. Responds with JSON content of requested data in the form, e.g. { uid => { 0 => <uid> }, mail => { 0 => <mail>@stanford.edu } }

If verify.php encounters an error, it will return { error => 1, message => <error message> } in JSON format.

TODO
----

* Log errors, etc. to a flatfile

Feedback
--------
Any feedback and suggestions are very welcome at web <at> kingstontam.com.

Notes
-----

ExAuth is completely unaffiliated with Stanford University and licensed under the MIT License.  I haven't done a complete security audit of the code so can't guarantee anything, other than it has worked for me.

If you wish to run the automated tests, you must fill in the TEST_USER setting with a valid SUNetID.
