<?php
/**
 * @package    openid
 * @version    1.0
 * @license    GPLv3
 * @author     Gilles Meier
 * @link       http://github.com/krtek4/fuel-openid
 */

class Auth_Login_OpenID_Error {
	const USER_CANCEL = 1; // the user cancelled the authentication on the OpenID provider side.
	const INSUFICIENT_INFORMATION = 2; // the OpenID provider doesn't returned all the required informations
	const DATABASE_ERROR = 3; // a database error occured when getting or creating the user
	const PROVIDER_404 = 4; // unable to join the provider
	const UNKNOWN_ERROR = 100;
}

class Auth_Login_OpenID extends \Auth_Login_Driver {
	// <editor-fold defaultstate="collapsed" desc="Properties">

	/**
	 * @var array Our database mapping.
	 */
	private static $mapping = null;
	/**
	* @var string The table name
	*/
	private static $table_name = null;
	/**
	 * @var  LightOpenID  used to connect to OpenID provider.
	 */
	private static $openid = null;
	/**
	 * @var  Auth_Login_OpenID_Error  the code of the last error.
	 */
	protected $e_code = null;
	/**
	 * @var  Account the current logged user or null.
	 */
	private $user = null;
	//</editor-fold>

	public static function _init()
	{
		\Config::load('openid', true);
		static::$openid = new LightOpenID('localhost');
		static::$table_name = \Config::get('openid.table_name');
		static::$mapping = \Config::get('openid.mapping');
	}

	// <editor-fold defaultstate="collapsed" desc="Private methods">

	//</editor-fold>

	// <editor-fold defaultstate="collapsed" desc="Protected methods">

	/**
	 * Validate the user given in paramaters. If the user doesn't exist in the database a new row is
	 * created if enough informations are supplied and the data are stored in the user attribute.
	 *
	 * Otherwise, the Auth_Login_OpenID_Error::INSUFICIENT_INFORMATION is returned and the current
	 * informations are stored in the session as openid_identity and openid_data.
	 *
	 * @return  bool  the user validity (the error code is already set)
	 */
	final protected function validate_user($openid_identity, $openid_data = array()) {
		$user = $this->get_user($openid_identity);

		// user is not in DB
		if(! $user) {
			$diff = array_diff(\Config::get('openid.ax_required'), array_keys($openid_data));
			if(! empty($diff)) {
				$this->e_code = Auth_Login_OpenID_Error::INSUFICIENT_INFORMATION;
				\Session::set('openid_identity', $openid_identity);
				\Session::set('openid_data', $openid_data);
				return false;
			}
			if(! $this->create_user($openid_identity, $openid_data)) {
				// create_user() sets his own error code.
				return false;
			};
		}
		return true;
	}

	/**
	 * Return the database mapping for an AX attribute
	 *
	 * @param string AX attribute name
	 * @return string database column to use
	 */
	final protected function get_mapping($key) {
		return static::$mapping[$key];
	}

	/**
	 * Get the user in the database
	 *
	 * @return  mixed  false if no user found, the user informations otherwise.
	 */
	final protected function get_user($openid_identity) {
		$user =\DB::select()	->where($this->get_mapping('identity'), '=', $openid_identity)
						->from(static::$table_name)
						->as_object()
						->execute();
		if($user->count() == 0) {
			return false;
		}
		$user = $user->as_array();
		return $user[0];
	}

	/**
	 * Create the user in the database. Set the Auth_Login_OpenID_Error::DATABASE_ERROR
	 * error code if something went wrong.
	 *
	 * @return  bool  false if an error occured , true if everything went fine.
	 */
	final protected function create_user($openid_identity, $openid_data) {
		$user = array(
			$this->get_mapping('identity') => $openid_identity
		);
		foreach($openid_data as $k => $v) {
			$mapping = $this->get_mapping($k);
			if(! empty($mapping)) {
				$user[$mapping] = $v;
			}
		}
		$result = \DB::insert(static::$table_name)->set($user)->execute();
		if(! $result) {
			$this->e_code = Auth_Login_OpenID_Error::DATABASE_ERROR;
			return false;
		}
		return true;
	}

	/**
	 * Get user data based on the mapping defined in the config
	 *
	 * @param string the key
	 * @return  mixed user data
	 */
	final protected function get_user_data($key) {
		if(is_null($this->user)) {
			return null;
		}
		$map = $this->get_mapping($key);
		return $this->user->$map;
	}

	/**
	 * Set user data based on the mapping defined in the config
	 *
	 * @param string the key
	 * @param mixed the value
	 */
	final protected function set_user_data($key, $value) {
		if(is_null($this->user)) {
			return;
		}
		$map = $this->get_mapping($key);
		$this->user->$map = $value;
	}

	/**
	 * Creates a temporary hash that will validate the current login. (borrowed from SimpleAuth)
	 *
	 * @return  string
	 */
	protected function create_login_hash() {
		$login_hash = sha1(\Config::get('openid.salt').\Date::factory()->get_timestamp());
		\DB::update(static::$table_name)
			->value('login_hash', $login_hash)
			->where('id', '=', $this->user->id)
			->execute();
		$this->set_user_data('login_hash', $login_hash);
		return $login_hash;
	}

	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="Public methods">

	/**
	 * @return  Auth_Login_OpenID_Error the last error code.
	 */
	public function error_code() {
		return $this->e_code;
	}

	public function get_action($type) {
		$actions = \Config::get('openid.actions');
		return $actions[$type];
	}

	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="Overriden abstract methods">

	/**
	* Perform the actual login check. Data in session are resetted if the identity
	 * of the current user can't be ascertained.
	*
	* @return  bool
	*/
	public function perform_check() {
		$identity = \Session::get('openid_identity');
		$login_hash  = \Session::get('login_hash');

		if (is_null($this->user)) {
			$this->user = $this->get_user($identity);
		}

		if ($this->user and $this->get_user_data('login_hash') === $login_hash) {
			return true;
		}

		$this->logout();
		return false;
	}

	/**
	 * Log the user using OpenID. This is a two step method :
	 *
	 * 1° The browser is redirected to the OpenID provider which identifies the user and then redirect to
	 * the login page
	 * 2° The user identity is validated and the login process is completed.
	 *
	 * If false is returned, an error during the login process occured, you can check the status by calling
	 * error_code()
	 *
	 * If the the connection to the OpenID provider was not possible, the Auth_Login_OpenID_Error::PROVIDER_404
	 * error code is set.
	 *
	 *  If the user cancelled the authentication with the provider, the Auth_Login_OpenID_Error::USER_CANCEL
	 * error code is set.
	 *
	 * If the OpenID provider doesn't return all the required AX data (see the configuration), the
	 * Auth_Login_OpenID_Error::INSUFICIENT_INFORMATION; is set.
	 *
	 * If there's a database error, the Auth_Login_OpenID_Error::DATABASE_ERROR error code is set.
	 *
	 * If the error source is unknown, the Auth_Login_OpenID_Error::UNKNOWN_ERROR error code is set.
	 *
	 * @return  bool  whether login succeeded
	 */
	public function login($openid_identity = '') {
		if(! static::$openid->mode) {
			$openid_identity = trim($openid_identity) ?: trim(\Input::post('openid_identifier'));
			static::$openid->identity = $openid_identity;
			static::$openid->required = \Config::get('openid.ax_required');
			static::$openid->optional = \Config::get('openid.ax_optional');
			static::$openid->returnUrl = Uri::create('authopenid/login');

			try {
				$providerurl = static::$openid->authUrl();
			} catch(ErrorException $e) {
				$this->e_code = Auth_Login_OpenID_Error::PROVIDER_404;
				return false;
			}

			header('Location: ' . $providerurl);
			exit(); // we're redirection to the OpenID provider
		}

		if(static::$openid->mode == 'cancel') {
			$this->e_code = Auth_Login_OpenID_Error::USER_CANCEL;
		} else if (static::$openid->validate()) {
			if($this->validate_user(static::$openid->identity, static::$openid->getAttributes())) {
				$this->user = $this->get_user( static::$openid->identity);
				\Session::set('openid_identity', static::$openid->identity);
				\Session::set('login_hash', $this->create_login_hash());
				return true;
			}
			// validate_user set his own error code.
		} else {
			$this->e_code = Auth_Login_OpenID_Error::UNKNOWN_ERROR;
		}
		\Session::delete('openid_identity');
		return false;
	}

	/**
	* Logout method. Reset all data in the session.
	*/
	public function logout() {
		$this->user = null;
		\Session::delete('openid_identity');
		\Session::delete('login_hash');
		return true;
	}

	/**
	* Get User Identifier of the current logged in user
	* in the form: array(driver_id, user_id)
	*
	* @return  array
	*/
	public function get_user_id() {
		return array('openid', $this->user->id);
	}

	/**
	* Get User Groups of the current logged in user
	* in the form: array(array(driver_id, group_id), array(driver_id, group_id), etc)
	*
	* @return  array
	*/
	public function get_groups() {
		return array();
	}

	/**
	* Get emailaddress of the current logged in user
	*
	* @return  string
	*/

	public function get_email() {
		return $this->get_user_data('contact/email');
	}

	/**
	* Get screen name of the current logged in user
	*
	* @return  string
	*/
	public function get_screen_name() {
		$screenname = $this->get_user_data('namePerson/friendly');
		if(empty($screenname)) {
			$screenname = $this->get_user_data('namePerson/first').' '.$this->get_user_data('namePerson/last');
		}
		return $screenname;
	}

	// </editor-fold>
}