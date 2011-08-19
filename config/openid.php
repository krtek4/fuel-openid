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

	'actions' => array(
		'success'	=> 'auth/success',
		'error'		=> 'auth/error',
		'logout'	=> 'auth',
	),
);