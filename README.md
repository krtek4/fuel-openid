# OpenID authentication driver for Fuel PHP

## Introduction

This package provides an [OpenID](http://openid.net/) authentication driver for [FuelPHP](http://fuelphp.com).

OpenID is an open standard to authenticate an user in a decentralized way. A consumer (your application) connects to a provider in order to authenticate the user.

You can entirely configure the database mapping to suits your needs, there's no mandatory table creation.

## Installation

1. Clone the repository, don't forget to use the `--recursive` option to downlad submodules : `git clone --recursive git://github.com/krtek4/fuel-openid.git`
2. Copy the content to the package directory (`fuel/packages/`)
3. Edit your application configuration file to add the openid package in `fuel/app/config/config.php`

You're all set ! You can use the sample controller and views or create new ones.

## Configuration

	'salt' => 'your own private salt',

The salt used to create a login hash to avoid session hijacking.

	'ax_required' => array('contact/email', 'namePerson/first', 'namePerson/last'),
	'ax_optional' => array('namePerson/friendly', 'birthDate', 'person/gender', 'contact/country/home'),

The fields the driver should ask to the OpenID provider on the first connection to create the new account in the database. If any of the required fields are missing, the new user must provide the missing fields.

	'table_name' => 'accounts',

The table to use in the database.

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

The mapping to use in the database for each field specified in `ax_required` and `ax_optional`. `identity` is the column used to store the OpenID Identity to associate with the account and `login_hash` is the hash created on the last login to avoid session hijacking.

## Usage

Writing in progress...

## In depth

More informations to come...
