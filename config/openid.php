<?php
/**
 * @package    openid
 * @version    1.0
 * @license    GPLv3
 * @author     Gilles Meier
 * @link       http://github.com/krtek4/fuel-openid
 */

return array(
	// the salt for the login hash
	'salt' => 'your own private salt',

	// AX field to ask to the OpenID provider
	'ax_required' => array('contact/email', 'namePerson/first', 'namePerson/last'),
	'ax_optional' => array('namePerson/friendly', 'birthDate', 'person/gender', 'contact/country/home'),

	// the table where all the data must be saved
	'table_name' => 'accounts',
	// the database mapping for each AX fields.
	// identity is the OpenID Identity and login_hash a temporary login hash used to avoid session hijacking.
	'mapping' => array(
		'identity'				=> 'identity',
		'login_hash'			=> 'login_hash',
		'contact/email'			=> 'email',
		'namePerson/friendly'		=> 'nickname',
		'namePerson/first'		=> 'firstname',
		'namePerson/last'		=> 'lastname',
		'birthDate'				=> 'birthdate',
		'person/gender'			=> 'gender',
		'contact/country/home'	=> 'country',
	),

	// The actions we must redirect to when the login is completed with a
	// a success or an error or after a logout.
	// The 'login' hash is more particular, it is the action sent to the provider
	// has a return URL. Normally you won't have to modify this one.
	'actions' => array(
		'login'		=> 'authopenid/login',
		'success'	=> 'auth/success',
		'error'		=> 'auth/error',
		'logout'	=> 'auth',
	),

	// Is the file action activated on the controller ? (see the doc about
	// openid-selector for more details)
	'use_file_action' => true,
	// the path to directory containing images for openid-selector. This path will
	// be access trough javascript, so it must be relative to the site base url.
	// if null, the file action must be activated.
	'openid_selector_img' => null,
);
