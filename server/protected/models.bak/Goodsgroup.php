<?php

/**
 * This is the model class for table "goodsgroup".
 *
 * The followings are the available columns in table 'goodsgroup':
 * @property string $id
 * @property string $title
 * @property string $title_sub
 * @property double $price
 * @property string $times
 * @property string $times_per
 * @property string $pics
 * @property string $thumb
 * @property string $detail
 * @property string $createtime
 * @property string $stock
 * @property integer $auto_release
 */
class Goodsgroup extends CActiveRecord
{

  const AUTO_RELEASE = [
    1 => '是',
    2 => '否',
  ];
  const AUTO_RELEASE_YES = 1;
  const AUTO_RELEASE_NO = 2;


  /**
   * 自动发布一个商品
   * @param $groupid
   * @return array|string
   */
  public static function auto_release($groupid)
  {
    $group = self::model()->findByPk($groupid);
    if(!$group)
    {
      return '商品库不存在';
    }
    if($group['auto_release'] != self::AUTO_RELEASE_YES)
    {
      return '商品库不支持自动发布';
    }
    if($group['stock'] < 1)
    {
      return '商品库库存不足';
    }

    $cnt = Goods::model()->count(
      'groupid=:groupid',
      [
        ':groupid' => $group['id']
      ]
    );

    $group_times = $cnt+1;

    $data = new Goods('add');
    $attr = [
      'groupid' => $group['id'],
      'group_times' => $group_times,
      'sort' => 1,
      'show' => Goods::SHOW_OPEN,
      'createtime' => time()
    ];
    foreach (explode(',', 'title,title_sub,price,times,times_per,pics,thumb,detail') as $v)
    {
      $attr[$v] = $group[$v];
    }
    $data->attributes = $attr;
    if($data->save(true, array_keys($attr)))
    {
      //减去库存
      self::model()->updateCounters(
        [
          'stock' => -1
        ],
        'id=:id',
        [
          ':id' => $group['id']
        ]
      );
      return $data->id;
    }
    else
    {
      $errors = $data->errors;
      return '自动发布商品出错:'.reset($errors)[0];
    }

  }


  public function beforeValidate()
  {
    if( parent::beforeValidate() )
    {
      if( is_array($this->pics))
      {
        $cArr = $this->pics;

        $cArr = array_values(array_filter($cArr, function($v){
          return !!$v;
        }));

        foreach ($cArr as $k=>$v)
        {
          $cArr[$k] = CHtml::encode(CFuncHelper::getUploadVirtulUrl($v));
        }
        $this->pics = CFuncHelper::secJSON($cArr);
      }
      $this->thumb = CFuncHelper::getUploadVirtulUrl($this->thumb);
      $allowTags = explode(',', '
        h1,h2,h3,h4,h5,h6,
        div,section,p,
        ul,li,dt,dd,
        table,thead,tbody,tfoot,tr,td,th,
        span,a,em,i,img,font
      ');
      $allowTag = '';
      foreach ($allowTags as $v)
      {
        $allowTag .= '<'.trim($v).'>';
      }
      $detail = strip_tags($this->detail, $allowTag);
      $detail = preg_replace('/(<[\s\S]+?)on([\s\S]+?>)/i', '$1o n$2', $detail);
      $detail = preg_replace([
        '/\s{0,1}class\=\"{0,1}[^\"]+\"{0,1}/i',
        '/\s{0,1}id\=\"{0,1}[^\"]+\"{0,1}/i'
      ], '', $detail);

      $this->detail = $detail;
      return true;
    }
    return false;
  }

  public function afterFind()
  {
    $cArr = json_decode($this->pics,1) ?: [];
    foreach ($cArr as $k=>$v)
    {
      $cArr[$k] = CFuncHelper::getUploadRealUrl($v);
    }
    $this->pics = $cArr ?: [];
    $this->thumb = CFuncHelper::getUploadRealUrl($this->thumb);
  }

  public function beforeDelete()
  {
    if(parent::beforeDelete())
    {
      $count = Goods::model()->count(
        'groupid=:groupid',
        [
          ':groupid' => $this->id
        ]
      );
      if($count)
      {
        $this->addError('id', '请先删除夺宝数据');
        return false;
      }
      return true;
    }
    return false;
  }


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'goodsgroup';
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
        'title,title_sub,price,times,createtime',
        'required',
        'on' => 'add'
      ],

      [
        'title,title_sub,price,times,times_per,thumb',
        'filter', 'filter' => function($str){
          return CHtml::encode($str);
        }
      ],

			array('detail', 'required'),
			array('auto_release', 'numerical', 'integerOnly'=>true),
			array('price', 'numerical'),
			array('title, title_sub, thumb', 'length', 'max'=>200),
			array('times, times_per, createtime, stock', 'length', 'max'=>10),
			array('pics', 'length', 'max'=>1000),
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
			'title' => '标题',
			'title_sub' => '副标题',
			'price' => '价格',
			'times' => '所需次数',
			'times_per' => '每人限次',
			'pics' => '图片列表',
			'thumb' => '缩略图',
			'detail' => '详情',
			'createtime' => '创建时间',
			'stock' => '库存',
			'auto_release' => '自动发布',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Goodsgroup the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
