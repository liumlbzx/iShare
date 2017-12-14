<?php

/**
 * 开奖
 */
class GoodsCommand extends CConsoleCommand
{
  private $openInterval = 3; //每隔几秒检查一次开奖


	/**
	 * 开奖
	 */
	public function actionOpen()
	{
	  $willOpenTime = Yii::app()->params['website']['willOpenTime'];
	  while(true)
    {
      //检查满员的商品
      $goodsArr = Goods::model()->findAll([
        'order' => 'fulltime asc',
        'select' => 'id,title,group_times,times',
        'condition' => 'fulltime>0 AND opentime=0 AND fulltime<=:time1',
        'params' => [
          ':time1' => time() - $willOpenTime
        ]
      ]);

      foreach ($goodsArr as $goods)
      {
        $this->_echo("(第{$goods['group_times']}次){$goods['title']}正在开奖...");
        flush();

        $lastBuy = Lottery::model()->find([
          'select' => 'buytime',
          'order' => 'buytime desc',
          'condition' => 'goods_id=:goods_id',
          'params' => [
            ':goods_id' => $goods['id']
          ]
        ]);

        if(!$lastBuy)
        {
          $this->_errorReport("{$goods['id']}、{$goods['title']} 没有夺宝纪录");
          flush();
          continue;
        }

        $select = 'buytime';
        $buy100 = Lottery::model()->findAll([
          'select' => $select,
          'order' => 'buytime desc',
          'condition' => 'buytime < :buytime',
          'params' => [
            ':buytime' => $lastBuy['buytime']
          ],
          'limit' => 100,
        ]);

        $timeSum = 0;

        foreach ($buy100 as $k=>$v)
        {
          $buytime = explode(' ', $v['buytime']);
          $buytime = explode('.', $buytime[1]);
          $us = $buytime[1];
          $buytime = explode(':', $buytime[0]);
          $timeSum += $buytime[0].$buytime[1].$buytime[2].$us;
        }

        $openCode = $timeSum % $goods['times'] + 100001;

        $lotterys = Lottery::model()->findAll([
          'select' => 'id,uid,code',
          'condition' => 'goods_id=:goods_id',
          'params' => [
            ':goods_id' => $goods['id']
          ]
        ]);

        $openUid = $openLottery = '';
        foreach ($lotterys as $lottery)
        {
          if( in_array($openCode,$lottery['code']) !== false )
          {
            $openUid = $lottery['uid'];
            $openLottery = $lottery['id'];
            break;
          }
        }

        if(!$openUid || !$openLottery)
        {
          $this->_errorReport("{$goods['id']}、{$goods['title']} 开奖失败");
          flush();
          continue;
        }

        $openTime = time();
        $this->_echo("{$goods['title']} 开奖码：{$openCode}，开奖时间：".date('Y-m-d H:i:s',$openTime));
        flush();

        Goods::model()->updateByPk($goods['id'], [
          'opentime' => $openTime,
          'opencode' => $openCode,
          'openuid' => $openUid,
          'openlottery' => $openLottery
        ]);

        $userinfo = User::getUser($openUid, 'mobile,info');
        $info = $userinfo['info'];

        if($userinfo['mobile'])
        {
          Sms::sendSms($userinfo['mobile'], Sms::TYPE_LOTTERY, [
            'title' => $goods['title']
          ]);
        }

        $express = new Express();
        $expressAttr = [
          'uid' => $openUid,
          'goodsid' => $goods['id'],
          'status' => Express::STATUS_WAIT_ADDRESS,
          'cityid' => CFuncHelper::kArr($info, 'shouhuo_cityid'),
          'address' => CFuncHelper::kArr($info, 'shouhuo_address'),
          'fulladdress' => CFuncHelper::kArr($info, 'shouhuo_cityname') . CFuncHelper::kArr($info, 'shouhuo_address'),
          'contact' => CFuncHelper::kArr($info, 'shouhuo_contact'),
          'mobile' => CFuncHelper::kArr($info, 'shouhuo_mobile'),
        ];
        $express->attributes = $expressAttr;
        $express->save(true, array_keys($expressAttr));


      }

      $this->_echo('本轮处理完成');
      flush();

      sleep($this->openInterval);
    }

    $this->_echo('处理完成');
    flush();

    return 0;

	}

  /**
   * 日志通知
   * @param $err
   */
  private function _errorReport($err)
  {
    return Yii::log($err, CLogger::LEVEL_ERROR);
  }

  /**
   * 输出
   * @param $str
   */
  private function _echo($str)
  {
    echo '['.date('Y-m-d H:i:s').'] '.$str."\n";
  }

}