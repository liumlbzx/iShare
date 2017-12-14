<?php

/**
 * This is the model class for table "syscfg".
 *
 * The followings are the available columns in table 'syscfg':
 * @property string $key
 * @property string $value
 */
class Syscfg extends CActiveRecord
{

  /**
   * 获取系统配置
   * @param $key
   * @param bool $isJson
   * @return mixed
   */
  public static function getValue($key, $isJson=false)
  {
    if(!$key)
    {
      return null;
    }
    $row = self::model()->findByPk($key);
    if(!$row)
    {
      return null;
    }

    return $isJson? json_decode($row['value'],1) : $row['value'];

  }

  /**
   * 获取系统配置
   * @param $key
   * @param $value
   * @param bool $isJson
   * @return mixed
   */
  public static function setValue($key, $value, $isJson=false)
  {
    if(!$key)
    {
      return null;
    }

    $row = self::model()->findByPk($key);
    if(!$row)
    {
      return null;
    }

    if($isJson)
    {
      $value = json_encode($value);
    }

    $attr = [
      'value' => $value
    ];

    $row->attributes=  $attr;

    return $row->save(true, array_keys($attr));

  }


  /**
   * @return string the associated database table name
   */
  public function tableName()
  {
    return 'syscfg';
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
      array('key', 'length', 'max'=>20),
      array('value', 'length', 'max'=>250),
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
      'key' => '配置的索引',
      'value' => '值',
    );
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return Syscfg the static model class
   */
  public static function model($className=__CLASS__)
  {
    return parent::model($className);
  }
}
