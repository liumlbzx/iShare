<?php

/**
 * This is the model class for table "msg".
 *
 * The followings are the available columns in table 'msg':
 * @property string $id
 * @property integer $module
 * @property integer $type
 * @property string $msg
 * @property string $sendUid
 * @property string $sendName
 * @property string $receiveUid
 * @property string $receiveName
 * @property string $sendTime
 * @property integer $status
 */
class Msg extends CActiveRecord
{
  const MODULE = [
    1 => '用户',
    2 => '管理中心',
  ];
  const MODULE_USER = 1;
  const MODULE_ADMIN = 2;

  const TYPE = [
    1 => '普通消息',
    2 => '重要消息',
  ];
  const TYPE_NORMAL = 1;
  const TYPE_IMPORTANT = 2;

  const STATUS = [
    1 => '已发送',
    2 => '已阅读',
  ];
  const STATUS_SEND = 1;
  const STATUS_READ = 2;



  /**
   * 添加一条站内信
   * @param $module
   * @param $type
   * @param $sendUid
   * @param $sendName
   * @param $receiveUid
   * @param $receiveName
   * @return bool|string
   */
  public static function addMsg($module, $type, $sendUid, $sendName, $receiveUid, $receiveName, $msg)
  {
    $data = new self('addMsg');
    $attr = [
      'module' => $module,
      'type' => $type,
      'msg' => $msg,
      'sendUid' => $sendUid,
      'sendName' => $sendName,
      'receiveUid' => $receiveUid,
      'receiveName' => $receiveName,
      'sendTime' => time(),
      'status' => 0
    ];
    $data->attributes = $attr;
    if($data->save())
    {
      MsgRead::addRead($module, $receiveUid, $data->id);
      return true;
    }
    else
    {
      $errors = $data->errors;
      return reset($errors)[0] ?: '未知错误';
    }
  }

  /**
   * 阅读一条消息
   * @param $id
   * @return bool
   */
  public static function readMsg($id)
  {
    $data = self::model()->findByPk($id);
    if(!$data)
    {
      return false;
    }
    MsgRead::setRead($data['module'], $data['receiveUid'], $data['id']);
    return true;
  }

  private $_msgReadArr = [];
  public function afterFind()
  {
    $receiveUid = $this->receiveUid;
    $msgReadKey = $this->module.'_'.$receiveUid;
    if( !isset($this->_msgReadArr[$msgReadKey]) )
    {
      $this->_msgReadArr[$msgReadKey] = MsgRead::getReadArr($this->module, $receiveUid);
    }

    if(in_array($this->id, $this->_msgReadArr[$msgReadKey]))
    {
      $this->status = Msg::STATUS_SEND;
    }
    else
    {
      $this->status = Msg::STATUS_READ;
    }

  }

  /**
   * @return string the associated database table name
   */
  public function tableName()
  {
    return 'msg';
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
        'module,type,sendUid,sendName,receiveUid,receiveName,sendTime,msg',
        'required',
      ],

      [
        'sendName,receiveName',
        'filter', 'filter' => function($str){
        return CHtml::encode($str);
      }
      ],

      [
        'module',
        'in', 'range' => array_keys(self::MODULE)
      ],
      [
        'type',
        'in', 'range' => array_keys(self::TYPE)
      ],

      array('module, type, status', 'numerical', 'integerOnly'=>true),
      array('msg', 'length', 'max'=>5000),
      array('sendUid, sendTime', 'length', 'max'=>10),
      array('sendName, receiveName', 'length', 'max'=>50),
      array('receiveUid', 'length', 'max'=>11),
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
      'module' => '模块',
      'type' => '消息分类',
      'msg' => '内容',
      'sendUid' => '发送者',
      'sendName' => '发送者名称',
      'receiveUid' => '接收方',
      'receiveName' => '接收方名称',
      'sendTime' => '发送时间',
      'status' => '状态',
    );
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return Msg the static model class
   */
  public static function model($className=__CLASS__)
  {
    return parent::model($className);
  }
}
