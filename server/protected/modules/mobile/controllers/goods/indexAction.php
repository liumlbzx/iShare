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

  /**
   * 首页幻灯片广告
   */
  private function _getCarousel()
  {
    $file = Yii::getPathOfAlias('webroot.config') . '/mobile_top_carousel.php';
    $retArr = [];
    if(file_exists($file))
    {
      $retArr = require($file);
    }

    $this->encodeJSON(200, $retArr);

  }

  //商品
  private function _get()
  {
    $id = $this->getPhpInput('id');
    $select = explode(',', $this->getPhpInput('select'));
    $select = array_intersect($select, array_keys(Goods::model()->attributeLabels()) );

    if($select && is_array($select) && !in_array('id', $select) )
    {
      $select[] = 'id';
    }

    $data = Goods::model()->findByPk($id, [
      'with' => [
        'r_user' => [
          'select' => 'id,nickname,face,wechatId'
        ],
        'r_lottery'
      ],
      'select' => $select ?: '*'
    ]);

    if(!$data)
    {
      $this->encodeJSON(500, '商品已下架');
    }

    if($data['id'])
    {
      $data = array_merge(
        $data->attributes,
        [
          'r_user' => $data['r_user'] ?: [],
          'r_lottery' => $data['r_lottery'] ?: [],
          'cacheTimes' => Goods::getCacheTimes($data['id']),
          'goodsStatus' => Goods::checkGoodsStatus($data),
        ]
      );
      if($data['fulltime'])
      {
        //多10秒给前端延迟
        $data['_willOpenTime'] = Goods::getWillOpenTime($data['fulltime']);
      }
    }

    $this->encodeJSON(200, $data);
  }

  //商品库
  private function _getGroup()
  {
    $id = $this->getPhpInput('id');
    $select = explode(',', $this->getPhpInput('select'));
    $select = array_intersect($select, array_keys(Goodsgroup::model()->attributeLabels()) );

    if($select && is_array($select) && !in_array('id', $select) )
    {
      $select[] = 'id';
    }

    $data = Goodsgroup::model()->findByPk($id, [
      'select' => $select ?: '*'
    ]);

    if(!$data)
    {
      $this->encodeJSON(500, '商品库已下架');
    }

    $this->encodeJSON(200, $data);
  }

  //获取往期商品
  private function _getGoodsGroupIds()
  {
    $groupid = $this->getPhpInput('groupid');
    $limit = $this->getPhpInput('limit');

    if(!$groupid)
    {
      $this->encodeJSON(200, []);
    }

    $select = 'group_times,id';
    $cdb = new CDbCriteria([
      'order' => 'id desc',
      'select' => $select,
      'condition' => '`show`=:show AND groupid=:groupid',
      'params' => [
        ':show' => Goods::SHOW_OPEN,
        ':groupid' => $groupid
      ]
    ]);
    if($limit)
    {
      $cdb->limit = $limit;
    }
    $datas = Goods::model()->findAll($cdb);

    $datas = array_map(function($v) use($select){
      return $this->filterArray($select, $v);
    }, $datas);
    $this->encodeJSON(200, $datas);
  }

  //首页需要的商品列表
  private function _getGoodsIndexNormal()
  {
    $sortType = $this->getPhpInput('sortType', 'jiexiao');
    $sortOrder = $this->getPhpInput('sortOrder', 'desc');
    $page = $this->getPhpInput('page', 1);
    $pageSize = $this->getPhpInput('pageSize', 20);

    $select = 'id,thumb,group_times,title,price,times,createtime';

    $cdb = new CDbCriteria(array(
      'select' => $select
    ));

    $cdb->addCondition('`show`=:show AND fulltime=0 AND opentime=0');
    $cdb->params[':show'] = Goods::SHOW_OPEN;

    $datas = Goods::model()->findAll($cdb);

    $datas = array_map(function($v) use($select){
      $ret = $this->filterArray($select, $v);
      $ret['cacheTimes'] = Goods::getCacheTimes($v['id']);
      return $ret;
    }, $datas);

    usort($datas, function($pre,$next) use ($sortType, $sortOrder){
      if($sortType==='jiexiao')
      {
        $nextValue = $next['cacheTimes']['done']/$next['times'];
        $preValue = $pre['cacheTimes']['done']/$pre['times'];
      }
      elseif($sortType==='renqi')
      {
        $nextValue = $next['cacheTimes']['done'];
        $preValue = $pre['cacheTimes']['done'];
      }
      elseif($sortType==='zuixin')
      {
        $nextValue = $next['createtime'];
        $preValue = $pre['createtime'];
      }
      elseif($sortType==='jiazhi')
      {
        $nextValue = $next['price'];
        $preValue = $pre['price'];
      }
      else
      {
        return 0;
      }

      $jbk = ($sortOrder === 'asc') ? 1 : -1;

      if($nextValue < $preValue)
      {
        return $jbk;
      }
      elseif($nextValue == $preValue)
      {
        return $next['createtime'] < $pre['createtime'] ? $jbk : -$jbk;
      }
      else
      {
        return -$jbk;
      }
    });

    $datas = array_slice($datas, ($page-1)*$pageSize, $pageSize );

    $this->encodeJSON(200, [
      'datas' => $datas,
    ]);
  }

  //即将揭晓和已经揭晓的夺宝
  private function _getFullAndOpenGoods()
  {
    $select = 'id,`show`,thumb,title,title_sub,price,fulltime,opentime,opencode';
    $select_user = 'id,nickname';
    $select_lottery = 'codecount';

    $cdb = new CDbCriteria(array(
      'order' => '(opentime=0) desc,fulltime asc,opentime desc',
      'select' => $select,
      'with' => [
        'r_user' => [
          'select' => $select_user
        ],
        'r_lottery' => [
          'select' => $select_lottery
        ]
      ]
    ));

    $cdb->addCondition('`show`=:show AND fulltime>0');
    $cdb->params[':show'] = Goods::SHOW_OPEN;

    $pages = new CPagination(Goods::model()->count($cdb));
    $pages->pageSize = $this->request->getParam('pageSize', 20);
    $pages->applyLimit($cdb);
    $datas = Goods::model()->findAll($cdb);

    $datas = array_map(function($v) use($select, $select_user, $select_lottery){
      $ret = $this->filterArray($select, $v);
      $ret['goodsStatus'] = Goods::checkGoodsStatus($v);
      if($v['opentime']>0)
      {
        $ret['r_user'] = $this->filterArray($select_user, $v['r_user']) ?: [];
        $ret['r_lottery'] = $this->filterArray($select_lottery, $v['r_lottery']) ?: [];
      }
      if($v['opentime']==0)
      {
        $ret['_willOpenTime'] = Goods::getWillOpenTime($v['fulltime']);
      }
      return $ret;
    }, $datas);

    $this->encodeJSON(200, [
      'count' => $pages->itemCount,
      'serverTime' => time(),
      'datas' => $datas,
    ]);
  }

  //商品库往期夺宝纪录
  private function _getGroupHistory()
  {
    $groupId = $this->getPhpInput('id');

    $select = 'id,opencode,opentime,times,group_times,thumb';
    $select_user = 'id,nickname,wechatId,face';

    $cdb = new CDbCriteria(array(
      'order' => '(opentime=0) desc,opentime desc',
      'select' => $select,
      'with' => [
        'r_user' => [
          'select' => $select_user
        ],
      ]
    ));

    $cdb->addCondition('`show`=:show AND groupid=:groupid');
    $cdb->params[':show'] = Goods::SHOW_OPEN;
    $cdb->params[':groupid'] = $groupId;

    $pages = new CPagination(Goods::model()->count($cdb));
    $pages->pageSize = $this->request->getParam('pageSize', 20);
    $pages->applyLimit($cdb);
    $datas = Goods::model()->findAll($cdb);

    $datas = array_map(function($v) use($select, $select_user){
      $ret = $this->filterArray($select, $v);
      $ret['goodsStatus'] = Goods::checkGoodsStatus($v);
      if($ret['goodsStatus'] == Goods::GOODS_STATUS_OPEN)
      {
        $ret['r_user'] = $this->filterArray($select_user, $v['r_user']) ?: [];
      }
      else
      {
        $ret['cacheTimes'] = Goods::getCacheTimes($v['id']);
      }
      return $ret;
    }, $datas);

    $this->encodeJSON(200, [
      'count' => $pages->itemCount,
      'serverTime' => time(),
      'datas' => $datas,
    ]);
  }

  //某个商品的夺宝参与纪录
  private function _getGoodsLotteryList()
  {
    $goodsId = $this->getPhpInput('id');
    $select = 'id,buytime,ip,ipstr,codecount';
    $select_user = 'id,nickname,face,wechatId';

    $cdb = new CDbCriteria(array(
      'order' => 't.id desc',
      'select' => $select,
      'with' => [
        'r_user' => [
          'select' => $select_user
        ],
      ]
    ));

    $cdb->addCondition('goods_id=:goods_id');
    $cdb->params[':goods_id'] = $goodsId;

    $pages = new CPagination(Lottery::model()->count($cdb));
    $pages->pageSize = $this->request->getParam('pageSize', 20);
    $pages->applyLimit($cdb);
    $datas = Lottery::model()->findAll($cdb);

    $datas = array_map(function($v) use($select, $select_user){
      $ret = $this->filterArray($select, $v);
      $ret['r_user'] = $this->filterArray($select_user, $v['r_user']) ?: [];
      return $ret;
    }, $datas);

    $this->encodeJSON(200, [
      'count' => $pages->itemCount,
      'datas' => $datas,
    ]);
  }

  //某次夺宝纪录的详细数据
  private function _getLottery()
  {
    $lotteryId = $this->getPhpInput('id');
    $select_user = 'id,nickname,face,wechatId';

    $datas = Lottery::model()->findByPk($lotteryId,[
      'with' => [
        'r_user' => [
          'select' => $select_user
        ],
      ]
    ]);

    $this->encodeJSON(200, $datas);
  }


  //某个商品的开奖计算详情
  private function _getGoodsCodeOpenDetail()
  {
    $goodsId = $this->getPhpInput('id');

    $retInfo = [];

    $goods = Goods::model()->findByPk($goodsId, [
      'select' => 'id,opencode,times',
      'condition' => 'opentime>0'
    ]);

    if(!$goods)
    {
      $this->encodeJSON(500, '还没有揭晓');
    }

    $retInfo['opencode'] = $goods['opencode'];
    $retInfo['times'] = $goods['times'];

    $lastBuy = Lottery::model()->find([
      'select' => 'buytime',
      'order' => 'buytime desc',
      'condition' => 'goods_id=:goods_id',
      'params' => [
        ':goods_id' => $goods['id']
      ]
    ]);

    if(!$lastBuy)
    {
      $this->encodeJSON(500, '没有夺宝纪录');
    }

    $retInfo['lastBuyTime'] = $lastBuy['buytime'];

    $select = 'buytime';
    $select_user = 'id,nickname';
    $buy100 = Lottery::model()->findAll([
      'select' => $select,
      'order' => 'buytime desc',
      'condition' => 'buytime < :buytime',
      'params' => [
        ':buytime' => $lastBuy['buytime']
      ],
      'limit' => 100,
      'with' => [
        'r_user' => [
          'select' => $select_user
        ]
      ]
    ]);

    $timeSum = 0;

    $retDatas = [];
    foreach ($buy100 as $k=>$v)
    {
      $ret = $this->filterArray($select, $v);

      $buytime = explode(' ', $v['buytime']);
      $buytime = explode('.', $buytime[1]);
      $us = $buytime[1];
      $buytime = explode(':', $buytime[0]);
      $ret['buytime_str'] = $buytime[0].$buytime[1].$buytime[2].$us;

      $timeSum += $ret['buytime_str'];

      $ret['r_user'] = $this->filterArray($select_user, $v['r_user']) ?: [];

      $retDatas[] = $ret;
    }

    $retInfo['timeSum'] = $timeSum;

    $this->encodeJSON(200, [
      'datas' => $retDatas,
      'info' => $retInfo
    ]);
  }



  //收藏栏商品
  private function _getFavGoods()
  {
    $groupIds = $this->getPhpInput('ids');
    $groupIds = explode(',', $groupIds);
    if(!$groupIds)
    {
      $this->encodeJSON(200, []);
    }

    $groupIds = array_slice($groupIds, 0, 20);

    //$select = 'id,thumb,group_times,title,price,times,fulltime,opentime,`show`';
    $select = 'id,thumb,title,price';

    $cdb = new CDbCriteria(array(
      'select' => $select,
    ));

    $cdb->addInCondition('t.id', $groupIds);

    $datas = Goods::model()->findAll($cdb);

    $datas = array_map(function($v) use($select){
      $ret = $this->filterArray($select, $v);
      //$ret['cacheTimes'] = Goods::getCacheTimes($v['id']);
      return $ret;
    }, $datas);

    $this->encodeJSON(200, $datas);
  }

}