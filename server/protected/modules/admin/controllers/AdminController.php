<?php
class adminController extends GController
{
	/**
	 * 转发动作
	 * @return array
	 */
	public function actions()
	{
		$control = 'application.modules.'.$this->module->id.'.controllers.'.$this->id.'.';
		$actions = '
		login,logout,modifypwd';
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

}