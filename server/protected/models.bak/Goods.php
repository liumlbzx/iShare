<?php

/**
 * This is the model class for table "goods".
 *
 * The followings are the available columns in table 'goods':
 * @property string $id
 * @property string $groupid
 * @property string $group_times
 * @property string $title
 * @property string $title_sub
 * @property double $price
 * @property string $times
 * @property string $times_per
 * @property string $pics
 * @property string $thumb
 * @property string $sort
 * @property string $detail
 * @property string $show
 * @property string $createtime
 * @property string $fulltime
 * @property string $opentime
 * @property string $opencode
 * @property string $openuid
 * @property string $openlottery
 */
class Goods extends CActiveRecord
{
  const SHOW = [
    1 => '正常',
    2 => '隐藏',
  ];
  const SHOW_OPEN = 1;
  const SHOW_CLOSE = 2;


  const GOODS_STATUS_NORMAL = 1; //正常
  const GOODS_STATUS_FULL = 2; //满员
  const GOODS_STATUS_OPEN = 3; //已开奖
  const GOODS_STATUS_HIDDEN = 5; //已隐藏

  /**
   * 已用抽奖次数与剩余抽奖次数
   * @param $id
   * @return array
   */
  public static function getCacheTimes($id)
  {
    return Lottery::getGoodsLotteryTimes($id);

  }

  /**
   * 检查商品状态
   * @param $data
   * @return string
   */
  public static function checkGoodsStatus($data)
  {
    if (is_numeric($data))
    {
      $data = self::model()->findByPk($data, [
        'select' => 'opentime,fulltime,`show`'
      ]);
    }
    if (!$data)
    {
      return false;
    }
    if ($data['opentime'] > 0)
    {
      return self::GOODS_STATUS_OPEN;
    }
    elseif ($data['fulltime'] > 0)
    {
      return self::GOODS_STATUS_FULL;
    }
    elseif ($data['show'] != self::SHOW_OPEN)
    {
      return self::GOODS_STATUS_HIDDEN;
    }
    else
    {
      return self::GOODS_STATUS_NORMAL;
    }

  }


  public static function getWillOpenTime($fulltime)
  {
    if($fulltime)
    {
      return $fulltime + Yii::app()->params['website']['willOpenTime'] + Yii::app()->params['website']['willOpenTimeDelay'] - time();
    }
    else
    {
      return 0;
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
      $goodsStatus = Goods::checkGoodsStatus($this->id);
      if($goodsStatus != Goods::GOODS_STATUS_NORMAL)
      {
        $this->addError('id', '不可以删除满员、已开奖的商品');
        return false;
      }
      $lotteryCount = Lottery::model()->count(
        'goods_id=:goods_id',
        [
          ':goods_id' => $this->id
        ]
      );
      if($lotteryCount)
      {
        $this->addError('id', '不可以删除已有用户参与的商品');
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
		return 'goods';
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
        'title,title_sub,price,times,times_per,thumb,opencode',
        'filter', 'filter' => function($str){
          return CHtml::encode($str);
        }
      ],

			array('detail', 'required'),
			array('price', 'numerical'),
			array('title, title_sub, thumb', 'length', 'max'=>200),
			array('times, times_per, sort, show, createtime, fulltime, opentime, opencode, openuid, openlottery, groupid, group_times', 'length', 'max'=>10),
      array('pics', 'length', 'max'=>1000),
      array('detail', 'length', 'max'=>60000),
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
        'openuid',
        'select' => 'id,nickname'
      ],
      'r_lottery' => [
        self::BELONGS_TO,
        'Lottery',
        'openlottery',
        'select' => 'id,buytime,ip,ipstr,codecount'
      ],
      'r_goodsgroup' => [
        self::BELONGS_TO,
        'Goodsgroup',
        'groupid',
        'select' => 'id,title',
        'on' => 't.groupid>0'
      ],
      'r_express' => [
        self::HAS_ONE,
        'Express',
        'goodsid',
        'select' => 'id,status',
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
      'groupid' => '商品库',
      'group_times' => '商品发布序次',
			'title' => '标题',
			'title_sub' => '副标题',
			'price' => '价格',
			'times' => '所需次数',
			'times_per' => '每人限次',
			'pics' => '图片列表',
			'thumb' => '缩略图',
			'sort' => '排序',
			'detail' => '详情',
			'show' => '是否显示',
			'createtime' => '创建时间',
			'fulltime' => '满员时间',
			'opentime' => '开奖时间',
			'opencode' => '开奖码',
			'openuid' => '幸运用户',
      'openlottery' => 'openLottery'
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Goods the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
