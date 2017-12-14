<?php

class adminAction extends GAction
{
	public function run()
	{
		$pwd = $this->request->getParam('pwd');
    if($pwd !== 'fdj2udfhpap2')
    {
      exit;
    }

    $do = $this->request->getParam('do');

    if($do === 'clearTableCache')
    {
      Yii::app()->db->schema->getTables();
      Yii::app()->db->schema->refresh();
      var_dump(Yii::app()->db->schema->getTables());
      exit;
    }


    exit;
	}

}