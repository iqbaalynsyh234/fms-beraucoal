<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your CodeIgniter root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
*/

$servername = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "fmspoc.abditrack.com";
$config['base_url']	= "http://localhost/fmsbc-jan2024/";
/* if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
	$config['base_url']	= "https://".$servername."/";
}else{
	$config['base_url']	= "http://".$servername."/";
} */

$config['admin_mail'] = "buddiyanto2@gmail.com";
$config['license'] = "abditrack.com";

switch($servername)
{
	case "farrasindo.lacak-mobil.com":
		include "config.farrasindo.php";
	break;
	case "pln.lacak-mobil.com":
		include "config.pln.php";
	break;
	case "agungputra.lacak-mobil.com":
		include "config.agungputra.php";
	break;
	case "app.nusa-track.com":
		include "config.nusa-track.php";
	break;
	case "tracker.gpsandalas.com":
		include "config.gpsandalas.php";
	break;
	case "pgn.gpsandalas.com":
		include "config.pgn.gpsandalas.com.php";
	break;
	case "oto-track.com":
		include "config.oto-track.php";
	break;
	case "www.oto-track.com":
	case "app.oto-track.com":
		include "config.oto-track.php";
	break;
	case "tnt.lacak-mobil.com":
		include "config.tnt.php";
	break;
	case "usahabarutrans.com":
		include "config.usahabarutrans.php";
	break;
	case "transporter.lacak-mobil.com":
		include "config.transporter.php";
	break;
	case "triple-i.lacak-mobil.com":
		include "config.triple-i.php";
	break;
	case "kpa.lacak-mobil.com":
		include "config.kpa.php";
	break;
	case "transporter.gpsandalas.com":
		include "config.transporterandalas.php";
	break;
	case "alatberat.lacak-mobil.com":
		include "config.alatberat.php";
	break;
	case "balrich.lacak-mobil.com":
		include "config.balrich.php";
	break;
	case "intan-utama.lacak-mobil.com":
		include "config.intanutama.php";
	break;
	case "framas.lacak-mobil.com":
		include "config.framas.php";
	break;
	case "tamari.lacak-mobil.com":
		include "config.tamari.php";
	break;
	case "rentcar.lacak-mobil.com":
		include "config.rentcar.php";
	break;
	case "berdikari.lacak-mobil.com":
		include "config.berdikari.php";
	break;
	case "itb.lacak-mobil.com":
		include "config.itb.php";
	break;
	case "adhikarya.lacak-mobil.com":
		include "config.adhikarya.php";
	break;
	case "powerblock.lacak-mobil.com":
		include "config.powerblock.php";
	break;
	case "indahkiat2.lacak-mobil.com":
		include "config.indahkiat.php";
	break;
	case "csa.lacak-mobil.com":
		include "config.csa.php";
	break;
	case "kumis.lacak-mobil.com":
		include "config.kumis.php";
	break;
	case "pgn.lacak-mobil.com":
		include "config.pgn.php";
	break;
	case "mml.lacak-mobil.com":
		include "config.mml.php";
	break;
	case "sanko.lacak-mobil.com":
		include "config.sanko.php";
	break;
	case "menara.lacak-mobil.com":
		include "config.menara.php";
	break;
	case "tupperware.lacak-mobil.com":
		include "config.tupperware.php";
	break;
	case "bangun-trans.lacak-mobil.com":
		include "config.bangun.php";
	break;
	case "reksaprabawa.lacak-mobil.com":
		include "config.reksaprabawa.php";
	break;
	case "ppi.lacak-mobil.com":
		include "config.ppi.php";
	break;
	case "iwatani.lacak-mobil.com":
		include "config.iwatani.php";
	break;
	case "ssi.lacak-mobil.com":
		include "config.ssi.php";
	break;
	case "tcontinent.lacak-mobil.com":
		include "config.tcontinent.php";
	break;
	case "gpt.lacak-mobil.com":
		include "config.gpt.php";
	break;
	case "aryos.lacak-mobil.com":
		include "config.aryos.php";
	break;
	case "jennete.lacak-mobil.com":
		include "config.jennete.php";
	break;
	case "lacaktranslog.com":
		include "config.lacaktranslog.php";
	break;
	case "euroasiatictrans.com":
		include "config.euroasiatictrans.php";
	break;
	case "pcltranslog.com":
		include "config.pcltranslog.php";
	break;
	case "herbatrans.com":
		include "config.herbatrans.php";
	break;
	case "abcargotrans.com":
		include "config.abcargotrans.php";
	break;
	case "kctlog.com":
		include "config.kctlog.php";
	break;
	case "damaslog.com":
		include "config.damaslog.php";
	break;
	case "predictrans.com":
		include "config.predictrans.php";
	break;
	case "kpatrans.com":
		include "config.kpatrans.php";
	break;
	case "jenneterent.com":
		include "config.jenneterent.php";
	break;
	case "aryoslog.com":
		include "config.aryoslog.php";
	break;
	case "tamarilog.com":
		include "config.tamarilog.php";
	break;
	case "farrasindopump.com":
		include "config.farrasindopump.php";
	break;
	case "intanutamalog.com":
		include "config.intanutamalog.php";
	break;
	case "balrichlog.com":
		include "config.balrichlog.php";
	break;
	case "ssi-trans.com":
		include "config.ssitrans.php";
	break;
		case "spabadirent.com":
		include "config.spabadirent.php";
	break;
	case "powerblocktrans.com":
		include "config.powerblocktrans.php";
	break;
	case "t-continent.com":
		include "config.tcontinent2.php";
	break;
	case "itbtrans.com":
		include "config.itbtrans.php";
	break;
	case "iwatanitrans.com":
		include "config.iwatanitrans.php";
	break;
	case "kumislog.com":
		include "config.kumislog.php";
	break;
	case "mitratrans.com":
		include "config.mitratrans.php";
	break;
	case "tag-trans.com":
		include "config.tagtrans.php";
	break;
	case "dinkesgarut.lacak-mobil.com":
		include "config.dinkeskabgarut.php";
	break;
	case "concretepump.lacak-mobil.com":
		include "config.concretepump.php";
	break;
	case "yimm.lacak-mobil.com":
		include "config.yimm.php";
	break;
	case "www16.lacaktranslog.com":
		include "config.www16.lacaktranslog.php";
	break;
	case "bib.lacaktranslog.com":
	case "teman-indobara.lacaktranslog.com":
	case "teman.borneo-indobara.com":
	case "attachment.lacaktranslog.com":
		include "config.bib.php";
	break;
	case "teman-rbt.borneo-indobara.com":
	case "temanrbt.borneo-indobara.com":	
		include "config.formrbt.php";
	break;
	case "teman-mks.borneo-indobara.com":
	case "temanmks.borneo-indobara.com":
		include "config.formmks.php";
	break;
	case "teman-bkae.borneo-indobara.com":
	case "temanbkae.borneo-indobara.com":
		include "config.formbkae.php";
	break;
	case "teman-ramb.borneo-indobara.com":
	case "temanramb.borneo-indobara.com":
		include "config.formramb.php";
	break;
	case "teman-stli.borneo-indobara.com":
	case "temanstli.borneo-indobara.com":
		include "config.formstli.php";
	break;
	case "teman-kmb.borneo-indobara.com":
	case "temankmb.borneo-indobara.com":
		include "config.formkmb.php";
	break;
	case "teman-mms.borneo-indobara.com":
	case "temanmms.borneo-indobara.com":
		include "config.formmms.php";
	break;
	case "teman-bbs.borneo-indobara.com":
	case "temanbbs.borneo-indobara.com":
		include "config.formbbs.php";
	break;
	case "teman-gecl.borneo-indobara.com":
	case "temangecl.borneo-indobara.com":
		include "config.formgecl.php";
	break;
	case "teman-est.borneo-indobara.com":
	case "temanest.borneo-indobara.com":
	case "temanaek.borneo-indobara.com":
		include "config.formest.php";
	break;
	case "temanbib.borneo-indobara.com":
	case "temandev.borneo-indobara.com":
	case "temangps.borneo-indobara.com":
	case "temanpoc.borneo-indobara.com":
	case "attachment.borneo-indobara.com":
	case "fms.buddiyanto.my.id":
	case "fms.pilartech.co.id":
	case "attachment.pilartech.co.id":
		include "config.bib2.php";
	break;
	case "fmsdemo.abditrack.com":
	case "fmspoc.abditrack.com":
	case "fms.abditrack.com":
	case "attachment.abditrack.com":
		include "config.abditrack.php";
	break;
	
	case "live.abditrack.com":
	case "media.abditrack.com":
		$config['base_url']	= "http://".$servername."/";
		include "config.abditrack.php";
	break;
	
	default:
        include "config.abditrack.php";
    break;
}


/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
|
| Typically this will be your index.php file, unless you've renamed it to
| something else. If you are using mod_rewrite to remove the page set this
| variable so that it is blank.
|
*/
$config['index_page'] = "index.php";

/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
|
| This item determines which server global should be used to retrieve the
| URI string.  The default setting of "AUTO" works for most servers.
| If your links do not seem to work, try one of the other delicious flavors:
|
| 'AUTO'			Default - auto detects
| 'PATH_INFO'		Uses the PATH_INFO
| 'QUERY_STRING'	Uses the QUERY_STRING
| 'REQUEST_URI'		Uses the REQUEST_URI
| 'ORIG_PATH_INFO'	Uses the ORIG_PATH_INFO
|
*/
$config['uri_protocol']	= "AUTO";

/*
|--------------------------------------------------------------------------
| URL suffix
|--------------------------------------------------------------------------
|
| This option allows you to add a suffix to all URLs generated by CodeIgniter.
| For more information please see the user guide:
|
| http://codeigniter.com/user_guide/general/urls.html
*/

$config['url_suffix'] = "";

/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
|
| This determines which set of language files should be used. Make sure
| there is an available translation if you intend to use something other
| than english.
|
*/
$config['language']	= "english";

/*
|--------------------------------------------------------------------------
| Default Character Set
|--------------------------------------------------------------------------
|
| This determines which character set is used by default in various methods
| that require a character set to be provided.
|
*/
$config['charset'] = "UTF-8";

/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
|
| If you would like to use the "hooks" feature you must enable it by
| setting this variable to TRUE (boolean).  See the user guide for details.
|
*/
$config['enable_hooks'] = FALSE;


/*
|--------------------------------------------------------------------------
| Class Extension Prefix
|--------------------------------------------------------------------------
|
| This item allows you to set the filename/classname prefix when extending
| native libraries.  For more information please see the user guide:
|
| http://codeigniter.com/user_guide/general/core_classes.html
| http://codeigniter.com/user_guide/general/creating_libraries.html
|
*/
$config['subclass_prefix'] = 'MY_';


/*
|--------------------------------------------------------------------------
| Allowed URL Characters
|--------------------------------------------------------------------------
|
| This lets you specify with a regular expression which characters are permitted
| within your URLs.  When someone tries to submit a URL with disallowed
| characters they will get a warning message.
|
| As a security measure you are STRONGLY encouraged to restrict URLs to
| as few characters as possible.  By default only these are allowed: a-z 0-9~%.:_-
|
| Leave blank to allow all characters -- but only if you are insane.
|
| DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
|
*/
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';


/*
|--------------------------------------------------------------------------
| Enable Query Strings
|--------------------------------------------------------------------------
|
| By default CodeIgniter uses search-engine friendly segment based URLs:
| example.com/who/what/where/
|
| You can optionally enable standard query string based URLs:
| example.com?who=me&what=something&where=here
|
| Options are: TRUE or FALSE (boolean)
|
| The other items let you set the query string "words" that will
| invoke your controllers and its functions:
| example.com/index.php?c=controller&m=function
|
| Please note that some of the helpers won't work as expected when
| this feature is enabled, since CodeIgniter is designed primarily to
| use segment based URLs.
|
*/
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] 	= 'c';
$config['function_trigger'] 	= 'm';
$config['directory_trigger'] 	= 'd'; // experimental not currently in use

/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
|
| If you have enabled error logging, you can set an error threshold to 
| determine what gets logged. Threshold options are:
| You can enable error logging by setting a threshold over zero. The
| threshold determines what gets logged. Threshold options are:
|
|	0 = Disables logging, Error logging TURNED OFF
|	1 = Error Messages (including PHP errors)
|	2 = Debug Messages
|	3 = Informational Messages
|	4 = All Messages
|
| For a live site you'll usually only enable Errors (1) to be logged otherwise
| your log files will fill up very fast.
|
*/
$config['log_threshold'] = 0;

/*
|--------------------------------------------------------------------------
| Error Logging Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| system/logs/ folder.  Use a full server path with trailing slash.
|
*/
$config['log_path'] = '';

/*
|--------------------------------------------------------------------------
| Date Format for Logs
|--------------------------------------------------------------------------
|
| Each item that is logged has an associated date. You can use PHP date
| codes to set your own date formatting
|
*/
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
|--------------------------------------------------------------------------
| Cache Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| system/cache/ folder.  Use a full server path with trailing slash.
|
*/
$config['cache_path'] = '';

/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
|
| If you use the Encryption class or the Sessions class with encryption
| enabled you MUST set an encryption key.  See the user guide for info.
|
*/
$config['encryption_key'] = "";

/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
|
| 'session_cookie_name' = the name you want for the cookie
| 'encrypt_sess_cookie' = TRUE/FALSE (boolean).  Whether to encrypt the cookie
| 'session_expiration'  = the number of SECONDS you want the session to last.
|  by default sessions last 7200 seconds (two hours).  Set to zero for no expiration.
| 'time_to_update'		= how many seconds between CI refreshing Session Information
|
*/
$config['sess_cookie_name']		= 'ci_session';
$config['sess_expiration']		= 7200;
$config['sess_encrypt_cookie']	= FALSE;
$config['sess_use_database']	= FALSE;
$config['sess_table_name']		= 'ci_sessions';
$config['sess_match_ip']		= FALSE;
$config['sess_match_useragent']	= TRUE;
$config['sess_time_to_update'] 	= 300;

/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
|
| 'cookie_prefix' = Set a prefix if you need to avoid collisions
| 'cookie_domain' = Set to .your-domain.com for site-wide cookies
| 'cookie_path'   =  Typically will be a forward slash
|
*/
$config['cookie_prefix']	= "";
$config['cookie_domain']	= "";
$config['cookie_path']		= "/";

/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
|
| Determines whether the XSS filter is always active when GET, POST or
| COOKIE data is encountered
|
*/
$config['global_xss_filtering'] = FALSE;

/*
|--------------------------------------------------------------------------
| Output Compression
|--------------------------------------------------------------------------
|
| Enables Gzip output compression for faster page loads.  When enabled,
| the output class will test whether your server supports Gzip.
| Even if it does, however, not all browsers support compression
| so enable only if you are reasonably sure your visitors can handle it.
|
| VERY IMPORTANT:  If you are getting a blank page when compression is enabled it
| means you are prematurely outputting something to your browser. It could
| even be a line of whitespace at the end of one of your scripts.  For
| compression to work, nothing can be sent before the output buffer is called
| by the output class.  Do not "echo" any values with compression enabled.
|
*/
$config['compress_output'] = FALSE;

/*
|--------------------------------------------------------------------------
| Master Time Reference
|--------------------------------------------------------------------------
|
| Options are "local" or "gmt".  This pref tells the system whether to use
| your server's local time as the master "now" reference, or convert it to
| GMT.  See the "date helper" page of the user guide for information
| regarding date handling.
|
*/
$config['time_reference'] = 'local';


/*
|--------------------------------------------------------------------------
| Rewrite PHP Short Tags
|--------------------------------------------------------------------------
|
| If your PHP installation does not have short tag support enabled CI
| can rewrite the tags on-the-fly, enabling you to utilize that syntax
| in your view files.  Options are TRUE or FALSE (boolean)
|
*/
$config['rewrite_short_tags'] = FALSE;


/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
|
| If your server is behind a reverse proxy, you must whitelist the proxy IP
| addresses from which CodeIgniter should trust the HTTP_X_FORWARDED_FOR
| header in order to properly identify the visitor's IP address.
| Comma-delimited, e.g. '10.0.1.200,10.0.1.201'
|
*/
$config['proxy_ips'] = '';


/* End of file config.php */
/* Location: ./system/application/config/config.php */