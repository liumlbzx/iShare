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
    $uid = $this->getPhpInput('uid');
    $data = User::getUser($uid);

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


  //他的夺宝纪录
  private function _getGoodsBuyList()
  {
    $uid = $this->getPhpInput('uid');

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

    $cdb->addCondition('uid='.intval($uid));

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

  //他的中奖纪录
  private function _getGoodsLotteryList()
  {
    $uid = $this->getPhpInput('uid');

    $select_goods = 'id,group_times,thumb,title,title_sub,price,opentime,opencode';
    $select_lottery = 'id,codecount';

    $cdb = new CDbCriteria(array(
      'order' => 't.opentime desc',
      'select' => $select_goods,
      'with' => [
        'r_lottery' => [
          'select' => $select_lottery,
        ],
      ]
    ));

    $cdb->addCondition('openuid='.intval($uid).' AND opentime>0');

    $pages = new CPagination(Goods::model()->count($cdb));
    $pages->pageSize = $this->request->getParam('pageSize', 20);
    $pages->applyLimit($cdb);
    $datas = Goods::model()->findAll($cdb);

    $datas = array_map(function($v) use($select_goods, $select_lottery){
      $ret = $this->filterArray($select_goods, $v);
      $ret['r_lottery'] = $this->filterArray($select_lottery, $v['r_lottery']) ?: [];
      return $ret;
    }, $datas);

    $this->encodeJSON(200, [
      'count' => $pages->itemCount,
      'datas' => $datas,
    ]);
  }


}