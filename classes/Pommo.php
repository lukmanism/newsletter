<?php
/**
 * Copyright (C) 2005, 2006, 2007, 2008  Brice Burgess <bhb@iceburg.net>
 *
 * This file is part of poMMo (http://www.pommo.org)
 *
 * poMMo is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published
 * by the Free Software Foundation; either version 2, or any later version.
 *
 * poMMo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
 * the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with program; see the file docs/LICENSE. If not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
 * MA 02110-1301 USA.
 */

/*	Common class.
 *	Holds Configuration values, authentication state, etc (revived from session)
 */

class Pommo
{
    public static $_baseDir;	// (e.g. /home/www/site1/pommo/)
    public static $_baseUrl;	// (e.g. http://s.com/pommo/) - null = autodetect
    public static $_config;	// Array to hold values loaded from the DB
    public static $_auth;	// holds the authentication object
    public static $_escaping;	// if true, responses from logger and translation
                                // functions will use htmlspecialchars
    public static $_logger;	// holds the logger (messaging) object
    public static $_workDir;	// Writable dir (/home/www/site/pommo/cache/)
    public static $_debug;	// debug status (bool)
    public static $_default_subscriber_sort;	// what column (or field_id)
                // should be used to sort the subscribers when managing them?
    public static $_verbosity;	// log + debug verbosity (1(most)-3(less|default))
    public static $_dateformat;	// 1: YYYY/MM/DD, 2: MM/DD/YYYY, 3: DD/MM/YYYY
    public static $_hasConfigFile;	// Tells us if config.php exists
    public static $_hostname;	// (e.g. www.site1.com) - null = autodetect
    public static $_hostport; 	// (e.g. 80) - null = autodetect
    public static $_ssl;	// bool - true if accessed via HTTPS
    public static $_http;	// "http(s)://hostname(:port)" full connection string
    public static $_language;	// language to translate to (via Pommo::_T())
    public static $_slanguage;	// the "session" language (if set)
    public static $_l10n;	// True if language is not english
    public static $_dbo;	// holds the database object
    public static $_section;	// Current section
    public static $_session;	// install/instance values in $_SESSION

    /*	__construct
     *	Does nothing since everything is static
     *
     *	@return	void
     */
    private function __construct()
    {
    }

    /*	preInit
     *	populates poMMo's core with values from config.php.
     *	initializes the logger + database
     *
     *	@return	boolean	True if there is a config.php file, false otherwise
     */
    public static function preInit($baseDir)
    {
        //	Remove quotes added by magic_quotes
        if (get_magic_quotes_gpc()) {
            $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
            while (list($key, $val) = each($process)) {
                foreach ($val as $k => $v) {
                    unset($process[$key][$k]);
                    if (is_array($v)) {
                        $process[$key][stripslashes($k)] = $v;
                        $process[] = &$process[$key][stripslashes($k)];
                    } else {
                        $process[$key][stripslashes($k)] = stripslashes($v);
                    }
                }
            }
            unset($process);
        }

        self::$_baseDir		= $baseDir;
        self::$_config 		= array ();
        self::$_auth 		= null;
        self::$_escaping	= false;

        require_once(self::$_baseDir.'classes/Pommo_Log.php');
        require_once(self::$_baseDir.'lib/SafeSQL.class.php');
        require_once(self::$_baseDir.'classes/Pommo_Db.php');
        require_once(self::$_baseDir.'classes/Pommo_Auth.php');

        // 	initialize logger
        //	Check where this config variable comes from
        self::$_logger = new Pommo_Log();
        self::$_workDir = (empty($config['workDir'])) ?
                self::$_baseDir.'cache' : $config['workDir'];
        self::$_debug = (strtolower($config['debug']) != 'on') ? false : true;
        self::$_default_subscriber_sort =
                (empty($config['default_subscriber_sort'])) ?
                'email' : $config['default_subscriber_sort'];
        self::$_verbosity = (empty($config['verbosity'])) ? 3 :
                $config['verbosity'];
        self::$_logger->_verbosity = self::$_verbosity;
        self::$_dateformat = ($config['date_format'] >= 1
                && $cofig['date_format'] <= 3) ?
                intval($config['date_format']) : 1;

        //	set base URL (e.g. http://mysite.com/news/pommo => 'news/pommo/')
        if (isset ($config['baseURL']))
        {
            self::$_baseUrl = $config['baseURL'];
        }
        else
        {
            // 	If we're called from an embedded script, read baseURL from
            //	"last known good". Else, set it based off of REQUEST.
            if (defined('_poMMo_embed'))
            {
                require_once(self::$_baseDir.
                        'classes/Pommo_Helper_Maintenance.php');
                self::$_baseUrl = Pommo_Helper_Maintenance::rememberBaseURL();
            }
            else
            {
                $regex = '@/(ajax|inc|setup|user|install|support(/tests|/util)?|'.
                        'admin(/subscribers|/user|/mailings|/setup)?'.
                        '(/ajax|/mailing|/config)?)$@i';

                // This is to fix backslashes on windows systems
                $dirname = str_replace('\\', '/',  dirname($_SERVER['PHP_SELF']));

                $baseUrl = preg_replace($regex, '', $dirname);
                self::$_baseUrl = ($baseUrl == '/') ? $baseUrl : $baseUrl.'/';
            }
        }

        // read in config.php (configured by user)
        $config = Pommo_Helper::parseConfig(self::$_baseDir.'config.php');

        //	check to see if config.php was "properly" loaded
        if (count($config) < 5)
        {
            self::$_hasConfigFile = false;
            return self::$_hasConfigFile;
        }

        self::$_hasConfigFile = true;

        //	the regex strips port info from hostname
        self::$_hostname = (empty($config['hostname'])) ?
                preg_replace('/:\d+$/i', '', $_SERVER['HTTP_HOST']) :
                $config['hostname'];
        self::$_hostport = (empty($config['hostport'])) ?
                $_SERVER['SERVER_PORT'] : $config['hostport'];
        self::$_ssl = (!isset($_SERVER['HTTPS']) ||
                strtolower($_SERVER['HTTPS']) != 'on') ? false : true;
        self::$_http = ((self::$_ssl) ? 'https://' : 'http://').
                self::$_hostname;
        if (self::$_hostport != 80 && self::$_hostport != 443)
        {
            self::$_http .= ':'.self::$_hostport;
        }

        self::$_language = (empty($config['lang'])) ? 'en' :
                strtolower($config['lang']);
        self::$_slanguage = (defined('_poMMo_lang')) ? _poMMo_lang : false;

        //	include translation (l10n) methods if language is not English
        self::$_l10n = FALSE;
        if (self::$_language != 'en')
        {
            self::$_l10n = TRUE;
            require_once(self::$_baseDir.'classes/Pommo_Helper_L10n.php');
            Pommo_Helper_L10n::init(self::$_language, self::$_baseDir);
        }

        //	set the current "section" -- should be "user" for /user/* files,
        //	"mailings" for /admin/mailings/* files, etc. etc.
        self::$_section = preg_replace('@^admin/?@i', '',
                str_replace(self::$_baseUrl, '', dirname($_SERVER['PHP_SELF'])));

        $db_conn_compress = (strtolower($config['db_conn_compress']) != 'on') ? 0 : MYSQL_CLIENT_COMPRESS;
        $db_conn_secure   = (strtolower($config['db_conn_secure']) != 'on') ? 0 : MYSQL_CLIENT_SSL;

        // 	initialize database link
        self::$_dbo = @new Pommo_Db($config['db_username'],
                $config['db_password'], $config['db_database'],
                $config['db_hostname'], $config['db_prefix'],
                $db_conn_compress, $db_conn_secure);

        // 	turn off debugging if in user area
        if(self::$_section == 'user')
        {
            self::$_debug = false;
            self::$_dbo->debug(FALSE);
        }

        // if debugging is set in config.php, enable debugging on the database.
        if (self::$_debug)
        {
            // don't enable debugging in ajax requests unless verbosity is < 3
            if (Pommo_Helper::isAjax() && self::$_verbosity > 2)
            {
                self::$_debug = false;
            }
            else
            {
                self::$_dbo->debug(TRUE);
            }
        }
        return true;
    }

    /*	init
     *	called by page to load page state, populate config, and track
     *	authentication.
     *
     *	@param	array	$args.- (passed as Pommo::init(array('arg' => val,
     *					'arg2' => val)) ]
     *					authLevel: check that authenticated permission level is
     *					at least authLevel (non authenticated == 0).
     *					exit if not high enough. [default: 1]
     *					keep: keep data stored in session. [default: false]
     *					session: explicity set session name. [default: null]
     * 					install: bypass loading of config/version checking
     *					[default: false]
     *
     *	@return	boolean	True on success
     */
    public static function init($args = array ())
    {
        $defaults = array (
            'authLevel'	=> 1,
            'keep' 		=> FALSE,
            'noSession' => FALSE,
            'sessionID' => NULL,
            'install' 	=> FALSE
        );

        // 	merge submitted parameters
        $p = Pommo_Api::getParams($defaults, $args);

        // 	Return if not config.php file present
        if (!self::$_hasConfigFile)
        {
            return false;
        }

        //	Bypass Reading of Config, SESSION creation, and authentication checks
        //	and return if 'install' passed
        if ($p['install'])
        {
            return;
        }

        // 	load configuration data. Note: cannot save in session, as session
        //	needs unique key -- this is simplest method.
        self::$_config = Pommo_Api::configGetBase();

        //	toggle DB debugging
        if (self::$_debug)
        {
            self::$_dbo->debug(TRUE);
        }

        //	Bypass SESSION creation, reading of config, authentication checks
        //	and return if 'noSession' passed
        if ($p['noSession'])
        {
            return;
        }

        // 	Start the session
        if (!empty($p['sessionID']))
        {
            session_id($p['sessionID']);
        }
        self::startSession();

        // check for "session" language -- user defined language on the fly.
        if (self::$_slanguage)
        {
            self::$_session['slanguage'] = self::$_slanguage;
        }

        if (isset(self::$_session['slanguage']))
        {
            if (self::$_session['slanguage'] == 'en')
            {
                self::$_l10n = FALSE;
            }
            else
            {
                self::$_l10n = TRUE;
                require_once(self::$_baseDir.'classes/Pommo_Helper_L10n.php');
                Pommo_Helper_L10n::init(self::$_session['slanguage'],
                        self::$_baseDir);
            }
            self::$_slanguage = self::$_session['slanguage'];
        }

        // 	if authLevel == '*' || _poMMo_support (0 if poMMo not installed,
        //	1 if installed)
        if (defined('_poMMo_support'))
        {
            require_once(self::$_baseDir.'classes/Pommo_Install.php');
            $p['authLevel'] = (Pommo_Install::verify()) ? 1 : 0;
        }

        // check authentication levels
        self::$_auth = new Pommo_Auth(array (
            'requiredLevel' => $p['authLevel']
        ));

        // clear SESSION 'data' unless keep is passed.
        // TODO --> phase this out in favor of page state system?
        // -- add "persistent" flag & complicate state initilization...
        if (!$p['keep'])
        {
            self::$_session['data'] = array ();
        }
    }

    /*	reloadConfig
     *	Loads config base
     *
     *	@return	array	self::$_config
     */
    static function reloadConfig()
    {
        return self::$_config = Pommo_Api::configGetBase(TRUE);
    }

    /*	toogleEscaping
     *	Changes the escaping status
     *
     *	@param	boolean	$toogle.- New escaping value
     *
     *	@return	boolean	$toogle
     */
    function toggleEscaping($toggle = TRUE)
    {
        self::$_escaping = $toggle;
        self::$_logger->toggleEscaping(self::$_escaping);
        return $toggle;
    }

     /*	_T
     *	Translation (l10n) Function
     *
     *	@param	string	$msg.- Message to translate
     *
     *	@return	string	Translated message
     */
     static function _T($msg)
     {
        if(Pommo::$_escaping)
        {
            return (Pommo::$_l10n) ?
                    htmlspecialchars(Pommo_Helper_L10n::translate($msg)) :
                    htmlspecialchars($msg);
        }
        return (Pommo::$_l10n) ? Pommo_Helper_L10n::translate($msg) : $msg;
    }

    /*	_TP
     *	Takes care of plurals when translating
     *
     *	@param	string	$msg.- Message to translate
     *	@param	string	$plural
     *	@param	string	$count
     *
     *	@return	string	Translated message
     */
    function _TP($msg, $plural, $count)
    {
        if (Pommo::$_escaping) {
            return (Pommo::$_l10n) ? htmlspecialchars(
                Pommo_Helper_L10n::translatePlural($msg, $plural, $count)
            ) : htmlspecialchars($msg);
        }
        return (Pommo::$_l10n) ?
                Pommo_Helper_L10n::translatePlural($msg, $plural, $count) : $msg;
    }

    /*	set
     *	Saves value in Pommo::$_session array
     *
     *	@param	mixed	$value.- Value to save
     *
     *	@return	mixed	Saved value
     */
    public static function set($value)
    {
        if (!is_array($value)) {
            $value = array (
                $value => TRUE
            );
        }
        return (empty(self::$_session['data'])) ?
                self::$_session['data'] = $value :
                self::$_session['data'] = array_merge(
                    self::$_session['data'],
                    $value
                );
    }

    /*	get
     *	Gets value from Pommo::$_session array
     *
     *	@param	mixed	$name
     *
     *	@return	mixed	value
     */
    public static function get($name = FALSE)
    {
        if ($name) {
            return (isset(self::$_session['data'][$name])) ?
                    self::$_session['data'][$name] :
                    array();
        }
        return self::$_session['data'];
    }


    /*	redirect
     *	Redirect to pommo page
     *
     *	@param	string	$url
     *	@param	string	$msg
     *	@param	boolean	$kill
     *
     *	@return
     */
    public static function redirect($url, $msg = NULL, $kill = true)
    {
        // adds http & baseURL if they aren't already provided...
        //	allows code shortcuts ;)
        //  if url DOES NOT start with '/', the section will automatically be
        //	appended
        if (!preg_match('@^https?://@i', $url)) {
            if (strpos($url, Pommo::$_baseUrl) === false) {
                if (substr($url, 0, 1) != '/') {
                    if (Pommo::$_section != 'user' &&
                            Pommo::$_section != 'admin') {
                        $url = Pommo::$_http.Pommo::$_baseUrl.$url;
                    } else {
                        $url = Pommo::$_http.Pommo::$_baseUrl.Pommo::$_section.
                                '/'.$url;
                    }
                } else {
                    $url = Pommo::$_http.Pommo::$_baseUrl.
                            str_replace(Pommo::$_baseUrl, '', substr($url, 1));
                }
            } else {
                $url = Pommo::$_http.$url;
            }
        }
        header('Location: '.$url);
        if ($kill) {
            if ($msg) {
                Pommo::kill($msg);
            } else {
                Pommo::kill(Pommo::_T('Redirecting, please wait...'));
            }
        }
        return;
    }

    /*	kill
     *	Used to terminate a script
     *
     *	@param	string	$msg.- Message to display
     *	@param	array	$backtrace
     *
     *	@return	void
     */
    public static function kill($msg = NULL, $backtrace = FALSE)
    {
        // output passed message
        if ($msg) {
            if (empty(self::$_workDir)) {
                echo ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
                        "http://www.w3.org/TR/html4/strict.dtd">');
                echo ('<title>poMMo Error</title>'); // Added for valid output
                echo '<div><img src="'.Pommo::$_baseUrl.
                        'themes/shared/images/icons/alert.png" alt="alert icon"
                        style="vertical-align: middle; margin-right: 20px;"/>'.
                        $msg.'</div>';
            } else {
                $logger = self::$_logger;
                $logger->addErr($msg);
                require_once self::$_baseDir.'classes/Pommo_Template.php';
                $view = new Pommo_Template();
                $view->assign('fatalMsg', TRUE);
                $view->display('message');
            }
        }

        // output debugging info if enabled (in config.php)
        if (self::$_debug) {
            require_once(self::$_baseDir.'classes/Pommo_Helper_Debug.php');
            $debug = new Pommo_Helper_Debug();
            $debug->bmDebug();
        }

        if ($backtrace) {
            $backtrace = debug_backtrace();
            echo @'<h2>BACKTRACE</h2>'.
                    '<p>'.@str_ireplace(
                        Pommo::$_baseDir, '',
                        $backtrace[1]['file']
                    ).':'.$backtrace[1]['line'].' '.
                    $backtrace[1]['function'].'()</p>'
                    .'<p>'.@str_ireplace(
                        Pommo::$_baseDir, '',
                        $backtrace[2]['file']
                    ).' '.$backtrace[2]['function'].
                    '()</p>'.'<p>'.@str_ireplace(
                        Pommo::$_baseDir, '',
                        $backtrace[3]['file']
                    ).' '.$backtrace[3]['function'].
                    '()</p>';
        }

        // print and clear output buffer
        ob_end_flush();

        // kill script
        die();
    }

    /*	startSession
     *	Starts a new session
     *
     *	@param	string	$name.- Session name
     *
     *	@return	void
     */
    static function startSession($name = null)
    {
        static $start = false;
        if (!$start) {
            session_start();
        }
        $start = true;

        // generate unique session name
        $key = self::$_config['key'];

        if (empty($key)) {
            $key = '123456';
        }

        // create SESSION placeholder for if this is a new session
        if (empty ($_SESSION['pommo'.$key])) {
            $_SESSION['pommo'.$key] = array (
                'data' => array (),
                'state' => array (),
                'username' => null
            );
        }

        self::$_session = &$_SESSION['pommo'.$key];
    }

    /*	logErrors
     *	error log, E_ERROR trapping
     *
     *	@return	void
     */
    static function logErrors()
    {
        // ignore call if verbosity maximum.
        if (self::$_verbosity < 2) {
            return;
        }

        // error handling
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
        ini_set('log_errors_max_len', 0);
        ini_set('html_errors', 0);

        // set log file
        ini_set('error_log', self::$_workDir.'/ERROR_LOG');
    }

    public static function debug($text)
    {
        $mysqli = new mysqli("localhost", "cerr_user", "asdfasdf", "pommo");
        $mysqli->query(
            "INSERT INTO adebug(content) VALUES('".
            $mysqli->real_escape_string($text)."')"
        );
        $mysqli->close();
    }
}
