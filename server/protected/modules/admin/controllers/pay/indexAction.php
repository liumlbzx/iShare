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

  //列表
  private function _getList()
  {

    $cdb = new CDbCriteria(array(
      'order' => 't.id desc',
      'with' => [
        'r_user'
      ]
    ));

    foreach (['uid', 'sn', 'step', 'platform'] as $v)
    {
      if($q = $this->getPhpInput($v))
      {
        $cdb->addCondition("t.{$v}=:{$v}");
        $cdb->params[":{$v}"] = $q;
      }
    }

    $pages = new CPagination(Pay::model()->count($cdb));
    $pages->pageSize = $this->request->getParam('pageSize', 20);
    $pages->applyLimit($cdb);
    $datas = Pay::model()->findAll($cdb);

    $datas = array_map(function($v){
      $ret = $v->attributes;
      $ret['r_user'] = $this->filterArray('id,nickname', $v['r_user']) ?: [];
      return $ret;
    }, $datas);

    $this->encodeJSON(200, [
      'count' => $pages->itemCount,
      'datas' => $datas,
    ]);
  }

}