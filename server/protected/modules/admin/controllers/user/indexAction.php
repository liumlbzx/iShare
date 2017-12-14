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

    foreach (['nickname', 'source', 'status'] as $v)
    {
      if($q = $this->getPhpInput($v))
      {
        $f[$v] = $q;
      }
    }

    $id = $this->getPhpInput('id');

    if(!$id)
    {
      $this->encodeJSON(500, '修改失败');
    }

    $data = (new User())->findByPk($id);
    if(!$data)
    {
      $this->encodeJSON(500, '修改失败');
    }
    $data->attributes = $f;
    if( $data->save(true, array_keys($f)) )
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

    $data = User::model()->findByPk($id);

    $this->encodeJSON(200, $data);
  }

  //列表
  private function _getList()
  {
    $cdb = new CDbCriteria(array(
      'order' => 'id desc',
    ));

    foreach (['nickname', 'mobile', 'status', 'source'] as $v)
    {
      if($q = $this->getPhpInput($v))
      {
        $cdb->addCondition("{$v}=:{$v}");
        $cdb->params[":{$v}"] = $q;
      }
    }

    $pages = new CPagination(User::model()->count($cdb));
    $pages->pageSize = $this->request->getParam('pageSize', 20);
    $pages->applyLimit($cdb);
    $datas = User::model()->findAll($cdb);

    $this->encodeJSON(200, [
      'count' => $pages->itemCount,
      'datas' => $datas,
    ]);
  }

}