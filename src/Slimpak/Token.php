<?php namespace Slimpak;

class Token  { 

	private static $token;
	private static $old_token;

	public function __construct()
	{
		if(isset($_SESSION['token']))
		{
          	static::$old_token = $_SESSION['token'];
        	}
	}

	private static function createToken()
	{
		return md5($_SERVER['REMOTE_ADDR'] . uniqid(mt_rand(), true));
	}

	public static function getToken()
	{
		static::$token = static::createToken();
		$_SESSION['token'] = static::$token;

		return static::$token;
	} 

	public static function validate()
    {
        return (isset($_SESSION['token']) == \Input::post('token'))  ? true : false;
    }
}