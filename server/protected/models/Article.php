<?php

/**
 * This is the model class for table "article".
 *
 * The followings are the available columns in table 'article':
 * @property string $id
 * @property integer $status
 * @property string $uid
 * @property string $title
 * @property string $thumb
 * @property string $createtime
 * @property string $updatetime
 * @property string $free_content
 * @property string $charge_content
 * @property string $charge_jinbi
 * @property string $view_count
 * @property string $close_msg
 */
class Article extends CActiveRecord
{
  const STATUS = [
    1 => '正常',
    2 => '禁用',
  ];
  const STATUS_OPEN = 1;
  const STATUS_CLOSE = 2;


  public function beforeValidate()
  {
    if( parent::beforeValidate() )
    {
      $this->thumb = CFuncHelper::getUploadVirtulUrl($this->thumb);
      return true;
    }
    return false;
  }

  public function afterFind()
  {
    $this->thumb = CFuncHelper::getUploadRealUrl($this->thumb);
    parent::afterFind();
  }









  /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'article';
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
		  //todo 规则不全
      [
        'uid,title,createtime,charge_content,charge_jinbi',
        'required',
        'on' => 'add'
      ],

      [
        'updatetime',
        'required',
        'on' => 'edit'
      ],

      [
        'msg',
        'required',
        'on' => 'close'
      ],

      [
        'free_content,charge_content',
        'filter',
        'filter' => function($str){
          return CHtml::encode($str);
        }
      ],



			array('status', 'numerical', 'integerOnly'=>true),
			array('uid, createtime, updatetime, charge_jinbi', 'length', 'max'=>10),
			array('title', 'length', 'max'=>250),
			array('thumb, close_msg', 'length', 'max'=>200),
			array('free_content, charge_content', 'length', 'max'=>5000),
			array('view_count', 'length', 'max'=>11),
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
			'status' => '状态：1正常 2关闭',
			'uid' => '发布者',
			'title' => '标题',
			'thumb' => '缩略图',
			'createtime' => '发布时间',
			'updatetime' => '更新时间',
			'free_content' => '免费内容',
			'charge_content' => '收费内容',
			'charge_jinbi' => '收费金币',
			'view_count' => '阅读次数',
			'close_msg' => '关闭的原因',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Article the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
