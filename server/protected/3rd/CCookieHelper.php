<?php
/**
 * 表单cookie
 * Date: 2012-04-10 15:03
 */

Class CCookieHelper
{
	private static $_pre = 'CCookieHelper_';
	/**
	 * @static
	 * @param string $name
	 * @param int $expire
	 * @return string $value
	 */
	private static $_cookies = array();
	public static function create( $name , $expire = 300 )
	{
		$name  = self::$_pre . $name;
		if( isset(self::$_cookies[$name]) )
		{
			$cookie = self::$_cookies[$name];
			$value = $cookie->value;
		}
		else
		{
			$_time = time();
			$expire = $_time + $expire;
			$value = base64_encode( md5( $_time . mt_rand(1,9999) ) );
			$cookie = new CHttpCookie($name , $value );
			$cookie->expire = $expire;
			$cookie->httpOnly = true;
			Yii::app()->request->cookies[$name] = $cookie;
			self::$_cookies[$name] = $cookie;
		}
		return $value;
	}

	/**
	 * @static
	 * @param $name
	 * @param $value
	 * @return bool
	 */
	public static function valid( $name , $value )
	{
		$name  = self::$_pre . $name;
		$cookie = Yii::app()->request->cookies[$name];
		if( !$cookie )
		{
			//cookie超时
			return false;
		}
		if( !$value || $value != $cookie->value )
		{
			//cookie错误
			return false;
		}
		return true;
	}

	/**
	 * @static
	 * @param $name
	 * @return bool;
	 */
	public static function destory( $name )
	{
		$cookies = Yii::app()->request->getCookies();
		unset($cookies[self::$_pre . $name]);
		return true;
	}

}