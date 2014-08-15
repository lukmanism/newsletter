## Hosting provider doesn't allow loop-back connections

From Issue #31 JayB83 found that his hosting company didn't block fsockopen, but blocked http access through it.

He was able to work around this by editing the file '\classes\Pommo_Mail_Ctl.php' on line (about) 100 from:

OLD: $socket = fsockopen($ssl.Pommo::$_hostname, Pommo::$_hostport, $errno, $errstr, 25);

to

NEW: $socket = fsockopen('localhost', Pommo::$_hostport, $errno, $errstr, 25);

## Unable to log-in after upgrade

If you have upgraded from an older version of poMMo and you are unable to log-in, you can use the script upgrade.sql in the sql folder of your poMMo installation. Open the file before running the sql statements because there are some modifications that need to be made to the script before you can run it.

## Call to undefined function _()

This happens when you don't have gettext installed in your system. Make sure you have gettext and php-gettext installed.
