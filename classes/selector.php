<?php
/**
 * @package    openid
 * @version    1.0
 * @license    GPLv3
 * @author     Gilles Meier
 * @link       http://github.com/krtek4/fuel-openid
 */

class OpenID_Selector extends \Fuel\Core\Singleton {
	/**
	 * Return the HTML for an OpenId Selector form. You must provide the URL the provided must
	 * call after authenticating the user. If you're using the provided controller it will be the login action.
	 *
	 * @param string $url the URL for the provider
	 * @return string HTML form for OpenID provider selection
	 */
	public function get_form() {
		return \View::factory('form')->set('url', Uri::create('authopenid/login'));
	}

	public function get_css() {
		return array(
			Uri::create('authopenid/file/css/openid-shadow.css'),
		);
	}

	public function get_inline_js() {
		// TODO: provide a way to have Prototype or MooTools init code.
		return '$(document).ready(function() { openid.init("openid_identifier"); });';
	}

	public function get_js() {
		// TODO: provide a way to choose between jQuery, MooTools or Prototype
		return array(
			Uri::create('authopenid/file/js/openid-jquery.js'),
			Uri::create('authopenid/file/js/locales/openid-en.js'),
		);
	}
}
?>
