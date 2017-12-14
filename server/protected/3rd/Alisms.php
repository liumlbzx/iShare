<?php
/**
 * 阿里云短信
 * https://help.aliyun.com/document_detail/56189.html?spm=5176.doc54229.6.562.q0DtZa
 */

class Alisms {

  private $_apiUrl = 'http://dysmsapi.aliyuncs.com/';
  private $_config = [];

  public function __construct($appKey, $appSecret, $com='')
  {
    $this->_config = [
      'key' => $appKey,
      'secret' => $appSecret,
      'com' => $com,
    ];
  }

  /**
   * 签名
   * @param $params
   * @param $method
   * @return string
   */
  private function _sign($params=[], $method='GET')
  {
    unset($params['Signature']);

    ksort($params);
    $canonicalizedQueryString = '';
    foreach ($params as $key => $value)
    {
      $canonicalizedQueryString .= '&' . $this->_utf8code($key). '=' . $this->_utf8code($value);
    }
    $stringToSign = strtoupper($method).'&%2F&' . $this->_utf8code(substr($canonicalizedQueryString, 1));
    $signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->_config['secret']."&", true));
    return $signature;
  }

  private function _utf8code($str)
  {
    $res = urlencode($str);
    $res = preg_replace('/\+/', '%20', $res);
    $res = preg_replace('/\*/', '%2A', $res);
    $res = preg_replace('/%7E/', '~', $res);
    return $res;
  }

  /**
   * 返回公共参数
   * @return array
   */
  private function _getPublicParams()
  {
    date_default_timezone_set("GMT");
    return [
      'RegionId' => 'cn-shanghai',
      'AccessKeyId' => $this->_config['key'],
      'Format' => 'JSON',
      'SignatureMethod' => 'HMAC-SHA1',
      'SignatureVersion' => '1.0',
      'SignatureNonce' => uniqid(),
      'Timestamp' => date('Y-m-d\TH:i:s\Z'),
      //'Action' => 'SendSms',
      'Version' => '2017-05-25',
    ];
  }

  /**
   * 发送短信
   * @param $tpl
   * @param $mobile
   * @param array $params
   * @return mixed
   */
  public function sendSms($tpl, $mobile, $params=[])
  {

    $params = array_merge(
      $this->_getPublicParams(),
      [
        'Action' => 'SendSms',
        'PhoneNumbers' => $mobile,
        'SignName' => ($this->_config['com']),
        'TemplateCode' => $tpl,
        'TemplateParam' => json_encode($params),
      ]
    );

    $params['Signature'] = $this->_sign($params, 'GET');

    if(YII_DEBUG)
    {
      //return true;
    }

    require_once Yii::getPathOfAlias('application.3rd').'/CCurlHelper.php';
    $curl = new CCurlHelper();
    $ret = $curl->curlGet($this->_apiUrl.'?'.http_build_query($params));
    return json_decode($ret, 1);
  }

  /**
   * 短信发送记录
   * @param $mobile
   * @param $date | 格式：yyyyMMdd
   * @param $pageSize
   * @param $currentPage
   * @return mixed
   */
  public function querySms($mobile, $date=null, $pageSize=50, $currentPage=1)
  {
    if(!$date)
    {
      $date = date('Ymd');
    }
    $params = array_merge(
      $this->_getPublicParams(),
      [
        'Action' => 'QuerySendDetails',
        'PhoneNumbers' => $mobile,
        'SendDate' => str_replace('-','',$date),
        'PageSize' => $pageSize,
        'CurrentPage' => $currentPage
      ]
    );

    $params['Signature'] = $this->_sign($params, 'GET');

    require_once Yii::getPathOfAlias('application.3rd').'/CCurlHelper.php';
    $curl = new CCurlHelper();
    $ret = $curl->curlGet($this->_apiUrl.'?'.http_build_query($params));
    return json_decode($ret, 1);
  }

}