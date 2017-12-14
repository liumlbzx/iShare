<?php

class wechatAction extends Action
{

  public function run()
  {
    $do = $this->request->getParam('do');

    require_once Yii::getPathOfAlias('application.3rd').'/Wechat.php';

    $func = '_' . $do;
    if (method_exists($this, $func))
    {
      $this->$func();
    }
  }

  //授权页面网址
  private function _authorize()
  {
    $wechat = new Wechat();
    $this->encodeJSON(200, [
      'url' => $wechat->oauth2_authorize(Yii::app()->createAbsoluteUrl('mobile/wechat/wechat', [
          'do' => 'get_openid',
          'redirect' => urldecode($this->getPhpInput('redirect', $this->request->urlReferrer))
        ]))
    ]);
  }

  //获取网页授权用户openid
  private function _get_openid()
  {
    $code = $this->request->getParam('code');
    if (!$code)
    {
      exit('用户拒绝授权');
    }

    if (YII_DEBUG === true)
    {
      $ret = [
        'openid' => $this->request->getParam('openid') ?: 'ok5CbszxbyXTmjD1hVmT27kkfIV1'
      ];
    }
    else
    {
      $wechat = new Wechat();
      $ret = $wechat->oauth2_openid($code);
    }

    if (isset($ret['errcode']))
    {
      exit('授权出错:' . $ret['errmsg'] . "({$ret['errcode']})");
    }
    elseif (isset($ret['openid']))
    {
      $openId = $ret['openid'];
      $redirect = $this->request->getParam('redirect', Yii::app()->createAbsoluteUrl('mobile/default/index'));

      //根据openid登录用户或创建用户
      if ($openId)
      {
        $model = User::model();
        $userCount = $model->count('wechatId=:wechatId', [
          ':wechatId' => $openId
        ]);
        if (!$userCount)
        {
          //注册
          $wechat = new Wechat();
          $wechatUserInfo = $wechat->getUserInfo($openId);
          if(!$wechatUserInfo || !isset($wechatUserInfo['openid']))
          {
            exit('获取微信用户信息失败');
          }
          $nickname = CFuncHelper::emoji_reject($wechatUserInfo['nickname']);
          if(!$nickname)
          {
            $nickname = '微信用户'.mt_rand(10000000, 99999999);
          }
          $m = new User('register');
          $attr = [
            'id' => substr(time(), 0, 5) . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'username' => 'wechat_' . substr(md5($openId), 10, 10),
            'password' => md5($openId . microtime()),
            'nickname' => $nickname,
            'face' => 'wechat_face',
            'source' => User::SOURCE_SELF,
            'wechatId' => $openId,
            'regtime' => time(),
            'info' => [
              'regTime' => time(),
              'city' => CFuncHelper::kArr($wechatUserInfo, 'city'),
              'province' => CFuncHelper::kArr($wechatUserInfo, 'province'),
            ],
            'status' => User::STATUS_OPEN
          ];
          $m->attributes = $attr;
          $m->save(true , array_keys($attr));
        }

        if ($row = $model->wechatLogin($openId))
        {
          //跳转
          $urlP = 'token=' . $row['token'] . '&expire='. Yii::app()->params['safe']['user_expire'];
          $rUrl = $redirect;
          if (strpos($rUrl, '?'))
          {
            $rUrl .= '&' . $urlP;
          }
          else
          {
            $rUrl .= '?' . $urlP;
          }
          Yii::app()->request->redirect($rUrl);
        }
        else
        {
          $errors = $model->getErrors();
          $errors = reset($errors);
          exit('登陆失败:' . $errors[0]);
        }
      }

    }
    else
    {
      exit('未知错误');
    }

  }

  //jssdk 签名
  private function _js_sign()
  {
    $wechat = new Wechat();
    $this->encodeJSON(200, $wechat->js_sign($this->request->getParam('redirect_uri')));
  }

  //获取jsapi_ticket签名和appid
  private function _getJsapiTicket()
  {
    $wechat = new Wechat();
    $this->encodeJSON(200, [
      'jsapi_ticket' => $wechat->getJsapiTicket(),
      'appid' => $wechat->_config['appid']
    ]);

  }


}