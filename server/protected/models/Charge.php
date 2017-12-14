<?php

/**
 * This is the model class for table "charge".
 *
 * The followings are the available columns in table 'charge':
 * @property string $id
 * @property string $article_id
 * @property string $consumer_id
 * @property string $publisher_id
 * @property double $jinbi
 * @property double $jinbi_system
 * @property double $jinbi_publisher
 * @property string $createtime
 */
class Charge extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'charge';
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
			array('jinbi, jinbi_system, jinbi_publisher', 'numerical'),
			array('article_id, consumer_id, publisher_id, createtime', 'length', 'max'=>10),
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
			'article_id' => '文章',
			'consumer_id' => '消费者',
			'publisher_id' => '发布者',
			'jinbi' => '消费金币',
			'jinbi_system' => '系统扣除金币',
			'jinbi_publisher' => '发布者获得金币',
			'createtime' => '消费时间',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Charge the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
