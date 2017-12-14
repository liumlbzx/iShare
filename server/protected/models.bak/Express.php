<?php

/**
 * This is the model class for table "express".
 *
 * The followings are the available columns in table 'express':
 * @property string $id
 * @property string $uid
 * @property string $goodsid
 * @property string $status
 * @property string $cityid
 * @property string $address
 * @property string $fulladdress
 * @property string $contact
 * @property string $mobile
 * @property integer $kdtype
 * @property string $kdcode
 */
class Express extends CActiveRecord
{
  const STATUS = [
    1 => '请填写收货地址',
    2 => '等待发货',
    3 => '已发货',
    4 => '已确认收货',
  ];
  const STATUS_WAIT_ADDRESS = 1;
  const STATUS_WAIT = 2;
  const STATUS_EXPRESS = 3;
  const STATUS_DONE = 4;

  const KDTYPE = [
    1 => '顺丰快递',
    2 => 'EMS',
    3 => '中通快递',
    4 => '圆通快递',
    5 => '天天快递',
    6 => '韵达快递',
    99 => '其他快递'
  ];





  public function beforeValidate()
  {
    if( parent::beforeValidate() )
    {
      if( is_array($this->cityid))
      {
        $cArr = $this->cityid;
        $this->cityid = implode(',', $cArr);
      }
      return true;
    }
    return false;
  }

  public function afterFind()
  {
    $this->cityid = explode(',', $this->cityid) ?: [];
    parent::afterFind();
  }






	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'express';
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
        'cityid,address,contact,mobile',
        'filter', 'filter' => function($str){
        return CHtml::encode($str);
      }
      ],

			array('id', 'required'),
			array('kdtype', 'numerical', 'integerOnly'=>true),
			array('id, uid, status', 'length', 'max'=>10),
			array('goodsid, mobile', 'length', 'max'=>11),
      array('contact', 'length', 'max'=>20),
      array('cityid', 'length', 'max'=>30),
      array('address', 'length', 'max'=>200),
      array('fulladdress', 'length', 'max'=>250),
			array('kdcode', 'length', 'max'=>50),
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
      'r_goods' => [
        self::BELONGS_TO,
        'Goods',
        'goodsid',
        'select' => 'id,title'
      ],
      'r_user' => [
        self::BELONGS_TO,
        'User',
        'uid',
        'select' => 'id,nickname'
      ],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'uid' => '关联用户',
			'goodsid' => '关联商品',
			'status' => '快递状态',
			'cityid' => '收货城市',
      'address' => '收货地址',
      'fulladdress' => '收货地址(完整)',
			'contact' => '收货人',
			'mobile' => '收货地址',
			'kdtype' => '快递公司',
			'kdcode' => '快递单号',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Express the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
