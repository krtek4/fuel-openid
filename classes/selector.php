<?php
/**
 * @package    openid
 * @version    1.0
 * @license    GPLv3
 * @author     Gilles Meier
 * @link       http://github.com/krtek4/fuel-openid
 */

/**
 * Helper class to ease the use of OpenID-Selector.
 *
 * To activate the OpenID-Selector on your website, follow theses steps :
 *
 * 1° include the return value of get_form() on your site
 * 2° include the css files returned by get_css()
 * 3° include the javascript files returned by get_js()
 * 4° execute the javascript snippet returned by get_inline_js()
 *
 * For more information about the configuration, see the documentation.
 */
class OpenID_Selector extends \Fuel\Core\Singleton {
	/**
	 * Format the filename accordingly to the activation of the file action on
	 * the controller.
	 * @param string $filename the filename relative to the openid-selector dir
	 * @return string the formatted filename
	 */
	private function format_filename($filename) {
		if($this->use_file_action()) {
			return Uri::create('authopenid/file/'.$filename);
		} else {
			return '/../fuel/packages/openid/vendor/openid-selector/'.$filename;
		}
	}

	/**
	 * load the config for the openid package
	 */
	public function __construct() {
		\Config::load('openid', true);
	}

	/**
	 * @return bool is the file action enabled on the controller ?
	 */
	private function use_file_action() {
		return \Config::get('openid.use_file_action');
	}

	/**
	 * @return string the directory containing the images for the openid-selector
	 */
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
		$actions = \Config::get('openid.actions');
		return \View::factory('form')->set('url', Uri::create($actions['login']));
	}

	/**
	 * return a list of css files to include for the openid-selector.
	 * If the file action is enabled on the controller, it will be used.
	 * Otherwise, it will be the path relative to the DOCROOT.
	 * @return array files to include
	 */
	public function get_css() {
		return array(
			$this->format_filename('css/openid-shadow.css'),
		);
	}

	/**
	 * @return string a javascript snippet to include in order to initialize
	 * the openid-selector (jQuery only).
	 */
	public function get_inline_js() {
		// TODO: provide a way to have Prototype or MooTools init code.
		return '$(document).ready(function() { openid.init("openid_identifier", "'.$this->image_path().'"); });';
	}

	/**
	 * return a list of javascript files to include for the openid-selector.
	 * If the file action is enabled on the controller, it will be used.
	 * Otherwise, it will be the path relative to the DOCROOT.
	 *
	 * Currently, only jQuery is supported.
	 * @return array files to include
	 */
	public function get_js() {
		// TODO: provide a way to choose between jQuery, MooTools or Prototype
		return array(
			$this->format_filename('js/openid-jquery.js'),
			$this->format_filename('js/locales/openid-en.js'),
		);
	}
}
?>
