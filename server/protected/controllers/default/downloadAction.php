<?php

class downloadAction extends GAction
{
	public function run()
	{
    $filename = urldecode($this->getPhpInput('filename'));
    $url = urldecode($this->getPhpInput('url'));

    require_once Yii::getPathOfAlias('application.3rd') . '/CCurlHelper.php';
    $curl = new CCurlHelper();
    $ret = $curl->curlGet($url);

    Yii::app()->request->sendFile($filename, $ret);
	}

}