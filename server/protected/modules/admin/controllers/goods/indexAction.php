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

  //新增/编辑
  private function _add()
  {
    $updateTimes = false;

    $f = [];

    $id = $this->getPhpInput('id');

    $goodsGroupStockFoll = false;

    if($id)
    {
      $data = (new Goods('add'))->findByPk($id);
      if(!$data)
      {
        $this->encodeJSON(500, '修改失败');
      }

      foreach (['title', 'title_sub', 'price', 'times', 'times_per', 'pics', 'thumb', 'detail', 'sort', 'show'] as $v)
      {
        if($q = $this->getPhpInput($v))
        {
          $f[$v] = $q;
        }
      }

      $goodsStatus = Goods::checkGoodsStatus($data);
      if($goodsStatus == Goods::GOODS_STATUS_OPEN || $goodsStatus == Goods::GOODS_STATUS_FULL)
      {
        foreach (['times', 'times_per'] as $v)
        {
          if( isset($f[$v]) )
          {
            unset($f[$v]);
          }
        }
      }
      else
      {
        $updateTimes = true;
      }

      $attr = $f;
    }
    else
    {
      foreach (['title', 'title_sub', 'price', 'times', 'times_per', 'pics', 'thumb', 'detail', 'sort', 'show', 'groupid'] as $v)
      {
        if($q = $this->getPhpInput($v))
        {
          $f[$v] = $q;
        }
      }

      $data = new Goods('add');
      $attr = array_merge($f, [
        'createtime' => time()
      ]);

      if(isset($attr['groupid']) && $attr['groupid'])
      {
        $goodsGroup = Goodsgroup::model()->findByPk($attr['groupid']);
        if(!$goodsGroup)
        {
          $this->encodeJSON(500, '商品库不存在');
        }
        if($goodsGroup['stock'] < 1)
        {
          $this->encodeJSON(500, '商品库库存不足');
        }
        $groupCount = Goods::model()->count('groupid=:groupid', [
          ':groupid' => $attr['groupid']
        ]);
        $attr['group_times'] = $groupCount+1;
        $goodsGroupStockFoll = true;
      }

    }

    if(!isset($attr['detail']))
    {
      $attr['detail'] = '暂无详情';
    }
    if( isset($attr['thumb']) && $attr['thumb'] && strpos($attr['thumb'], 'tmp/') )
    {
      $fileName = CFuncHelper::fromTmp($attr['thumb'], 'goods_thumb/'.date('Ymd').'/'.substr(md5(time().mt_rand(100000,999999)), 6, 10).'.%s');
      if( $fileName )
      {
        $attr['thumb'] = Yii::app()->params['uploadDomain1'] . $fileName;
      }
    }

    if( isset($attr['pics']) && $attr['pics'] )
    {
      foreach ($attr['pics'] as $k=>$pic)
      {
        if( strpos($pic, 'tmp/') )
        {
          $fileName = CFuncHelper::fromTmp($pic, 'goods_pics/'.date('Ymd').'/'.substr(md5(time().mt_rand(100000,999999)), 6, 10).'.%s');
          if( $fileName )
          {
            $attr['pics'][$k] = Yii::app()->params['uploadDomain1'] . $fileName;
          }

        }
      }
    }

    if( isset($attr['times_per']) && !$attr['times_per'] )
    {
      $attr['times_per'] = 0;
    }

    $data->attributes = $attr;
    if( $data->save(true, array_keys($attr)) )
    {
      if($updateTimes)
      {
        Lottery::clearGoodsTimes($data['id']);
      }
      if($goodsGroupStockFoll)
      {
        Goodsgroup::model()->updateCounters(
          [
            'stock' => -1
          ],
          'id=:id',
          [
            ':id' => $data['groupid']
          ]
        );
      }
      $this->encodeJSON(200);
    }
    else
    {
      $errors = $data->errors;
      $this->encodeJSON(500, reset($errors)[0]);
    }
  }

  //单条
  private function _get()
  {
    $id = $this->getPhpInput('id');

    $data = Goods::model()->findByPk($id, [
      'with' => [
        'r_user'
      ]
    ]);
    if($data['id'])
    {
      $data = array_merge(
        $data->attributes,
        [
          'r_user' => $data['r_user'],
          'cacheTimes' => Goods::getCacheTimes($data['id']),
          'goodsStatus' => Goods::checkGoodsStatus($data)
        ]
      );
    }

    $this->encodeJSON(200, $data);
  }

  //列表
  private function _getList()
  {
    $goodsStatus = $this->getPhpInput('goodsStatus');

    $select = 'id,groupid,group_times,title,title_sub,price,times,times_per,sort,`show`,createtime,fulltime,opentime,opencode,openuid';

    $cdb = new CDbCriteria(array(
      'order' => 't.id desc',
      'select' => $select
    ));

    if($goodsStatus==Goods::GOODS_STATUS_NORMAL)
    {
      $cdb->addCondition('fulltime=0 AND opentime=0');
      $cdb->order = 'sort desc,id desc';
    }
    elseif($goodsStatus==Goods::GOODS_STATUS_FULL)
    {
      $cdb->addCondition('fulltime>0 AND opentime=0');
      $cdb->order = 'fulltime desc,id desc';
    }
    elseif($goodsStatus==Goods::GOODS_STATUS_OPEN)
    {
      $cdb->addCondition('fulltime>0 AND opentime>0');
      $cdb->order = 'opentime desc,t.id desc';
      $cdb->with = [
        'r_user'
      ];
    }

    foreach (['show'] as $v)
    {
      if($q = $this->getPhpInput($v))
      {
        $cdb->addCondition("t.{$v}=:{$v}");
        $cdb->params[":{$v}"] = $q;
      }
    }
    foreach (['title'] as $v)
    {
      if($q = $this->getPhpInput($v))
      {
        $cdb->addSearchCondition($v, $q);
      }
    }

    $pages = new CPagination(Goods::model()->count($cdb));
    $pages->pageSize = $this->request->getParam('pageSize', 20);
    $pages->applyLimit($cdb);
    $datas = Goods::model()->findAll($cdb);

    $datas = array_map(function($v) use($select){
      $ret = $this->filterArray($select, $v);
      $ret['r_user'] = $this->filterArray('id,nickname', $v['r_user']) ?: [];
      $ret['cacheTimes'] = Goods::getCacheTimes($v['id']);
      return $ret;
    }, $datas);

    $this->encodeJSON(200, [
      'count' => $pages->itemCount,
      'datas' => $datas,
    ]);
  }

}