<?php

/**
 * Fuel-OpenID
 *
 * OpenID Auth driver for Fuel
 *
 * @package    openid
 * @version    1.0
 * @license    GPLv3
 * @author     Gilles Meier
 * @link       http://github.com/krtek4/fuel-openid
 */

Autoloader::add_core_namespace('OpenID');

Autoloader::add_classes(array(
	'Controller_Auth_OpenId' => __DIR__.'/classes/controller/auth.php',
	'OpenID_Selector' => __DIR__.'/classes/controller/selector.php',
	'Auth_Login_OpenID' => __DIR__.'/classes/auth/login/openid.php',
	'Auth_Login_OpenID_Error' => __DIR__.'/classes/auth/login/openid.php',
	'LightOpenID' => __DIR__.'/vendor/lightopenid/openid.php',
));
