<?php
/**
 * 微信通信
 */

class Wechat {

  private $_apiUrl = 'https://api.weixin.qq.com/cgi-bin/';
  public $_config = [];

  public function __construct($wechatConfig=[])
  {
    if(!$wechatConfig)
    {
      $wechatConfig = Yii::app()->params['api']['wechat'];
    }

    $this->_config = $wechatConfig;
  }

  //开发调试
  public function echostr($signature, $timestamp, $nonce, $echostr)
  {
    $arr = [$this->_config['token'], $timestamp, $nonce];
    sort($arr, SORT_STRING);
    $sha1 = sha1( implode('', $arr) );
    return $sha1 === $signature;
  }

  /**
   * 授权页面网址
   * @param $redirect_uri
   * @param string $scope
   * @return string
   */
  public function oauth2_authorize($redirect_uri, $scope = 'snsapi_base')
  {
    $config = $this->_config;
    $baseUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    $params = [
      'appid' => $config['appid'],
      'redirect_uri' => $redirect_uri,
      'response_type' => 'code',
      'scope' => $scope,
      'state' => 'abcdefg',
    ];
    $url = $baseUrl . '?' . http_build_query($params) . '#wechat_redirect';
    return $url;
  }


  /**
   * jsapi签名
   * @param $redirect_uri
   * @return array
   */
  public function js_sign($redirect_uri)
  {
    $ticket = $this->getJsapiTicket();
    $noncestr = $this->_randomStr();
    $timestamp = time();

    $params = [
      'noncestr' => $noncestr,
      'jsapi_ticket' => $ticket,
      'timestamp' => $timestamp,
      'url' => $redirect_uri
    ];

    //签名步骤一：按字典序排序参数
    ksort($params);
    $string = '';
    foreach ($params as $k => $v)
    {
      if (!$v || $k === 'sign')
      {
        continue;
      }
      $string .= "{$k}={$v}&";
    }
    $string = trim($string, '&');
    $string = sha1($string);

    $config = $this->_config;
    return [
      'appId' => $config['appid'],
      'timestamp' => $timestamp,
      'nonceStr' => $noncestr,
      'signature' => $string
    ];

  }

  /**
   * 获取jsapi_ticket
   */
  private $_jsapi_ticket = null;
  public function getJsapiTicket()
  {
    $accessToken = $this->getAccessToken();
    if ($accessToken)
    {
      if (!$this->_jsapi_ticket)
      {
        $cache = Yii::app()->cache;
        $cacheKey = 'wechat_jsapi_ticket_'.$this->_config['appid'];
        $this->_jsapi_ticket = $cache->get($cacheKey);
        if (!$this->_jsapi_ticket)
        {
          //如果缓存里没有,则url
          $ret = $this->_get('ticket/getticket', [
            'access_token' => $accessToken,
            'type' => 'jsapi',
          ]);
          if ($ret && isset($ret['ticket']))
          {
            $this->_jsapi_ticket = $ret['ticket'];
            $cache->set($cacheKey, $ret['ticket'], $ret['expires_in'] - 600);
          }
          else
          {
            throw new CException('获取jsapi_ticket失败');
          }
        }
      }
      return $this->_jsapi_ticket;
    }
  }



  /**
   * code换取用户openid
   * @param $code
   * @return array
   */
  public function oauth2_openid($code)
  {
    $config = $this->_config;
    $url = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    $ret = $this->_get($url, [
      'appid' => $config['appid'],
      'secret' => $config['appsecret'],
      'code' => $code,
      'grant_type' => 'authorization_code',
    ]);
    if (!$ret)
    {
      return [
        'errcode' => -1,
        'errmsg' => 'empty ret'
      ];
    }
    return $ret;
  }

  /**
   * 返回用户基本信息
   * @param $wechatId
   * @return mixed
   */
  public function getUserInfo($wechatId)
  {
    $ret = $this->_get(
      'user/info',
      [
        'access_token'=> $this->getAccessToken(),
        'openid'=> $wechatId,
        'lang' => 'zh_CN'
      ]
    );
    return $ret;
  }

  /**
   * 获取微信全局accessToken
   */
  private $_accessToken = null;
  public function getAccessToken()
  {
    if( !$this->_accessToken )
    {
      $cache = Yii::app()->cache;
      $cacheKey = 'wechat_access_token_' . $this->_config['appid'];
      $this->_accessToken = $cache->get($cacheKey);
      if(!$this->_accessToken)
      {
        //如果缓存里没有,则url
        $ret = $this->_get(
          'token?grant_type=client_credential',
          [
            'appid'=> $this->_config['appid'],
            'secret'=> $this->_config['appsecret'],
          ]
        );
        if( $ret && isset($ret['access_token']) )
        {
          $this->_accessToken = $ret['access_token'];
          $cache->set($cacheKey, $ret['access_token'], $ret['expires_in'] - 600);
        }
        else
        {
          throw new CException('获取access_token失败');
        }
      }
    }
    return $this->_accessToken;
  }

  /**
   * 签名
   * @param $params
   * @return string
   */
  private function _sign($params=[])
  {
    ksort($params);
    $stringToBeSigned = $this->_config['appsecret'];
    foreach ($params as $k => $v)
    {
      if(is_string($v) && "@" != substr($v, 0, 1))
      {
        $stringToBeSigned .= "$k$v";
      }
    }
    unset($k, $v);
    $stringToBeSigned .= $this->_config['appsecret'];

    return strtoupper(md5($stringToBeSigned));
  }

  /**
   * 创建订单
   * @param $money
   * @param $orderId
   * @param $wechatId
   * @param $params
   * @return array|string
   */
  public function postPay($money, $orderId, $wechatId, $params=[])
  {
    $wechat = $this->_config;
    $wechatPay = Yii::app()->params['api']['wechatPay'];

    $payData = array(
      'appid' => $wechatPay['appid'],
      'mch_id' => $wechatPay['mch_id'],
      'nonce_str' => $this->_randomStr(32),
      'body' => Yii::app()->params['website']['webName'],
      'detail' => [
        'cost_price' => $money,
        'goods_detail' => [
          'goods_id' => $orderId,
          'goods_name' => Yii::app()->params['website']['webName'] . ' 订单',
          'quantity' => 1,
          'price' => $money
        ]
      ],
      'out_trade_no' => $orderId,
      'total_fee' => $money * 100 , //元 转 分
      'spbill_create_ip' => Yii::app()->request->getUserHostAddress(),
      'notify_url' => Yii::app()->getBaseUrl(true) . '/wechat_notify.php',
      'trade_type' => 'JSAPI',
      'openid' => $wechatId,
    );

    $payData = array_replace_recursive($payData, $params);
    $payData['detail'] = json_encode($payData['detail']);
    $payData['sign'] = $this->_paySign($payData, $wechatPay);

    $ret = $this->_post(
      'https://api.mch.weixin.qq.com/pay/unifiedorder',
      $payData,
      'xml2json'
    );

    if(is_array($ret) && isset($ret['errcode']))
    {
      return $ret['errmsg'].'('.$ret['errcode'].')';
    }

    if(is_array($ret) && isset($ret['return_code']))
    {
      if($ret['return_code'] === 'SUCCESS')
      {
        if($ret['result_code'] === 'SUCCESS')
        {
          $data = [
            "appId"=> $wechatPay['appid'],
            "timeStamp"=> time()."",
            "nonceStr"=> $this->_randomStr(),
            "package"=> "prepay_id={$ret['prepay_id']}",
            "signType"=> "MD5",
          ];
          $data['paySign'] = $this->_paySign($data, $wechatPay);
          return $data;
        }
        else
        {
          return '请求失败:' . $ret['err_code_des'] . '('.$ret['err_code'].')';
        }
      }
      else
      {
        return '请求失败:' . $ret['return_msg'];
      }
    }
    else
    {
      return '请求失败';
    }
  }

  /**
   * 支付签名
   * @param array $params
   * @return string
   */
  public function _paySign($params=[], $config=[])
  {
    //签名步骤一：按字典序排序参数
    ksort($params);
    $string = '';
    foreach ($params as $k=>$v)
    {
      if(!$v || $k === 'sign')
      {
        continue;
      }
      $string .= "{$k}={$v}&";
    }
    $string = trim($string, '&');
    //签名步骤二：在string后加入KEY
    $string = $string . "&key=". $config['payKey'];
    //签名步骤三：MD5加密
    $string = md5($string);
    //签名步骤四：所有字符转为大写
    $result = strtoupper($string);
    return $result;
  }





























































  /**
   * 返回长度不超过32位的随机字符串
   * @param int $len
   * @return string
   */
  protected function _randomStr($len = 32)
  {
    $ret = strtoupper(md5(mt_rand(10000, 99999) . '-' . microtime(true)));
    return substr($ret, 0, $len);
  }

  /**
   * 将array转为xml
   * @param array $datas
   * @return string
   * @throws CException
   */
  public function arrayToXml($datas=[])
  {
    if(!is_array($datas) || count($datas) <= 0)
    {
      throw new CException("数组数据异常！");
    }

    $xml = "<xml>";
    foreach ($datas as $key=>$val)
    {
      if (is_numeric($val))
      {
        $xml.="<".$key.">".$val."</".$key.">";
      }
      else
      {
        $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
      }
    }
    $xml.="</xml>";
    return $xml;
  }

  /**
   * 将xml转为array
   * @param string $xml
   * @return array
   */
  public function xmlToArray($xml)
  {
    if(!$xml)
    {
      return [];
    }
    libxml_disable_entity_loader(true);
    return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
  }

  /**
   * 以post方式提交
   *
   * @param string $url  url
   * @param array $params  需要post的数据
   * @param string $dataType 参数类型:json/xml/array
   * @param int $second   url执行超时时间，默认30s
   * @return string
   */
  protected function _post($url, $params=[], $dataType='xml', $second = 10)
  {
    $url = $this->_apiUrl . $url;
    if($dataType==='xml')
    {
      $params = $this->arrayToXml($params);
    }
    elseif($dataType==='xml2json')
    {
      $params = $this->arrayToXml($params);
    }
    elseif($dataType==='json')
    {
      $params = json_encode($params, JSON_UNESCAPED_UNICODE);
    }

    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);
    //如果有配置代理这里就设置代理
    $proxy = Yii::app()->params['proxy'];
    if(isset($proxy['enable']) && $proxy['enable'])
    {
      curl_setopt($ch,CURLOPT_PROXY, $proxy['proxy']);
    }
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //post提交方式
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    //运行curl
    $data = curl_exec($ch);
    //返回结果
    if($data)
    {
      curl_close($ch);
      if($dataType==='xml')
      {
        $retData = $this->xmlToArray($data);
      }
      elseif($dataType==='xml2json')
      {
        $retData = json_decode($data,1);
      }
      elseif($dataType==='json')
      {
        $retData = json_decode($data,1);
      }
      else
      {
        $retData = $data;
      }
      if( $retData && isset($retData['errcode']) && $retData['errcode'] == 40001 )
      {
        Yii::app()->cache->delete('wechat_access_token_'.$this->_config['appid']);
      }
      return $retData;
    }
    else
    {
      $error = curl_errno($ch);
      curl_close($ch);
      return [
        'return_code' => 'FAIL',
        'return_msg' => "curl出错，错误码:{$error}"
      ];
    }
  }

  /**
   * 以get方式提交
   *
   * @param string $url  url
   * @param string $data
   * @param int $second   url执行超时时间，默认30s
   * @return string
   */
  protected function _get($url, $data, $second = 10)
  {
    if( strpos($url, 'http') !== 0 )
    {
      $url = $this->_apiUrl . $url;
    }
    if(strpos($url, '?'))
    {
      $url .= '&' . http_build_query($data);
    }
    else
    {
      $url .= '?' . http_build_query($data);
    }
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);
    //如果有配置代理这里就设置代理
    $proxy = Yii::app()->params['proxy'];
    if(isset($proxy['enable']) && $proxy['enable'])
    {
      curl_setopt($ch,CURLOPT_PROXY, $proxy['proxy']);
    }
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //运行curl
    $data = curl_exec($ch);
    //返回结果
    curl_close($ch);

    $retData = json_decode($data, 1);
    if( $retData && isset($retData['errcode']) && $retData['errcode'] == 40001 )
    {
      Yii::app()->cache->delete('wechat_access_token_'.$this->_config['appid']);
    }

    return $retData;
  }


}