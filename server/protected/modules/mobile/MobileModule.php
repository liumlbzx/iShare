<?php

class MobileModule extends CWebModule
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
        'goods' => ['index'],
        'member' => ['index'],
        'wechat' => ['wechat']
      );
      $controller_id = strtolower($controller->id);
      $action_id = strtolower($action->id);
      if( isset($noauth[$controller_id]) && in_array($action_id , $noauth[$controller_id]) )
      {
        return true;
      }
      else
      {
        if( ($userReturn = User::isactive()) !==true )
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
