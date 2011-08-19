<?php
/**
 * @package    openid
 * @version    1.0
 * @license    GPLv3
 * @author     Gilles Meier
 * @link       http://github.com/krtek4/fuel-openid
 */

class Controller_Auth extends Controller_Public {
	public function action_error() {
		$this->template->title = 'Error';
		$this->template->content = 'An error occured during the login process.';
	}

	public function action_index() {
		$selector = OpenID_Selector::instance();
		$this->template->title = 'Log In';
		$form = $selector->get_form();
		$this->template->content = $form;

		$this->template->css = $selector->get_css();
		$this->template->js = $selector->get_js();

		$this->template->auto_encode(false);
		$this->template->inline_js = $selector->get_inline_js();
		$this->template->auto_encode(true);
	}

	public function action_success() {
		$this->template->title = 'Sucess';
		$this->template->content = 'You are now logged in as '.Auth::instance()->get_screen_name().'.';
	}

	public function action_logout() {
		$this->response->redirect(Uri::create('authopenid/logout'));
	}
}
