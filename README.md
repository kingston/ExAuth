ExAuth
======

ExAuth is a small PHP script to allow external websites to securely authenticate against Stanford's WebAuth.  At the moment, it is per-application, meaning only one application can use one instance, in part for security reasons.  This instance must be hosted within Stanford's subdomain.

Alternatives
------------
There are other ways to authenticate users via Stanford WebAuth in external web sites:
* Reverse Proxy - You can reverse proxy your site in the .htaccess and WebAuth-protect the Stanford instance AFAIK
* Host it on Stanford servers - Just host it on Stanford's own servers (or I believe technically any server within Stanford's network)
* Use a different/simpler script - You could probably write a script that forwards the info simply enough.  This version has been somewhat customized for extended usage.

Documentation
-------------
Coming... It's a work-in-progress.

Notes
-----

ExAuth is completely unaffiliated with Stanford University and licensed under the MIT License.  I haven't done a complete security audit of the code so can't guarantee anything, other than it has worked for me.