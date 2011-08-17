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
	public function get_form($url) {
		return \View::factory('form')->set('url', $url);
	}

	public function get_inline_js() {
		return 'openid.init("openid_identifier");';
	}
}
?>
