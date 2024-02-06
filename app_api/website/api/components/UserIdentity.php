<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * 
	 * @return boolean whether authentication succeeds.
	 */
	public $userid;
	public $role;
	public $username;
	public function authenticate() {
		$pwd = md5 ( $this->password );
		$user = User::model ()->find ( "username = '{$this->username}' and password = '$pwd'" );
		if (empty ( $user )) {
			return false;
		} else {
			$this->userid = $user->id;
			$this->role = $user->role;
			$this->username = $user->username;
			$this->setState ( '$userInfo', $user );
			return true;
		}
	}
}