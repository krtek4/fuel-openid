<?php
/**
 * @package    openid
 * @version    1.0
 * @license    GPLv3
 * @author     Gilles Meier
 * @link       http://github.com/krtek4/fuel-openid
 */

class Controller_OpenIdAuth extends \Controller_Template {
	private function login_form($data = array()) {
		$data['url'] = Uri::create('auth/login');
		$this->template->title = "Auth";
		$this->template->content = View::factory('auth/index', $data);
	}

	// <editor-fold defaultstate="collapsed" desc="Actions">

	public function action_index() {
		$this->login_form();
	}

	public function action_login() {
		$auth = Auth::instance();
		$data = array();
		if($auth->perform_check()) {
			$data['message'] = 'You are already logged in.';
		} else {
			$status = $auth->login();
			if(! $status) {
				$data = array('error' => 'Something went wrong : '.$auth->error_code());
				$this->login_form($data);
			} else {
				$data['message'] = 'Login successful.';
			}
		}
		$this->template->title = "Login sucess";
		$this->template->content = View::factory('auth/success', $data);
	}

	public function action_logout() {
		Auth::instance()->logout();
		Response::redirect('auth');
	}

	public function action_success() {
		if(Auth::instance()->perform_check()) {
			$this->success_template();
		} else {
			Response::redirect(Uri::create('auth'));
		}
	}

	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="Abstract methods">

	abstract protected function form_template();
	abstract protected function success_template();
	abstract protected function error_template();
	abstract protected function logout_template();

	// </editor-fold>

}