<?php

  //  ini_set('display_errors',1);
  //error_reporting(E_ALL);

/* ==========================================================
 * ErgoAdmin v1.2
 * index.php
 * 
 * http://www.mosaicpro.biz
 * Copyright MosaicPro
 *
 * Built exclusively for sale @Envato Marketplaces
 * ========================================================== */

include("lib/functions.php");
defined('APP_NAME') || define('APP_NAME', 'N4Gtv Inpulse');
defined('APP_VERSION') || define('APP_VERSION', 'v1');

/*
 * Config
 */
session_start();
// Current / default page
$page = isset($_GET['page']) ? $_GET['page'] : 'index';
if(logged_in()!=true){
  $page = 'login';
}else{
  $userInfo = getInfo($_SESSION['n4guser'],$_SESSION['n4gxhash']);
  $userInfo->{'avatar'} = getAvatar($_SESSION['n4guser'],$_SESSION['n4gxhash']);
}
// relative path to theme common resources (styles/iamges/etc)
// used for generating the final download package
// $_ROOT = "../../../../common/";
$_ROOT = "../common/";

// development / production
defined('DEV') || define('DEV', false);

// used to determine what resources to use in final package
defined('DEMO') || define('DEMO', false);

// toggle google analytics code in head section
defined('GA') || define('GA', false);

// default level / used for getURL paths (should't be modified)
defined('LEVEL') || define('LEVEL', 0);

// allow menu customization from the browser
defined('MENU_JS') || define('MENU_JS', /* isset($_GET['menu_position']) ? false :*/ false);

// allow layout customization from the browser
defined('LAYOUT_JS') || define('LAYOUT_JS', /* isset($_GET['layout_type']) ? false :*/ false);

// allow skin customization from the browser
defined('SKIN_JS') || define('SKIN_JS', false);

// show dark/light style toggle button
defined('STYLE_TOGGLE') || define('STYLE_TOGGLE', /* !DEMO ? false :*/ false);

// 'menu-right' / 'menu-left' or 'menu-hidden'
defined('MENU_POSITION') || define('MENU_POSITION', /* isset($_GET['menu_position']) ? $_GET['menu_position'] : */ 'menu-left');

// 'fixed' or 'fluid'
defined('LAYOUT_TYPE') || define('LAYOUT_TYPE', (($page == 'landing_page_1' || $page == 'landing_page_2') ? 'fluid' : (isset($_GET['layout_type']) ? $_GET['layout_type'] : 'fixed')));

// MAIN stylesheet
defined('STYLE') || define('STYLE', /* isset($_GET['style']) ? $_GET['style'] : */ 'style-dark');

// filename without extension (eg. "brown") or false for default
defined('SKIN_CUSTOM') || define('SKIN_CUSTOM', "dash");

// edit SKIN_CUSTOM above
defined('SKIN') || define('SKIN', /*SKIN_JS ? false :*/ SKIN_CUSTOM);

// enable or disable the Guided Tour
defined('GUIDED_TOUR') || define('GUIDED_TOUR', false);

// enable or disable the Resizable sidebars functionality
defined('MENU_RESIZABLE') || define('MENU_RESIZABLE', false);

/*
 * Other variables
 * Used mainly for documentation
 */

$section = isset($_GET['section']) ? $_GET['section'] : 'index';
$sub_section = isset($_GET['ss']) ? $_GET['ss'] : 'index';
$_LEVEL = LEVEL; // used for getURL function paths

/*
 * Zend_Translate
 */
define('APP_PATH',	realpath(dirname(__FILE__)));
define('APP_LANG',	APP_PATH . '/lang');

require_once 'Zend/Translate.php';

$locale = isset($_GET['lang']) ? $_GET['lang'] : 'en'; // default language
$translate = new Zend_Translate( array( 'adapter' => 'csv', 'content' => APP_LANG, 'scan' => Zend_Translate::LOCALE_DIRECTORY ) );
$translate->setLocale($locale);

/*
 * Functions
 */

require_once 'functions.php';

/*
 * Colors
 */

$primaryColor = "#8ec657";
$dangerColor = "#b55151";
$successColor = "#609450";
$warningColor = "#ab7a4b";
$inverseColor = "#45484d";

/*
 * Pages
 */

switch ($page)
{
	default: 
		$page = 'error';
		
		// header
		require_once 'pages/header.php';
		
		// content
		require_once 'pages/error.php';
		break;

	case 'index':
	case 'analytics':
	case 'login':
	case 'signup':
	case 'faq':
	case 'my_account':
	case 'my_account_advanced':
	case 'pages':
	case 'grid':
	case 'ui':
	case 'icons':
	case 'widgets':
	case 'tabs':
	case 'form_elements':
	case 'form_wizards':
	case 'form_validator':
	case 'file_managers':
	case 'tables':
	case 'pricing_tables':
	case 'modals':
	case 'thumbnails':
	case 'carousels':
	case 'tour':
	case 'notifications':
	case 'sliders':
	case 'calendar':
	case 'gallery_1':
	case 'gallery_2':
	case 'charts':
	case 'bookings':
	case 'finances':
	case 'invoice':
	case 'maps_vector':
	case 'maps_google':
	case 'shop_admin_products':
	case 'shop_admin_product':
	case 'shop_client_products':
	case 'shop_client_product':
	case 'shop_client_cart':
	case 'tasks':
	case 'blank':
	case 'choose':
	case 'documentation':
	case 'landing_page_1':
	case 'landing_page_2':
		// header
		require_once 'pages/header.php';
		
		// content
		if (file_exists('pages/' . $page . '.php')) require_once 'pages/' . $page . '.php';
		break;
	
}

/*
 * Footer
 */

require_once 'pages/footer.php';