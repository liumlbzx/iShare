<?php
class CCurlHelper
{
	private $_curl;
	private $_url = null;
	public function __construct( )
	{
		$this->_open();
	}

	public function __destruct()
	{
		curl_close($this->_curl);
	}

	public function getCurl()
	{
		return $this->_curl;
	}

	public function getError()
	{
		return curl_error($this->_curl);
	}

	public function getInfo($option)
	{
		return curl_getinfo( $this->_curl , $option );
	}

	public function curlPost($url, $data)
	{
	  curl_setopt( $this->_curl , CURLOPT_URL, $url);
		curl_setopt( $this->_curl , CURLOPT_POST , true);
		curl_setopt( $this->_curl , CURLOPT_POSTFIELDS , $data);
		$ret = curl_exec( $this->_curl );
		return $ret;
	}

	public function curlGet($url)
	{
    curl_setopt( $this->_curl , CURLOPT_URL, $url);
    curl_setopt( $this->_curl , CURLOPT_POST , false);
		$ret = curl_exec( $this->_curl );
		return $ret;
	}

	private function _open()
	{
		$curlHandle = curl_init();
		curl_setopt( $curlHandle , CURLOPT_URL , $this->_url ); //指定url
		curl_setopt( $curlHandle , CURLOPT_RETURNTRANSFER , true ); //返回源码
		curl_setopt( $curlHandle , CURLOPT_SSL_VERIFYPEER, false); //关闭ssl
		curl_setopt( $curlHandle , CURLOPT_USERAGENT , isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');//模拟一个header
		curl_setopt( $curlHandle , CURLOPT_TIMEOUT, 30 ); //读取的最大时间秒
		curl_setopt( $curlHandle , CURLOPT_FRESH_CONNECT ,true); //关闭内容缓存
    if( Yii::app()->params['proxy']['enable'] )
    {
      curl_setopt( $curlHandle , CURLOPT_PROXY , Yii::app()->params['proxy']['proxy'] );
    }
		$this->_curl = $curlHandle;
	}

}