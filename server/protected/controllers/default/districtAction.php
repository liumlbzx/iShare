<?php

class districtAction extends GAction
{
	public function run()
	{
	  require_once Yii::getPathOfAlias('application.3rd') . '/District.php';

    $level = $this->getPhpInput('level', 2);

    if($level==3)
    {
      $datas = District::getAll3();
    }
    else
    {
      $datas = District::getAll();
    }

    $this->encodeJSON(200, $datas);
	}

}