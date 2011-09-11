<?php
/**
 * @package    openid
 * @version    1.0
 * @license    GPLv3
 * @author     Gilles Meier
 * @link       http://github.com/krtek4/fuel-openid
 */

/**
 * Controller used to authenticate the user with an OpenID provider.
 *
 * 3 actions are defined :
 *
 * login : two step process. First the user is redirected to the OpenID provider,
 * then he is logged in with the information returned by the provider.
 *
 * logout : log the user out.
 *
 * file : transmit the various file needed by the openid-selector if activated.
 */
class Controller_Authopenid extends \Controller {
	// <editor-fold defaultstate="collapsed" desc="Properties">

	/**
	 * @var array The various MIME types the file action can return in an associative
	 * array.
	 */
	private static $types = array(
		'css' => 'text/css',
		'js' => 'text/javascript',
		'images' => '',
	);

	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="Private methods">

	private function return_file($dir, $filename) {
		$path = __DIR__.'/../../vendor/openid-selector/'.$dir.'/'.$filename;
		$content = file_get_contents($path);
		header('Content-type: '.static::$types[$dir]);
		echo $content;
		exit();
	}

	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="Actions">

	/**
	 * Procede with the OpenID authentication process. The OpenID identifier
	 * must be set in the GET variable named openid_identifier
	 *
	 * If everything went smoothly, the user is redirect to the 'sucess' action, otherwise the user is
	 * redirected to the 'error' action. The error code is saved in the session under the 'e_openid'
	 * hash.
	 */
	final public function action_login() {
		$controller = $this->request->controller;
		$auth = Auth::instance();
		if(! $auth->check()) {
			if(! $auth->login(Input::get_post('openid_identifier'))) {
				Session::set('e_openid', $auth->error_code());
				Response::redirect($auth->get_action('error'));
			}
		}
		Response::redirect($auth->get_action('success'));
	}

	/**
	 * Logout the user and redirect to the 'index' action.
	 */
	public function action_logout() {
		$auth = Auth::instance();
		$auth->logout();
		Response::redirect($auth->get_action('logout'));
	}

	/**
	 * Read a file on the disk and transmit its content to the browser.
	 * This action can be used to includes the file needed by the openid-selector.
	 *
	 * The first url parameter must contain the path to the wanted file relatively
	 * to the openid-selector directory : myhost.com/authopenid/css/openid-shadow.css
	 * will load PKGPATH/openid/vendor/openid-selector/css/openid-shadow.css
	 *
	 * For security reason, access is restricted to this base directory and its
	 * children.
	 */
	public function action_file() {
		$filename = func_get_args();
		$type = array_shift($filename);

		\Config::load('openid', true);
		if(in_array('..', $filename) || ! \Config::get('openid.use_file_action')) {
			Request::show_404();
		}

		$this->return_file($type, implode('/', $filename));
	}

	// </editor-fold>
}
