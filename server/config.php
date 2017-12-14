<?php
return [
  //网站配置信息
  'website' => array(
    'webName' => 'iShare',
  ),

  //安全配置
  'safe' => [
    'user_expire' => 86400*7, //用户多少秒不活动,就丢失登陆状态
  ],

  //发邮件
  'mail' => [
    'email' => 'system-mail@ka44.cn',
    'account' => 'system-mail@ka44.cn',
    'pwd' => '111111111122',
    'smtp' => 'smtp.exmail.qq.com',
    'port' => 465
  ],

  //api
  'api' => array(
    //微信配置
    //线上
    'wechat' => [
      'appid' => 'wx7ceccb499bacf178', //公众号ID
      'appsecret' => '727e6eeb4437d94da214da16ade31689',//秘钥
    ],
    //用于支付的微信配置
    'wechatPay' => [
      'appid' => 'wx7ceccb499bacf178', //公众号ID
      'appsecret' => '727e6eeb4437d94da214da16ade31689',//秘钥
      'mch_id' => '1459926602', //商户号ID
      'payKey' => '9c948380b39ff8972698ffe54bb2799e' , //支付key
    ],
    //测试
//    'wechat' => [
//      'appid' => 'wxfea69b0dd46967be',
//      'appsecret' => 'abd5d49f95eed4babc232d64d8575717',
//    ],

    //阿里云短信
    'alisms' => [
      'key'=> 'LTAIdELatG63pqvY',
      'secret'=>'H5pfj9YlfvAdZ8LF8o3Y6dNNteATs1',
      'com'=> '阿里云短信测试专用',//'天狗夺宝', //签名
    ]
  ),

  //是否需要curl代理
  'proxy' => [
    'enable' => false,
    'proxy' => 'http://taoliujun:password2_@10.199.75.12:8080'
  ],


];