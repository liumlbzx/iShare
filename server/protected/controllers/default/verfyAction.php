<?php

class verfyAction extends GAction
{
	public function run()
	{
		$type = $this->request->getParam('type');
		Yii::import('application.3rd.CyzmHelper');
		$img = new CyzmHelper( $type , $this->request->getParam('w',0), $this->request->getParam('h',0), $this->request->getParam('t',false) );
		$img->run();
	}
}