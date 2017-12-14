<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $wechatId
 * @property string $nickname
 * @property string $mobile
 * @property string $face
 * @property integer $sex
 * @property string $birthday
 * @property string $regtime
 * @property string $logintime
 * @property string $info
 * @property integer $status
 * @property string $jinbi
 * @property string $jinyuan
 * @property integer $jifen
 * @property integer $xinyong
 */
class User extends CActiveRecord
{
  const PWD_MD5 = 'jp)2dz:z?sd'; //md5混淆字符串,禁止修改

  const SEX = [
    1 => '男',
    2 => '女',
    99 => '其他'
  ];

  const STATUS = [
    1 => '正常',
    2 => '禁用',
  ];
  const STATUS_OPEN = 1;
  const STATUS_CLOSE = 2;





  /**
   * 登录
   * @param $username
   * @param $password
   * @return array|bool
   */
  public function login( $username, $password )
  {
    $username = trim($username);
    $password = trim($password);
    if(!$username || !$password)
    {
      $this->addError('id' , '帐号或密码不能为空');
      return false;
    }
    $row = $this->find([
      'condition' => 'username=:username',
      'params' =>  [
        ':username' => $username
      ],
    ]);
    if( !$row )
    {
      //帐号不存在
      $this->addError('id' , '帐号不存在');
      return false;
    }
    elseif($password !== true && $row['password'] != md5($password.self::PWD_MD5) )
    {
      //密码不正确
      $this->addError('id' , '密码不正确');
      return false;
    }
    elseif( $row['status'] != self::STATUS_OPEN )
    {
      $this->addError('status' , '被禁止登录，请联系客服');
      return false;
    }

    $updateArr = [
      'logintime' => time(),
      'info' => array_merge($row['info'], [
        'loginTime' => time(),
        'loginIp' => Yii::app()->request->getUserHostAddress()
      ])
    ];
    $row->attributes = $updateArr;
    $row->save(true, array_keys($updateArr));

    $token = strtoupper( md5(self::PWD_MD5 . $row['username'].time() ) );
    $this->activeLoginStatus($token, $row['id'] );

    $retAttrs = $row->attributes;
    unset($retAttrs['password']);

    return array_merge($retAttrs, [
      'token' => $token
    ]);
  }

  /**
   * 微信登录
   * @param $wechatId
   * @return array|bool
   */
  public function wechatLogin( $wechatId )
  {
    if($wechatId)
    {
      $row = $this->find(array(
        'select' => 'username',
        'condition' => 'wechatId=:wechatId',
        'params' =>  array(
          ':wechatId' => $wechatId ,
        ),
      ));
      if( !$row )
      {
        $this->addError('id' , '微信用户未绑定');
        return false;
      }
      $loginRet = $this->login($row['username'], true);
      if($loginRet===false)
      {
        return false;
      }
      else
      {
        return $loginRet;
      }
    }
    else
    {
      $this->addError('wechatId', '微信标识为空');
      return false;
    }
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
   * 获取用户信息
   * @param null $id
   * @param null $colmuns
   * @return static
   */
  public static function getUser($id=null , $colmuns = null)
  {
    if($id === null)
    {
      $id = self::getUserId();
    }
    $model = self::model();
    if( is_array($colmuns) )
    {
      $colmuns[] = $model->tableSchema->primaryKey;
    }
    elseif( is_string($colmuns) )
    {
      $colmuns .= ',' . $model->tableSchema->primaryKey;
    }
    if($colmuns === null)
    {
      $colmuns = "";
      foreach($model->getTableSchema()->columnNames as $v)
      {
        $colmuns .= ",`{$v}`";
      }
      $colmuns = trim($colmuns , ',');
    }

    return $model->findByPk($id , array(
      'select' => $colmuns
    ));
  }

  /**
   * 激活用户登陆状态
   * @param $token
   * @param $id
   * @param int $expire
   * @return bool
   */
  private static function activeLoginStatus($token, $id, $expire = null)
  {
    if(!$token || !$id )
    {
      return false;
    }
    if(!$expire)
    {
      $expire = Yii::app()->params['safe']['user_expire'];
    }
    $cache = Yii::app()->cache;
    $cache->set('user_uid_' . $token, $id, $expire);
    $cache->set('user_expire_' . $id ,  time(), $expire);
    $cache->set('user_token_' . $id, $token, $expire);
    return true;
  }


  /**
   * 删除用户的权限缓存,注销用户的登录状态
   * @param null $id
   * @return bool
   */
  public function delete_user_auth($id)
  {
    if(!$id)
    {
      return false;
    }
    $cache = Yii::app()->cache;
    $token = $cache->get('user_token_'.$id);
    if($token)
    {
      $cache->delete('user_uid_' . $token);
    }
    $cache->delete('user_token_'.$id);
    $cache->delete('user_expire_'.$id );
    return true;
  }

  /*
   * 用户的登录ID
   * */
  public static function getUserId($token=null)
  {
    return Yii::app()->cache->get('user_uid_'. ($token ? $token : self::getToken()) );
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
      return -2;
    }

    $cache = Yii::app()->cache;
    $expire = Yii::app()->params['safe']['user_expire'];
    if(time() - $cache->get("user_expire_{$uid}") > $expire)
    {
      return -3;
    }
    else
    {
      self::activeLoginStatus($token, $uid);
      return true;
    }
  }

  /**
   * 获取用户的登录凭证
   * @return mixed|null
   */
  public static function getToken()
  {
    $token = Yii::app()->request->getParam('token') ?: CFuncHelper::getPhpInput('token');
    return $token;
  }










  public function beforeValidate()
  {
    if( parent::beforeValidate() )
    {
      if( is_array($this->info))
      {
        $cArr = $this->info;
        $this->info = CFuncHelper::secJSON($cArr);
      }
      $this->face = CFuncHelper::getUploadVirtulUrl($this->face);
      return true;
    }
    return false;
  }

  public function afterFind()
  {
    $cArr = json_decode($this->info,1) ?: [];
    $this->info = $cArr ?: [];
    if($this->face === 'wechat_face')
    {
      $cache = Yii::app()->cache;
      $cacheKey = 'wechat_face_' . $this->wechatId;
      $face = $cache->get($cacheKey);
      if(!$face)
      {
        require_once Yii::getPathOfAlias('application.3rd').'/Wechat.php';
        $wechat = new Wechat();
        $wechatUserInfo = $wechat->getUserInfo($this->wechatId);
        if($wechatUserInfo && isset($wechatUserInfo['headimgurl']) && $wechatUserInfo['headimgurl'])
        {
          $face = $wechatUserInfo['headimgurl'];
        }
        else
        {
          $face = CFuncHelper::getUploadRealUrl('{URL}../assets/img/face.png');
        }
        $cache->set($cacheKey, $face, 3600*6); //头像地址缓存两个小时
      }
      $this->face = $face;
    }
    else
    {
      $this->face = CFuncHelper::getUploadRealUrl($this->face);
    }
    parent::afterFind();
  }

  public function beforeSave()
  {
    if(parent::beforeSave())
    {
      if( $this->isNewRecord )
      {
        //如果是新记录
        if( $this->username )
        {
          $count = $this->count('username = :q1' , array(
            ':q1' => $this->username
          ));
          if( $count )
          {
            $this->addError('username' , $this->getAttributeLabel('username') . '已被注册');
            return false;
          }
        }
        if( $this->wechatId )
        {
          $count = $this->count('wechatId = :q1' , array(
            ':q1' => $this->wechatId
          ));
          if( $count )
          {
            $this->addError('wechatId' , $this->getAttributeLabel('wechatId') . '已被使用');
            return false;
          }
        }
      }
      else
      {
        if($this->username && $this->id)
        {
          $count = $this->count('username = :q1 and id <> :q2' , array(
            ':q1' => $this->username ,
            ':q2' => $this->id
          ));
          if( $count )
          {
            $this->addError('username' , $this->getAttributeLabel('username') . '已被注册');
            return false;
          }
        }
        if($this->wechatId && $this->id)
        {
          $count = $this->count('wechatId = :q1 and id <> :q2' , array(
            ':q1' => $this->wechatId ,
            ':q2' => $this->id
          ));
          if( $count )
          {
            $this->addError('wechatId' , $this->getAttributeLabel('wechatId') . '已被使用');
            return false;
          }
        }
      }

      return true;
    }
    else
    {
      return false;
    }
  }

  public function beforeDelete()
  {
    if(parent::beforeDelete())
    {
      //不删除帐号，只是标识为删除状态
      $this->updateByPk($this->id , array(
        'status' => self::STATUS_CLOSE
      ));
      $this->delete_user_auth($this->id);
      return false;
    }
    return false;
  }






  /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

  public function primaryKey()
  {
    return 'id';
  }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
      [
        'nickname,mobile,sex,birthday',
        'required',
        'on' => 'userEdit'
      ],

      [
        'username,password,wechatId,status,regtime',
        'required',
        'on' => 'register'
      ],

      [
        'password',
        'length', 'is' => 32,
        'on' => 'register'
      ],

      [
        'mobile',
        'pattern' => '/^1\d{10}$/',
        'on' => 'editMobile'
      ],

      [
        'status',
        'in', 'range' => array_keys(self::STATUS)
      ],

      [
        'sex',
        'in', 'range' => array_keys(self::SEX)
      ],

      [
        'username,nickname,face,wechatId,birthday',
        'filter',
        'filter' => function($str){
          return CHtml::encode($str);
        }
      ],


			array('birthday', 'required'),
			array('sex, status, jifen, xinyong', 'numerical', 'integerOnly'=>true),
			array('username, wechatId, nickname', 'length', 'max'=>50),
			array('password', 'length', 'max'=>32),
			array('mobile', 'length', 'max'=>11),
			array('face', 'length', 'max'=>200),
			array('regtime, logintime, jinbi, jinyuan', 'length', 'max'=>10),
			array('info', 'length', 'max'=>5000),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => '帐号',
			'password' => '密码',
			'wechatId' => '微信openId',
			'nickname' => '昵称',
			'mobile' => '手机号码',
			'face' => '头像图片',
			'sex' => '性别',
			'birthday' => '出生',
			'regtime' => '注册时间',
			'logintime' => '登录时间',
			'info' => '详细信息：注册时间、注册ip、登录时间、登录ip、国家省市区、教育、收入等信息',
			'status' => '帐号状态:1正常2禁用',
			'jinbi' => '金币',
			'jinyuan' => '元宝',
			'jifen' => '积分',
			'xinyong' => '信用',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
