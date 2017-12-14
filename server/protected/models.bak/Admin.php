<?php

/**
 */
class Admin
{
  public $errors = [];
  private function addError($key, $value)
  {
    $this->errors[$key] = $value;
  }

  /**
   * 登录
   * @param $username
   * @param $password
   * @return array|bool
   */
  public function login( $username, $password )
  {
    if(!$username || !$password)
    {
      $this->addError('id' , '帐号不存在');
      return false;
    }

    $row = Syscfg::getValue('adminAccount', true);

    if( !$row )
    {
      $this->addError('id' , '帐号或密码不正确1');
      return false;
    }

    if($row['username'] == $username && $row['password'] == $password)
    {

    }
    else
    {
      $this->addError('id' , '帐号或密码不正确2');
      return false;
    }

    $row['id'] = 1;

    $token = strtoupper( md5('admin_' . $row['username'].time()) );
    $this->activeUserLoginStatus($token, $row['id'] );

    unset($row['password']);

    return array_merge($row, [
      'token' => $token
    ]);
  }

  /*
   * 注销
   * */
  public function logout($token=null)
  {
    $id = self::getUserId($token);
    $this->delete_user_auth($id);
    return true;
  }

  /**
   * 激活用户登陆状态
   * @param $token
   * @param $id
   * @param int $expire
   * @return bool
   */
  private static function activeUserLoginStatus($token, $id, $expire = 86400)
  {
    if(!$token || !$id )
    {
      return false;
    }
    $cache = Yii::app()->cache;
    $cache->set('admin_uid_' . $token, $id, $expire);
    $cache->set('admin_expire_' . $id ,  time(), $expire);
    $cache->set('admin_token_' . $id, $token, $expire);
    return true;
  }


  /**
   * 删除用户的权限缓存,注销用户的登录状态
   * @param null $id
   */
  public function delete_user_auth($id)
  {
    if(!$id)
    {
      return false;
    }
    $cache = Yii::app()->cache;
    $token = $cache->get('admin_token_'.$id);
    if($token)
    {
      $cache->delete('admin_uid_' . $token);
    }
    $cache->delete('admin_token_'.$id);
    $cache->delete('admin_expire_'.$id );
  }

  /*
   * 用户的登录ID
   * */
  public static function getUserId($token=null)
  {
    return Yii::app()->cache->get('admin_uid_'. ($token ? $token : self::getToken()) );
  }

  /**
   * 活动状态
   * @static
   * @return bool
   */
  public static function isactive()
  {
    $token = self::getToken();
    if(!$token || strlen($token) !== 32)
    {
      return -1;
    }

    $uid = self::getUserId();
    if(!$uid)
    {
      return -1;
    }
    
    $cache = Yii::app()->cache;
    $expire = 86400;
    if(time() - $cache->get("admin_expire_{$uid}") > $expire)
    {
      return -1;
    }
    else
    {
      self::activeUserLoginStatus($token, $uid);
      return true;
    }
  }

  public static function getToken()
  {
    $token = Yii::app()->request->getParam('token') ?: CFuncHelper::getPhpInput('token');
    return $token;
  }





}
