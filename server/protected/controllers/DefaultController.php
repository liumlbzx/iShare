<?php
class defaultController extends GController
{
	/**
	 * 转发动作
	 * @return array
	 */
	public function actions()
	{
		$control = 'application.controllers.'.$this->id.'.';
		$actions = 'index,options,verfy,error,district,admin,wechat,download';
		$ret = array();
		foreach(explode(',' , $actions) as $val)
		{
			$val = trim($val);
			if( !$val )
			{
				continue;
			}
			$ret[$val] = "{$control}{$val}Action";
		}
		return $ret;
	}

	/**
	 * error
	 */
	public function actionError()
	{
		$err = Yii::app()->errorHandler->error;
		header('content-Type:text/html;charset=utf-8');
		//echo '错误状态：' . $err['code'];
		//echo '<br/>';
		echo $err['message'];
		Yii::app()->end();

	}

}