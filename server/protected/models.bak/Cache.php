<?php

/**
 * This is the model class for table "cache".
 *
 * The followings are the available columns in table 'cache':
 * @property string $key
 * @property string $type
 * @property string $value
 */
class Cache extends CActiveRecord
{

  public static function addValue($key, $type, $num)
  {
    if(!is_numeric($num))
    {
      return false;
    }

    if(!$key || !$type)
    {
      return false;
    }

    $num = intval($num);
    $row = self::model()->findByPk($key);
    if(!$row)
    {
      $row = new self();
      $attr = [
        'key' => $key,
        'type' => $type,
        'value' => $num
      ];
    }
    else
    {
      $attr = [
        'value' => intval($row['value']) + $num
      ];
    }
    $row->attributes = $attr;
    return $row->save();
  }

  public static function setValue($key, $type, $value)
  {
    $row = self::model()->findByPk($key);
    if(!$row)
    {
      $row = new self();
      $attr = [
        'key' => $key,
        'type' => $type,
        'value' => $value
      ];
    }
    else
    {
      $attr = [
        'value' => $value
      ];
    }
    $row->attributes = $attr;
    $row->save();
  }

  public static function getValue($key, $defaultVal=null)
  {
    $row = self::model()->findByPk($key);
    return $row ? $row['value'] : $defaultVal;
  }

  public static function getValueWithType($type)
  {
    $retArr = self::model()->findAll('type=:type', [
      ':type' => $type
    ]);
    return CHtml::listData($retArr, 'key', 'value');
  }

  public static function deleteValue($key)
  {
    return self::model()->deleteByPk($key);
  }


  /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cache';
	}

  public function primaryKey()
  {
    return 'key';
  }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('key, type', 'length', 'max'=>50),
			array('value', 'length', 'max'=>30),
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
			'key' => '缓存键',
			'type' => '缓存分类',
			'value' => '缓存值',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cache the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
