<?php
/**
 * @package    openid
 * @version    1.0
 * @license    GPLv3
 * @author     Gilles Meier
 * @link       http://github.com/krtek4/fuel-openid
 */

abstract class Controller_Auth_OpenId extends \Controller_Template {
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
		if(! $auth->perform_check()) {
			if(! $auth->login(Input::get_post('openid_identifier'))) {
				Response::redirect($controller.'/error');
			}
		}
		Response::redirect($controller.'/success');
	}

	/**
	 * Logout the user and redirect to the 'index' action.
	 */
	public function action_logout() {
		Auth::instance()->logout();
		Response::redirect($this->request->controller);
	}

	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="Abstract methods">

	abstract public function action_index();
	abstract public function action_success();
	abstract public function action_error();

	// </editor-fold>

}