<?php
class wechatAction extends GAction
{

  /**
   * @var Wechat
   */
  private $_wechat = null;
  private $_weConfig = null;
  public function run()
  {
    if(!$this->_wechat)
    {
      $this->_weConfig = Yii::app()->params['api']['wechat'];
      require_once Yii::getPathOfAlias('application.3rd').'/Wechat.php';
      $this->_wechat = new Wechat($this->_weConfig);
    }

    $do = $this->request->getParam('do');
    $func = '_'. $do;
    if( method_exists($this, $func) )
    {
      $this->$func();
      Yii::app()->end();
    }

    $wechat = $this->_wechat;

    $echostr = $this->getPhpInput('echostr');
    //设置开发者
    if($echostr)
    {
      if($wechat->echostr(
        $this->getPhpInput('signature'),
        $this->getPhpInput('timestamp'),
        $this->getPhpInput('nonce'),
        $this->getPhpInput('echostr')
      ))
      {
        echo $echostr;
      }
      else
      {
        echo 'error echostr';
      }
      Yii::app()->end();
    }

    $input = file_get_contents('php://input');
    $arr = $wechat->xmlToArray($input);

    $MsgType = '';
    if(is_array($arr))
    {
      $MsgType = CFuncHelper::kArr($arr, 'MsgType');
    }

    //事件推送
    if($MsgType === 'event')
    {
      $Event = strtolower(CFuncHelper::kArr($arr, 'Event'));
      if($Event)
      {
        $func = '_'. $MsgType. '_'. $Event;
        if( method_exists($this, $func) )
        {
          $this->$func($arr);
          Yii::app()->end();
        }
      }
    }

  }

  /**
   * 关注
   * @param $weArr
   */
  private function _event_subscribe($weArr)
  {
    $EventKey = CFuncHelper::kArr($weArr, 'EventKey');
    $FromUserName = CFuncHelper::kArr($weArr, 'FromUserName');

    Yii::app()->cache->delete( 'checkWechatSubscribe' . $this->_weConfig['appid'] . '_' . $FromUserName );

    //普通关注
    echo $this->_wechat->arrayToXml([
      'ToUserName' => $FromUserName,
      'FromUserName' => CFuncHelper::kArr($weArr, 'ToUserName'),
      'CreateTime' => time(),
      'MsgType' => 'text',
      'Content' => '你好，欢迎关注' . Yii::app()->params['website']['webName']
    ]);
  }

  /**
   * 取消关注
   * @param $weArr
   */
  private function _event_unsubscribe($weArr)
  {
    $FromUserName = CFuncHelper::kArr($weArr, 'FromUserName');
    Yii::app()->cache->delete( 'checkWechatSubscribe' . $this->_weConfig['appid'] . '_' . $FromUserName );
  }

  private function _echoToken()
  {
    var_dump($this->_wechat->getAccessToken());
  }

}