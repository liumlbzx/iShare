<?php

class indexAction extends Action
{
  public function run()
  {
    $do = $this->request->getParam('do');

    $func = '_'. $do;
    if( method_exists($this, $func) )
    {
      $this->$func();
    }
  }

  //
  private function _getUser()
  {
    $data = User::getUser();

    if(!$data)
    {
      $this->encodeJSON(500, '用户不存在');
    }

    if($data['status'] != User::STATUS_OPEN)
    {
      $this->encodeJSON(500, '用户状态异常');
    }

    unset($data['password']);

    $this->encodeJSON(200, $data);
  }

  /**
   * 编辑用户信息
   */
  private function _postUser()
  {
    $userId = User::getUserId();
    $type = $this->getPhpInput('type');

    $data = (new User('userEdit'))->findByPk($userId);

    if(!$data)
    {
      $this->encodeJSON(500, '数据不存在');
    }


    $attr = [];

    if($type==='nickname')
    {
      $attr = [
        'nickname' => $this->getPhpInput('nickname')
      ];
    }
    elseif($type==='mobile')
    {
      $mobile_old = $this->getPhpInput('mobile_old');
      $mobile_new = $this->getPhpInput('mobile_new');
      $mobile_reply = $this->getPhpInput('mobile_reply');

      if($mobile_old != $data['mobile'])
      {
        $this->encodeJSON(500, '原手机号码不正确');
      }

      $attr = [
        'mobile' => $mobile_new
      ];
    }
    elseif($type==='shouhuo')
    {
      $arr = [];
      $info = $this->getPhpInput('info');
      foreach (['shouhuo_cityid','shouhuo_cityname','shouhuo_address','shouhuo_contact','shouhuo_mobile'] as $v)
      {
        $arr[$v] = isset($info[$v]) ? $info[$v] : '';
      }

      $attr = [
        'info' => array_merge($data['info'], $arr)
      ];
    }

    if(!$attr)
    {
      $this->encodeJSON(500, '编辑类目丢失');
    }

    $data->attributes = $attr;
    if( $data->save(true, array_keys($attr)) )
    {
      $this->encodeJSON(200);
    }
    else
    {
      $errors = $data->errors;
      $this->encodeJSON(500, reset($errors)[0]);
    }
  }

  //我的夺宝纪录
  private function _getGoodsBuyList()
  {
    $select_goods = 'id,`show`,group_times,thumb,title,price,fulltime,opentime,opencode';
    $select_lottery = 'id,codecount';
    $select_user = 'id,nickname';

    $cdb = new CDbCriteria(array(
      'order' => 't.id desc',
      'select' => $select_lottery,
      'with' => [
        'r_goods' => [
          'select' => $select_goods,
          'with' => [
            'r_user' => [
              'select' => $select_user
            ]
          ]
        ],
      ]
    ));

    $cdb->addCondition('uid='.User::getUserId());

    $pages = new CPagination(Lottery::model()->count($cdb));
    $pages->pageSize = $this->request->getParam('pageSize', 20);
    $pages->applyLimit($cdb);
    $datas = Lottery::model()->findAll($cdb);

    $datas = array_map(function($v) use($select_goods, $select_user, $select_lottery){
      $ret = $this->filterArray($select_lottery, $v);
      $ret['r_goods'] = $this->filterArray($select_goods, $v['r_goods']) ?: [];
      $ret['r_goods']['goodsStatus'] = Goods::checkGoodsStatus($ret['r_goods']);

      if($ret['r_goods']['goodsStatus']==Goods::GOODS_STATUS_OPEN)
      {
        $ret['r_goods']['r_user'] = $this->filterArray($select_user, $v['r_goods']['r_user']) ?: [];
      }
      elseif($ret['r_goods']['goodsStatus']==Goods::GOODS_STATUS_NORMAL)
      {
        $ret['r_goods']['cacheTimes'] = Goods::getCacheTimes($ret['r_goods']['id']);
      }

      return $ret;
    }, $datas);

    $this->encodeJSON(200, [
      'count' => $pages->itemCount,
      'serverTime' => time(),
      'datas' => $datas,
    ]);
  }

  //我的中奖纪录
  private function _getGoodsLotteryList()
  {
    $select_goods = 'id,group_times,thumb,title,title_sub,price,opentime,opencode';
    $select_lottery = 'id,codecount';
    $select_express = 'id,status';

    $cdb = new CDbCriteria(array(
      'order' => 't.opentime desc',
      'select' => $select_goods,
      'with' => [
        'r_lottery' => [
          'select' => $select_lottery,
        ],
        'r_express' => [
          'select' => $select_express,
        ],
      ]
    ));

    $cdb->addCondition('openuid='.User::getUserId().' AND opentime>0');

    $pages = new CPagination(Goods::model()->count($cdb));
    $pages->pageSize = $this->request->getParam('pageSize', 20);
    $pages->applyLimit($cdb);
    $datas = Goods::model()->findAll($cdb);

    $datas = array_map(function($v) use($select_goods, $select_lottery, $select_express){
      $ret = $this->filterArray($select_goods, $v);
      $ret['r_lottery'] = $this->filterArray($select_lottery, $v['r_lottery']) ?: [];
      $ret['r_express'] = $this->filterArray($select_express, $v['r_express']) ?: [];
      return $ret;
    }, $datas);

    $this->encodeJSON(200, [
      'count' => $pages->itemCount,
      'serverTime' => time(),
      'datas' => $datas,
    ]);
  }

  //中奖纪录的快递信息
  private function _getLotteryExpress()
  {
    $expressId = $this->getPhpInput('id');

    $data = Express::model()->findByPk($expressId, [
      'condition' => 'uid=:uid',
      'params' => [
        ':uid' => User::getUserId()
      ]
    ]);

    if(!$data)
    {
      $this->encodeJSON(500, '数据不存在');
    }

    if($data['status'] == Express::STATUS_WAIT_ADDRESS)
    {
      $userinfo = User::getUser(null, 'info');
      $arr = explode(',', 'cityid,address,contact,mobile');
      foreach ($arr as $v)
      {
        if(!$data[$v])
        {
          $data[$v] = CFuncHelper::kArr($userinfo['info'], 'shouhuo_'.$v);
        }
      }
    }

    $this->encodeJSON(200, $data);
  }

  //修改中奖纪录的快递信息
  private function _postLotteryExpress()
  {
    $expressId = $this->getPhpInput('id');

    $data = (new Express('userEdit'))->findByPk($expressId, [
      'condition' => 'uid=:uid',
      'params' => [
        ':uid' => User::getUserId()
      ]
    ]);

    if(!$data)
    {
      $this->encodeJSON(500, '数据不存在');
    }

    if(!in_array($data['status'], [Express::STATUS_WAIT_ADDRESS,Express::STATUS_WAIT, Express::STATUS_EXPRESS]))
    {
      $this->encodeJSON(500, '数据不可以修改');
    }

    $attr = [];

    if($data['status']==Express::STATUS_WAIT_ADDRESS||$data['status']==Express::STATUS_WAIT)
    {
      $arr = explode(',', 'cityid,address,fulladdress,contact,mobile');
      foreach ($arr as $v)
      {
        if( $q = $this->getPhpInput($v) )
        {
          $attr[$v] = $q;
        }
      }
      $attr['status'] = Express::STATUS_WAIT;
    }
    elseif($data['status']==Express::STATUS_EXPRESS)
    {
      $attr = [
        'status' => Express::STATUS_DONE
      ];
    }


    $data->attributes = $attr;
    if( $data->save(true, array_keys($attr)) )
    {
      $this->encodeJSON(200);
    }
    else
    {
      $errors = $data->errors;
      $this->encodeJSON(500, reset($errors)[0]);
    }
  }


  //我的资金明细纪录
  private function _getMoneyDetailList()
  {
    $select = '*';

    $cdb = new CDbCriteria(array(
      'order' => 't.id desc',
      //'select' => $select,
      'condition' => 'uid='.User::getUserId()
    ));

    $pages = new CPagination(Moneydetail::model()->count($cdb));
    $pages->pageSize = $this->request->getParam('pageSize', 20);
    $pages->applyLimit($cdb);
    $datas = Moneydetail::model()->findAll($cdb);

//    $datas = array_map(function($v) use($select){
//      $ret = $this->filterArray($select, $v);
//      return $ret;
//    }, $datas);

    $this->encodeJSON(200, [
      'count' => $pages->itemCount,
      'datas' => $datas,
    ]);
  }

  //充值
  private function _postRecharge()
  {
    $money = $this->request->getParam('money');
    $platform = $this->request->getParam('platform');

    if( !preg_match('/^[1-9]\d*$/',$money))
    {
      $this->encodeJSON(500, '金额错误');
    }
    $platformArr = Pay::PLATFORM;
    if( !isset($platformArr[$platform]))
    {
      $this->encodeJSON(500, '支付渠道错误');
    }

    $money = floatval($money);

    $userInfo = User::getUser(null,'id,wechatId');
    $wechatId = $userInfo['wechatId'];

    require_once Yii::getPathOfAlias('application.3rd').'/Wechat.php';
    $wechat = new Wechat();
    $ret = $wechat->postPay(
      $money,
      time() . mt_rand(100000, 999999),
      $wechatId,
      [
        'body' => Yii::app()->params['website']['webName'] . ' - 充值',
        'detail' => [
          'goods_detail' => [
            'goods_name' => Yii::app()->params['website']['webName'] . " 充值{$money}元 "
          ]
        ],
        'attach' => $userInfo['id']
      ]
    );

    if(is_array($ret))
    {
      $this->encodeJSON(200, $ret);
    }
    else
    {
      $this->encodeJSON(500, $ret);
    }


    //不创建订单，直接支付，支付成功后再创建订单
    return false;
    $time = time();

    $data = [
      'money' => $money,
      'createtime' => $time,
      'uid' => User::getUserId(),
      'status' => Pay::STEP_CREATE,
      'platform' => $platform
    ];



    $m = new Pay();
    $m->attributes = $data;
    if( $m->save() )
    {
      //订单创建成功，请求支付链接
      $userInfo = User::getUser(null,'wechatId');
      $wechatId = $userInfo['wechatId'];

      require_once Yii::getPathOfAlias('application.3rd').'/Wechat.php';
      $wechat = new Wechat();
      $ret = $wechat->postPay(
        $m['money'],
        $m['id'],
        $wechatId,
        [
          'body' => Yii::app()->params['website']['webName'] . ' - 充值',
          'detail' => [
            'goods_detail' => [
              'goods_name' => Yii::app()->params['website']['webName'] . " 充值{$m['money']}元 "
            ]
          ]
        ]
      );

      if(is_array($ret))
      {
        $this->encodeJSON(200, $ret);
      }
      else
      {
        $this->encodeJSON(500, $ret);
      }


    }
    else
    {
      $errors = $m->errors;
      $errors = reset($errors);
      $this->encodeJSON(500, '订单创建失败:'.$errors[0]);
    }
  }

}