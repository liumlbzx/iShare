<?php

class optionsAction extends GAction
{
  public function run()
  {
    $do = $this->request->getParam('do');

    if($do==='get')
    {
      $retArr = [];

      $models = explode(',', $this->getPhpInput('m'));

      foreach ($models as $m)
      {
        if ($m==='user')
        {
          $retArr[$m] = [
            'status' => User::STATUS,
            'source' => User::SOURCE
          ];
        }
        elseif ($m==='goods')
        {
          $retArr[$m] = [
            'show' => Goods::SHOW,
          ];
        }
        elseif ($m==='goodsgroup')
        {
          $retArr[$m] = [
            'auto_release' => Goodsgroup::AUTO_RELEASE,
          ];
        }
        elseif ($m==='pay')
        {
          $retArr[$m] = [
            'step' => Pay::STEP,
            'platform' => Pay::PLATFORM,
          ];
        }
        elseif( $m === 'msg')
        {
          $retArr[$m] = [
            'module' => Msg::MODULE,
            'type' => Msg::TYPE,
            'status' => Msg::STATUS,
          ];
        }
        elseif( $m === 'express')
        {
          $retArr[$m] = [
            'status' => Express::STATUS,
            'kdtype' => Express::KDTYPE
          ];
        }
        elseif( $m === 'moneydetail')
        {
          $retArr[$m] = [
            'type' => Moneydetail::TYPE,
          ];
        }
      }

      $this->encodeJSON(200, $retArr);

    }

  }

}