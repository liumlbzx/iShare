<?php

/**
 * This is the model class for table "drawmoney".
 *
 * The followings are the available columns in table 'drawmoney':
 * @property string $id
 * @property string $uid
 * @property string $jinyuan
 * @property double $money_system
 * @property double $money_user
 * @property string $createtime
 * @property string $finishtime
 * @property integer $step
 * @property string $msg
 * @property string $pay_account
 */
class Drawmoney extends CActiveRecord
{
  public static $STEP = array(
    1 => '申请中',
    2 => '提现成功',
    3 => '提现失败',
  );

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'drawmoney';
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
        'filter',
        'filter' => function($str){
          return CHtml::encode($str);
        }
      ],



      array('step,jinyuan', 'numerical', 'integerOnly'=>true),
			array('money_system, money_user', 'numerical'),
			array('uid, jinyuan, createtime, finishtime', 'length', 'max'=>10),
			array('msg', 'length', 'max'=>200),
			array('pay_account', 'length', 'max'=>100),
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
			'uid' => '提现申请人',
			'jinyuan' => '提现金元宝',
			'money_system' => '系统扣除金额',
			'money_user' => '用户得到金额',
			'createtime' => '创建时间',
			'finishtime' => '完成时间',
			'step' => '提现步骤',
			'msg' => '系统备注',
			'pay_account' => '提现目标账号(微信/支付宝)',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Drawmoney the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
