<?php

/**
 * This is the model class for table "recharge".
 *
 * The followings are the available columns in table 'recharge':
 * @property string $id
 * @property double $money
 * @property string $createtime
 * @property string $finishtime
 * @property string $sn
 * @property integer $uid
 * @property integer $step
 * @property string $platform
 */
class Recharge extends CActiveRecord
{
  public static $STEP = array(
    1 => '未支付',
    2 => '支付成功',
    3 => '支付失败',
  );

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'recharge';
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
			array('uid, step', 'numerical', 'integerOnly'=>true),
			array('money', 'numerical'),
			array('createtime, finishtime, platform', 'length', 'max'=>10),
			array('sn', 'length', 'max'=>50),
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
			'money' => '充值金额',
			'createtime' => '订单创建时间',
			'finishtime' => '订单支付成功时间',
			'sn' => '支付平台编号',
			'uid' => '订单用户',
			'step' => '步骤',
			'platform' => '支付平台',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Recharge the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
