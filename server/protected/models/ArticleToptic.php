<?php

/**
 * This is the model class for table "article_toptic".
 *
 * The followings are the available columns in table 'article_toptic':
 * @property string $id
 * @property string $name
 * @property string $pinyin
 * @property integer $status
 */
class ArticleToptic extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'article_toptic';
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
        'name,pinyin',
        'required',
        'on' => 'add,edit'
      ],

      [
        'name,pinyin',
        'filter',
        'filter' => function($str){
          return CHtml::encode($str);
        }
      ],

			array('status', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>50),
			array('pinyin', 'length', 'max'=>100),
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
			'name' => '主题名称',
			'pinyin' => '主题的拼音',
			'status' => '状态：1正常 2关闭',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ArticleToptic the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
