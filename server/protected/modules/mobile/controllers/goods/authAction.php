<?php

class authAction extends Action
{
  public function run()
  {
    $do = $this->request->getParam('do');

    $func = '_'. $do;
    if( method_exists($this, $func) )
    {
      $this->$func();
    }
  }

  private function _buyGoods()
  {
    $id = $this->getPhpInput('goodsId');
    $buyTimes = $this->getPhpInput('buyTimes');
    $autonext = $this->getPhpInput('autonext');

    $ret = Lottery::addTimes($id, User::getUserId(), $buyTimes);

    if(is_array($ret) && isset($ret['code']))
    {
      if($autonext && preg_match('/^GOODS_/', $ret['code']) )
      {
        $goods = Goods::model()->findByPk($id, [
          'select' => 'groupid'
        ]);
        if($goods && $goods['groupid'])
        {
          $newGoods = Goods::model()->find(
            [
              'select' => 'id',
              'order' => 'id desc',
              'condition' => 'groupid=:groupid',
              'params' => [
                ':groupid' => $goods['groupid']
              ]
            ]
          );
          if($newGoods && $newGoods['id']>$goods['id'])
          {
            $ret = Lottery::addTimes($newGoods['id'], User::getUserId(), $buyTimes);
            if(is_array($ret) && isset($ret['code']))
            {
              $this->encodeJSON(500, $ret['msg'], $ret['code']);
            }
            else
            {
              $this->encodeJSON(200, $ret);
            }

          }
        }
      }
      $this->encodeJSON(500, $ret['msg'], $ret['code']);
    }
    else
    {
      $this->encodeJSON(200, $ret);
    }
  }

}