<?php
/**
 * @package    openid
 * @version    1.0
 * @license    GPLv3
 * @author     Gilles Meier
 * @link       http://github.com/krtek4/fuel-openid
 */

class Controller_Authopenid extends \Controller {
	// <editor-fold defaultstate="collapsed" desc="Properties">

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
	 * Procede with the OpenID authentication process.
	 *
	 * If everything went smoothly, the user is redirect to the 'sucess' action, otherwise the user is
	 * redirected to the 'error' action. The error code can be retrieved with Auth::instance()->error_code()
	 */
	final public function action_login() {
		$controller = $this->request->controller;
		$auth = Auth::instance();
		if(! $auth->check()) {
			if(! $auth->login(Input::get_post('openid_identifier'))) {
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

	public function action_file($type, $filename) {
		$filename = func_get_args();
		array_shift($filename);

		\Config::load('openid', true);
		if(in_array('..', $filename) || ! \Config::get('openid.use_file_action')) {
			Request::show_404();
		}

		return $this->return_file($type, implode('/', $filename));
	}

	// </editor-fold>
}
