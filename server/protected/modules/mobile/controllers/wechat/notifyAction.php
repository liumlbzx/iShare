<?php

class notifyAction extends Action
{
  public function run()
  {
    require_once Yii::getPathOfAlias('application.3rd').'/Wechat.php';
    $wechat = new Wechat();

    $data = $wechat->xmlToArray(file_get_contents("php://input"));

    Yii::log("接受到数据:\n".var_export($data, true), CLogger::LEVEL_INFO, 'wechat');

    $return_code = $data['return_code'];
    if($return_code !== 'SUCCESS')
    {
      //信息失败
      echo $wechat->arrayToXml([
        'return_code' => 'FAIL',
        'return_msg' => isset($data['return_msg']) ? $data['return_msg'] : '未知错误'
      ]);
      Yii::app()->end();
    }

    $result_code = $data['result_code'];
    if($result_code !== 'SUCCESS')
    {
      //数据失败
      echo $wechat->arrayToXml([
        'return_code' => 'FAIL',
        'return_msg' => $data['err_code_des'] . "({$data['err_code']})"
      ]);
      Yii::app()->end();
    }

    //验证签名
    $sign = $wechat->_paySign($data, Yii::app()->params['api']['wechatPay']);

    if($sign!== $data['sign'])
    {
      echo $wechat->arrayToXml([
        'return_code' => 'FAIL',
        'return_msg' => '签名验证失败(manual)'
      ]);
      Yii::app()->end();
    }

    $uid = $data['attach'];
    $total_fee = $data['total_fee']/100; //订单金额,分
    $trade_no = $data['transaction_id'];//平台订单号
    $out_trade_no = $data['out_trade_no']; //商户订单号

    if(!$uid)
    {
      echo $wechat->arrayToXml([
        'return_code' => 'FAIL',
        'return_msg' => '用户不存在'
      ]);
      Yii::app()->end();
    }

    $data = new Pay();
    $attr = [
      'money' => $total_fee,
      'createtime' => time(),
      'finishtime' => time(),
      'sn' => $trade_no,
      'uid' => $uid,
      'step' => Pay::STEP_PAY_SUCCESS,
      'platform' => Pay::PLATFORM_WECHAT_MOBILE
    ];

    $data->attributes = $attr;
    if(	$data->save(true , array_keys($attr)) )
    {
      //增加余额
      User::model()->updateCounters(
        [
          'money' => "+".$total_fee
        ],
        'id=:id',
        [
          ':id' => $uid
        ]
      );
      //资金明细
      Moneydetail::add(User::getUserId(), $total_fee, Moneydetail::TYPE_RECHARGE, '充值');
      //成功
      echo $wechat->arrayToXml([
        'return_code' => 'SUCCESS',
        'return_msg' => '订单更新成功'
      ]);
      Yii::app()->end();
    }
    else
    {
      echo $wechat->arrayToXml([
        'return_code' => 'FAIL',
        'return_msg' => '订单更新失败'
      ]);
      Yii::app()->end();
    }

    return false;

    //支付成功
    $row = Pay::model()->findByPk($out_trade_no);

    if($row)
    {
      if($row['step'] != Pay::STEP_CREATE)
      {
        echo $wechat->arrayToXml([
          'return_code' => 'FAIL',
          'return_msg' => '非待支付状态'
        ]);
        Yii::app()->end();
      }
      else
      {
        $attr = array(
          'finishtime' => time(),
          'sn' => $trade_no,
          'platform' => 'wechat',
          'step' => Pay::STEP_PAY_SUCCESS,
        );
        $row->attributes = $attr;
        if(	$row->save(true , array_keys($attr)) )
        {
          //成功
          echo $wechat->arrayToXml([
            'return_code' => 'SUCCESS',
            'return_msg' => '订单更新成功'
          ]);
          Yii::app()->end();
        }
        else
        {
          echo $wechat->arrayToXml([
            'return_code' => 'FAIL',
            'return_msg' => '订单更新失败'
          ]);
          Yii::app()->end();
        }
      }
    }
    else
    {
      echo $wechat->arrayToXml([
        'return_code' => 'FAIL',
        'return_msg' => '订单不存在'
      ]);
      Yii::app()->end();
    }

    Yii::app()->end();
  }
}
