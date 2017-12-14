<?php

class AdminModule extends CWebModule
{
	public function init()
	{
		$this->setImport(array(
			$this->id . '.models.*',
			$this->id . '.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			$noauth = array(
			  //无需验证权限的控制器
        'admin' => ['login'],
      );
			$controller_id = strtolower($controller->id);
			$action_id = strtolower($action->id);
			if( isset($noauth[$controller_id]) && in_array($action_id , $noauth[$controller_id]) )
			{
				return true;
			}
			else
			{
        if( ($userReturn = Admin::isactive()) !==true )
        {
          echo CJSON::encode([
            'status' => -501,
            'msg' => '未登录'
          ]);

          Yii::app()->end();
          return false;
        }
				return true;
			}
		}
		else
		{
			return false;
		}
	}
}
