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

    $f = [];

    $id = $this->getPhpInput('id');

    if($id)
    {
      $data = (new Goodsgroup('add'))->findByPk($id);
      if(!$data)
      {
        $this->encodeJSON(500, '修改失败');
      }

      foreach (['title', 'title_sub', 'price', 'times', 'times_per', 'pics', 'thumb', 'detail', 'stock', 'auto_release'] as $v)
      {
        if($q = $this->getPhpInput($v))
        {
          $f[$v] = $q;
        }
      }

      $attr = $f;
    }
    else
    {
      foreach (['title', 'title_sub', 'price', 'times', 'times_per', 'pics', 'thumb', 'detail', 'stock', 'auto_release'] as $v)
      {
        if($q = $this->getPhpInput($v))
        {
          $f[$v] = $q;
        }
      }

      $data = new Goodsgroup('add');
      $attr = array_merge($f, [
        'createtime' => time()
      ]);

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

    $data = Goodsgroup::model()->findByPk($id);
    if(!$data)
    {
      $this->encodeJSON(500, '数据不存在');
    }

    $this->encodeJSON(200, $data);
  }

  //列表
  private function _getList()
  {
    $select = 'id,title,title_sub,price,times,times_per,stock,auto_release,createtime';

    $cdb = new CDbCriteria(array(
      'order' => 't.id desc',
      'select' => $select
    ));

    foreach (['auto_release'] as $v)
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

    $pages = new CPagination(Goodsgroup::model()->count($cdb));
    $pages->pageSize = $this->request->getParam('pageSize', 20);
    $pages->applyLimit($cdb);
    $datas = Goodsgroup::model()->findAll($cdb);

    $datas = array_map(function($v) use($select){
      $ret = $this->filterArray($select, $v);
      return $ret;
    }, $datas);

    $this->encodeJSON(200, [
      'count' => $pages->itemCount,
      'datas' => $datas,
    ]);
  }

}