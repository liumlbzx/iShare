<?php

/**
 * This is the model class for table "pay".
 *
 * The followings are the available columns in table 'pay':
 * @property string $id
 * @property double $money
 * @property string $createtime
 * @property string $finishtime
 * @property string $sn
 * @property string $uid
 * @property integer $step
 * @property integer $platform
 */
class Pay extends CActiveRecord
{

  const STEP = [
    1 => '已创建',
    2 => '支付成功',
    3 => '支付失败',
  ];
  const STEP_CREATE = 1;
  const STEP_PAY_SUCCESS = 2;
  const STEP_PAY_FAIL = 3;

  //支付平台
  const PLATFORM = [
    1 => '手机-微信支付',
  ];

  const PLATFORM_WECHAT_MOBILE = 1;


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pay';
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
			array('step,platform', 'numerical', 'integerOnly'=>true),
			array('money', 'numerical'),
			array('createtime, finishtime', 'length', 'max'=>10),
			array('sn', 'length', 'max'=>50),
			array('uid', 'length', 'max'=>11),
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
			'money' => '金额',
			'createtime' => '订单创建时间',
			'finishtime' => '订单支付成功时间',
			'sn' => '支付编号',
			'uid' => '用户',
			'step' => '步骤',
			'platform' => '支付平台',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Pay the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
