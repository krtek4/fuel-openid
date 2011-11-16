## I don't plane to update this project anymore

I added the OpenID support to https://github.com/philsturgeon/fuel-ninjauth and I will use this package now for my own development.

However, I will continue to merge pull requests as they come and apply patch if you send one.

# OpenID authentication driver for Fuel PHP

## Introduction

This package provides an [OpenID](http://openid.net/) authentication driver for [FuelPHP](http://fuelphp.com).

OpenID is an open standard to authenticate an user in a decentralized way. A consumer (your application) connects to a provider in order to authenticate the user.

You can entirely configure the database mapping to suits your needs, there's no mandatory table creation.

## Installation

1. Clone the repository, don't forget to use the `--recursive` option to downlad submodules : `git clone --recursive git://github.com/krtek4/fuel-openid.git`
2. Copy the content to the package directory (`fuel/packages/`)
3. Edit your application configuration file to add the openid package in `fuel/app/config/config.php`

You're all set !

If you forgot to use the `--recursive` option when cloning, you can do `git submodule update --init` to download the submodules.

## Usage

There's three parts composing this packages : the driver itself, a basic controller to log the user in and out, a helper for an [OpenID Selector](http://code.google.com/p/openid-selector/).

### The driver

The driver has all the logic to redirect the browser to the OpenID provided and log the user in when the provider redirect on your site.

When you call the `login()` method the first time (ie: the URL parameters contain no OpenID modes), a redirection to the provider corresponding to the OpenID identity is made. The provided then authenticate the user and redirects it back to the 'authopenid/login' action of your website. Now, `login()` is called a second time with all the informations needed to ensure that the user is who he claims to be !

If the user already exists in the database, he is logged in and the method returns `true`. Otherwise, the user is created in the database (all the field mappings can be configured, see below) and then the login proceed like described.

If the provided didn't return all the mandatory fields, an error is raised. Like every other possible errors, the method returns `false` and you can then access the error code with the `error_code()` method.

### The controller

It is a really basic controller which as the purpose of logging the user in or out. No output is ever made, once the (dis)connexion is completed, the user is redirected to another action defined in the configuration.

The default is to redirect the user to a controller named `auth` which as the `index`, `success`, `error` actions defined. A sample controller implementing the actions can be found in your download.

The controller also defines a `list` action, but this will be discussed in the next section.

### The OpenID selector

To simplify the use of an OpenID authentication form for the user, a provided selector is included in the package. You can add it to your website with the help of the `OpenID_Selector` class. Just follow these steps :

 1. include the return value of `get_form()` on your site
 2. include the css files returned by `get_css()`
 3. include the javascript files returned by `get_js()`
 4. execute the javascript snippet returned by `get_inline_js()`

At the time being, this will only work with jQuery loaded. I plan to add the support for Mootools and Prototype soon.

Like described in the steps above, the selector need to have some css and javascript files loaded in order to work. Since there's no easy way to provide such files in a fuel packages, I offer you two solutions :

1. I wrote a `file` action on the controller which can transmit file contents to the browser. This is the easiest way, it should work out of the box with the default configuration in most case.
2. If you want to use an Asset management framework or for any other reasons, deactivate the `file` action in the configuration. After that, the methods from `OpenID_Selector` will return the filepath relative to the package directory and you'll have to make them visible from the web yourself.

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

	'actions' => array(
		'login'		=> 'authopenid/login',
		'success'	=> 'auth/success',
		'error'		=> 'auth/error',
		'logout'	=> 'auth',
	),

The actions the Authopenid controller must redirect to once the user is logged in with success, an error occured or the logout is done.

The 'login' action is particular, it is the action sent to the provider has a return URL. Normally you won't have to modify this one if you use the provided controller.

	'use_file_action' => true,

If the 'file' action must be activated on the controller or not. You can look at the 'Usage' section for more details.

	'openid_selector_img' => null,

The path relative to the website root for the images of the openid-selector. The 'null' value means the image will be loaded through the 'file' action on the controller.

## TODO

* Implements the drivers for the groups and the ACL.
* Add the support for Mootools and Prototype for OpenID-Selector.
* Improve the sample controller.
* Add something about the error codes in the documentation.

## In depth

More informations to come...
