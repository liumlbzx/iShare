<?php

/**
 * This is the model class for table "lottery".
 *
 * The followings are the available columns in table 'lottery':
 * @property string $id
 * @property string $uid
 * @property string $goods_id
 * @property string $buytime
 * @property string $ip
 * @property string $ipstr
 * @property string $code
 * @property string $codecount
 */
class Lottery extends CActiveRecord
{

  /**
   * 给某用户新增多少个抽奖机会
   * @param $goods_id
   * @param $uid
   * @param $num
   * @return array|string
   */
  public static function addTimes($goods_id, $uid, $num)
  {
    if( !is_numeric($num) || $num<1 || !$uid || !$goods_id)
    {
      return [
        'code' => 'ERROR_PARAMS',
        'msg' => '参数非法'
      ];
    }

    $user = User::getUser($uid, 'id,money,status');
    if(!$user)
    {
      return [
        'code' => 'USER_NOT_FOUND',
        'msg' => '用户不存在'
      ];
    }
    if($user['status'] != User::STATUS_OPEN)
    {
      return [
        'code' => 'USER_STATUS_EXCEPTION',
        'msg' => '用户状态异常'
      ];
    }
    if($user['money'] < $num)
    {
      return [
        'code' => 'USER_MONEY_LOW',
        'msg' => '用户余额不足'
      ];
    }

    $goods = Goods::model()->findByPk($goods_id, [
      'select' => 'id,groupid,title,times,times_per,opentime,fulltime,`show`',
    ]);

    if(!$goods)
    {
      return [
        'code' => 'GOODS_NOT_FOUND',
        'msg' => '商品不存在'
      ];
    }
    else
    {
      $goodsStatus = Goods::checkGoodsStatus($goods);
      if($goodsStatus == Goods::GOODS_STATUS_HIDDEN)
      {
        return [
          'code' => 'GOODS_HIDDEN',
          'msg' => '商品已下架'
        ];
      }
      elseif($goodsStatus == Goods::GOODS_STATUS_FULL)
      {
        return [
          'code' => 'GOODS_FULL',
          'msg' => '商品已满员'
        ];
      }
      elseif($goodsStatus == Goods::GOODS_STATUS_OPEN)
      {
        return [
          'code' => 'GOODS_OPEN',
          'msg' => '商品已揭晓'
        ];
      }
      else
      {
        if(!$goods['times_per'])
        {
          $goods['times_per'] = $goods['times'];
        }

        if($num > $goods['times'] || $num > $goods['times_per'] )
        {
          return [
            'code' => 'MAX_THAN_GOODS_TIMES',
            'msg' => '超出商品限制人次'
          ];
        }
        $surplus = self::getGoodsTimesSurplus($goods_id);
        if($num > count($surplus))
        {
          return [
            'code' => 'MAX_THAN_SURPLUS_TIMES',
            'msg' => '超出商品剩余参与人次'
          ];
        }

        $uRow = Yii::app()->db->createCommand()
          ->select('SUM(codecount) as cnt')
          ->from(self::model()->tableName())
          ->where('goods_id=:goods_id AND uid=:uid', [
            ':goods_id' => $goods['id'],
            ':uid' => $uid
          ])
          ->queryRow();

        $done = intval($uRow['cnt']);

        if( $done + $num > $goods['times_per'] )
        {
          return [
            'code' => 'MAX_THAN_USER_SURPLUS_TIMES',
            'msg' => '你最多只可以参与'.($goods['times_per'] - $done).'次'
          ];
        }

        shuffle($surplus);
        $code = array_slice($surplus, 0, $num);

        $transaction = User::model()->dbConnection->beginTransaction();

        User::model()->updateCounters(
          [
            'money' => 0 - $num
          ],
          'id=:id',
          [
            ':id' => $user['id']
          ]
        );

        if(count($surplus) <= $num)
        {
          Goods::model()->updateByPk($goods['id'], [
            'fulltime' => time()
          ]);

          if($goods['groupid'])
          {
            Goodsgroup::auto_release($goods['groupid']);
          }
        }

        $m = new self();
        $m->attributes = [
          'uid' => $user['id'],
          'goods_id' => $goods['id'],
          'buytime' => date('Y-m-d H:i:s') . '.' . floor(microtime()*1000),
          'ip' => Yii::app()->request->userHostAddress,
          'ipstr' => CFuncHelper::getIpStr(Yii::app()->request->userHostAddress, true),
          'code' => $code,
          'codecount' => count($code)
        ];
        if($m->save())
        {
          //资金明细
          Moneydetail::add(User::getUserId(), $num, Moneydetail::TYPE_GOODS, '参与 '.$goods['title'].' 夺宝');

          $transaction->commit();
          self::clearGoodsTimes($goods_id);
          return $code;
        }
        else
        {
          $transaction->rollBack();
          $errors = $m->errors;
          return [
            'code' => 'ERROR_OTHER',
            'msg' => '参与出错:'.reset($errors)[0]
          ];
        }


      }
    }

  }

  /**
   * 获取商品剩下的抽奖池
   * @param $goods_id
   * @return array
   */
  public static function getGoodsTimesSurplus($goods_id)
  {
    $cache = Yii::app()->cache;
    $cacheKey = 'goods_lottery_times_surplus_'.$goods_id;
    $ret = $cache->get($cacheKey);
    if($ret===false)
    {
      $goods = Goods::model()->findByPk($goods_id, [
        'select' => 'times,id'
      ]);
      if(!$goods)
      {
        $ret = [];
      }
      else
      {
        $goodsStatus = Goods::checkGoodsStatus($goods_id);
        if($goodsStatus != Goods::GOODS_STATUS_NORMAL)
        {
          $ret = [];
        }
        elseif($goods['times'] < 1)
        {
          $ret = [];
        }
        else
        {
          $rows = self::model()->findAll([
            'condition' => 'goods_id=:goods_id',
            'params' => [
              ':goods_id' => $goods['id']
            ],
            'select' => 'code'
          ]);
          $done = [];
          foreach ($rows as $row)
          {
            foreach ($row['code'] as $c)
            {
              $done[] = $c;
            }
          }

          $all = [];
          for($i=1;$i<=$goods['times'];$i++)
          {
            $all[] = 100000 + $i;
          }
          $ret = array_diff($all, $done);
        }
      }
      $cache->set($cacheKey, $ret, 86400*7);
    }
    return $ret;
  }


  /**
   * 商品的抽奖次数与剩余次数
   * @param $goods_id
   * @return array
   */
  public static function getGoodsLotteryTimes($goods_id)
  {
    $cache = Yii::app()->cache;
    $cacheKey = 'goods_lottery_times_'.$goods_id;
    $ret = $cache->get($cacheKey);
    if($ret===false)
    {
      $goods = Goods::model()->findByPk($goods_id, [
        'select' => 'times,id'
      ]);
      if(!$goods)
      {
        $ret = [
          'done' => 0,
          'surplus' => 0
        ];
      }
      else
      {
        if($goods['times'] < 1)
        {
          $ret = [
            'done' => 0,
            'surplus' => 0
          ];
        }
        else
        {
          $surplus = count(self::getGoodsTimesSurplus($goods_id));
          $ret = [
            'done' => $goods['times'] - $surplus,
            'surplus' => $surplus
          ];
        }
      }
      $cache->set($cacheKey, $ret, 86400*7);
    }
    return $ret;
  }

  /**
   * 清空商品的抽奖次数与剩余次数
   * 清空商品的奖池
   * @param $goods_id
   * @return bool
   */
  public static function clearGoodsTimes($goods_id)
  {
    Yii::app()->cache->delete('goods_lottery_times_'.$goods_id);
    Yii::app()->cache->delete('goods_lottery_times_surplus_'.$goods_id);
    return true;
  }


  public function beforeValidate()
  {
    if( parent::beforeValidate() )
    {
      if(is_array($this->code))
      {
        $this->codecount = count($this->code);
      }
      $this->code = is_array($this->code) ? implode(',', $this->code) : '';
      return true;
    }
    return false;
  }

  public function afterFind()
  {
    if($this->code)
    {
      $this->code = explode(',', $this->code);
    }
    else
    {
      $this->code = [];
    }
  }


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'lottery';
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
			array('code', 'required'),
      array('codecount', 'length', 'max'=>10),
      array('uid', 'length', 'max'=>11),
      array('goods_id', 'length', 'max'=>10),
      array('buytime', 'length', 'max'=>30),
			array('ip', 'length', 'max'=>20),
			array('ipstr', 'length', 'max'=>50),
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
      'r_goods' => [
        self::BELONGS_TO,
        'Goods',
        'goods_id',
        'select' => 'id,title'
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
			'uid' => '用户',
			'goods_id' => '商品',
			'buytime' => '购买时间',
			'ip' => 'ip',
			'ipstr' => 'ip地址',
      'code' => '机会码',
      'codecount' => '机会码数量',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Lottery the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
