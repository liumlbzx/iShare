<?php

/**
 * This is the model class for table "sms".
 *
 * The followings are the available columns in table 'sms':
 * @property string $id
 * @property string $receiver
 * @property string $msg
 * @property string $createtime
 * @property integer $type
 */
class Sms extends CActiveRecord
{

  const TYPE = [
    101 => '夺宝中奖通知',
  ];
  const TYPE_LOTTERY = 101;

  //短信模版
  const SMSTPL = [
    self::TYPE_LOTTERY => 'SMS_76565060'
  ];

  public function beforeValidate()
  {
    if( parent::beforeValidate() )
    {
      $this->receiver = CHtml::encode($this->receiver);
      $this->msg = CHtml::encode($this->msg);
      $this->createtime = intval($this->createtime);
      $this->type = intval($this->type);
      return true;
    }
    return false;
  }


  /**
   * 校验短信码
   * @param $code
   * @param $phone
   * @param $type
   * @return bool
   */
  public static function validCode($code, $phone, $type)
  {
    if(!preg_match('/^1\d{10}$/', $phone) || !$code)
    {
      return false;
    }
    $cache = Yii::app()->cache;
    $cacheKey = "sms_{$type}_{$phone}";
    $value = $cache->get($cacheKey);
    return $value == $code;
  }

  /**
   * 删除短信码
   * @param $phone
   * @param $type
   * @return bool
   */
  public static function clearCode($phone, $type)
  {
    if(!$phone)
    {
      return false;
    }
    $cache = Yii::app()->cache;
    $cacheKey = "sms_{$type}_{$phone}";
    return $cache->delete($cacheKey);
  }

  /**
   * 发送短信码
   * @param $phone
   * @param $type
   * @param $expire
   * @return bool
   */
  public static function sendCode($phone, $type, $expire=300)
  {
    if( !preg_match('/^1\d{10}$/', $phone) )
    {
      return false;
    }
    $code = mt_rand(100000,999999);
    $cache = Yii::app()->cache;
    $cacheKey = "sms_{$type}_{$phone}";
    $cache->set($cacheKey, $code, $expire);

    if($expire<60)
    {
      $minute = "{$expire}秒";
    }
    else
    {
      $minute = intval($expire/60) . "分";
    }

    $msg = self::TYPE[$type]."，校验码是{$code}，".$minute."钟后失效。";

    require_once Yii::getPathOfAlias('application.3rd').'/Alisms.php';
    $aliConfig = Yii::app()->params['api']['alisms'];

    $aliObj = new Alisms($aliConfig['key'], $aliConfig['secret'], $aliConfig['com']);
    $sendRet = $aliObj->sendSms(self::SMSTPL[$type], $phone, [
      'number'=> (string)$code
    ]);

    self::insertLog($phone, $msg.'|'.$sendRet, $type);

    return $code;
  }

  /**
   * 发送短信
   * @param $phone
   * @param $type
   * @param $params
   * @return mixed
   */
  public static function sendSms($phone, $type, $params=[])
  {
    if( !preg_match('/^1\d{10}$/', $phone) )
    {
      return false;
    }

    $tplID = self::SMSTPL[$type];
    $msg = $tplID . '|';
    if(!$tplID || !$msg)
    {
      return false; //没有短信模版
    }

    foreach ($params as $k=>$v)
    {
      $msg .= "{$k}=>{$v}|";
    }

    require_once Yii::getPathOfAlias('application.3rd').'/Alisms.php';
    $aliConfig = Yii::app()->params['api']['alisms'];

    $aliObj = new Alisms($aliConfig['key'], $aliConfig['secret'], $aliConfig['com']);
    $aliObj->sendSms($tplID, $phone, $params);

    return self::insertLog($phone, $msg, $type);
  }

  public static function insertLog($receiver, $msg, $type)
  {
    $data = new self();
    $data->attributes = [
      'receiver' => $receiver,
      'msg' => $msg,
      'createtime' => time(),
      'type' => $type
    ];
    return $data->save();
  }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sms';
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
        'msg',
        'filter', 'filter' => function($str){
          return CHtml::encode($str);
        }
      ],

      [
        'type',
        'in', 'range' => array_keys(self::TYPE)
      ],

      array('receiver,msg,createtime,type', 'required'),
      array('type', 'numerical', 'integerOnly'=>true),
			array('receiver', 'length', 'max'=>11),
			array('msg', 'length', 'max'=>200),
			array('createtime', 'length', 'max'=>10),
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
			'receiver' => '接收方',
			'msg' => '短信内容',
			'createtime' => '发送时间',
			'type' => '短信类型',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Sms the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
