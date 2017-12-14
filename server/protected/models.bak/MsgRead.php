<?php

/**
 * This is the model class for table "msg_read".
 *
 * The followings are the available columns in table 'msg_read':
 * @property string $id
 * @property integer $module
 * @property string $uid
 * @property string $read
 */
class MsgRead extends CActiveRecord
{
  const MODULE = []; //MODULE的定义映射为Msg模型的MODULE

  /**
   * 读取未读的消息数组
   * @param $module
   * @param $uid
   * @return array
   */
  public static function getReadArr($module, $uid)
  {
    $row = MsgRead::model()->find(
      'module=:module AND uid=:uid',
      [
        ':module' => $module,
        ':uid' => $uid
      ]
    );

    if(!$row)
    {
      return [];
    }
    else
    {
      return $row['read'];
    }
  }

  /**
   * 添加一条未读信息量
   * @param $module
   * @param $uid
   * @param $msgId
   * @return bool
   */
  public static function addRead($module, $uid, $msgId)
  {
    if(!$module || !$uid || !$msgId)
    {
      return false;
    }
    $row = self::model()->find(
      'module=:module AND uid=:uid',
      [
        ':module' => $module,
        ':uid' => $uid
      ]
    );
    if(!$row)
    {
      $row = new self();
      $attr = [
        'module' => $module,
        'uid' => $uid,
        'read' => [$msgId]
      ];
      $row->attributes = $attr;
      $row->save(true, array_keys($attr));
    }
    else
    {
      $msgIds = $row['read'];
      array_push($msgIds, $msgId);
      $attr = [
        'read' => $msgIds
      ];
      $row->attributes = $attr;
      $row->save(true, array_keys($attr));
    }
    return true;
  }

  /**
   * 阅读一条未读信息量
   * @param $module
   * @param $uid
   * @param $msgId
   * @return bool
   */
  public static function setRead($module, $uid, $msgId)
  {
    if(!$module || !$uid || !$msgId)
    {
      return false;
    }
    $row = self::model()->find(
      'module=:module AND uid=:uid',
      [
        ':module' => $module,
        ':uid' => $uid
      ]
    );
    if(!$row)
    {
      return false;
    }
    else
    {
      $msgIds = $row['read'];

      if( ($key = array_search($msgId, $msgIds)) !== false )
      {
        unset($msgIds[$key]);
        $attr = [
          'read' => $msgIds
        ];
        $row->attributes = $attr;
        $row->save(true, array_keys($attr));
      }
    }
    return true;
  }



  public function beforeValidate()
  {
    if( parent::beforeValidate() )
    {
      if( is_array($this->read))
      {
        $this->read = implode(',', $this->read);
      }
      return true;
    }
    return false;
  }

  public function afterFind()
  {
    if(!$this->read)
    {
      $this->read = [];
    }
    else
    {
      $cArr = explode(',', trim($this->read));
      $this->read = $cArr ? $cArr : [];
    }
  }


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'msg_read';
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
			array('module', 'numerical', 'integerOnly'=>true),
			array('uid', 'length', 'max'=>11),
			array('read', 'length', 'max'=>5000),
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
			'uid' => '关联用户',
			'read' => '未读消息列表',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MsgRead the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
