<?php

/**
 * This is the model class for table "moneydetail".
 *
 * The followings are the available columns in table 'moneydetail':
 * @property string $id
 * @property double $money
 * @property string $createtime
 * @property string $type
 * @property string $msg
 * @property string $uid
 */
class Moneydetail extends CActiveRecord
{
  const TYPE = [
    1 => '充值',
    2 => '消费',
  ];

  const TYPE_RECHARGE = 1;
  const TYPE_GOODS = 2;

  public static function add($uid, $money, $type, $msg)
  {
    $data = new self();
    $attr = [
      'money' => $money,
      'createtime' => time(),
      'type' => $type,
      'msg' => $msg,
      'uid' => $uid
    ];
    $data->attributes = $attr;
    return $data->save(true, array_keys($attr));
  }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'moneydetail';
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
			array('money', 'numerical'),
			array('createtime, type, uid', 'length', 'max'=>10),
			array('msg', 'length', 'max'=>200),
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
			'money' => '金额',
			'createtime' => '创建时间',
			'type' => '类型',
			'msg' => '备注',
			'uid' => '用户',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Moneydetail the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
