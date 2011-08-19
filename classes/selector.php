<?php
/**
 * @package    openid
 * @version    1.0
 * @license    GPLv3
 * @author     Gilles Meier
 * @link       http://github.com/krtek4/fuel-openid
 */

class OpenID_Selector extends \Fuel\Core\Singleton {
	private function format_filename($filename) {
		if($this->use_file_action()) {
			return Uri::create('authopenid/file/'.$filename);
		} else {
			return '/../fuel/packages/openid/vendor/openid-selector/'.$filename;
		}
	}

	public function __construct() {
		\Config::load('openid', true);
	}

	private function use_file_action() {
		return \Config::get('openid.use_file_action');
	}

	private function image_path() {
		return \Config::get('openid.openid_selector_img');
	}

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
			$this->format_filename('css/openid-shadow.css'),
		);
	}

	public function get_inline_js() {
		// TODO: provide a way to have Prototype or MooTools init code.
		return '$(document).ready(function() { openid.init("openid_identifier", "'.$this->image_path().'"); });';
	}

	public function get_js() {
		// TODO: provide a way to choose between jQuery, MooTools or Prototype
		return array(
			$this->format_filename('js/openid-jquery.js'),
			$this->format_filename('js/locales/openid-en.js'),
		);
	}
}
?>
