<?php
/**
 * Description
 * Date: 2012-03-14 15:14
 */

Class verfyWidget extends CWidget
{
	public $type = null;
	public function run()
	{
		echo('<img src="' . Yii::app()->createUrl('ajax/verfy' , array('type' => $this->type)) . '" alt="" title="点击更换验证码" onclick="javascript:this.src=(this.src+\'&_=\' + Math.random());" id="'.__CLASS__ . $this->type .'" style="cursor:pointer;">');
	}

}