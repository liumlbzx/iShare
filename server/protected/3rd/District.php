<?php

/**
 * 行政区
 * Class District
 */


class District {

  /**
   * 省市
   * @return mixed
   */
  public static function getAll()
  {
    $cache = Yii::app()->cache;
    $cacheKey = __CLASS__.'_'.__FUNCTION__;
    $retDatas = $cache->get($cacheKey);
    if(!$retDatas)
    {
      require_once __DIR__ . '/CCurlHelper.php';
      $url = 'http://restapi.amap.com/v3/config/district?key=5e8761621897f848028ee8ee6a527e17&showbiz=false&offset=500&output=json&subdistrict=3';
      $curl = new CCurlHelper();
      $ret = $curl->curlGet($url);
      $ret = json_decode($ret,1);
      if($ret)
      {
        if($ret['status']==1)
        {
          $datas = $ret['districts'][0]['districts'];
          foreach ($datas as $k=>$dd)
          {
            $v = $datas[$k];
            if( gettype($v['citycode']) === 'string' && preg_match('/^0\d+$/', $v['citycode']) )//额外处理 直辖市的下级城市
            {
              $_ds = [];

              foreach ($v['districts'] as $v1)
              {
                foreach ($v1['districts'] as $v2)
                {
                  $_ds[] = $v2;
                }
              }
              $v['districts'] = $_ds;
            }

            foreach ($v['districts'] as $k1=>$v1)
            {
              unset(
                $v['districts'][$k1]['districts'],
                $v['districts'][$k1]['center'],
                $v['districts'][$k1]['level'],
                $v['districts'][$k1]['citycode']
              );
            }

            unset($v['center'], $v['level'], $v['citycode']);

            $datas[$k] = $v;
          }

          $retDatas = $datas;
          $cache->set($cacheKey, $retDatas, 7*86400);
        }
      }
    }

    return $retDatas;
  }

  /**
   * 省市县
   * @return mixed
   */
  public static function getAll3()
  {
    $cache = Yii::app()->cache;
    $cacheKey = __CLASS__.'_'.__FUNCTION__;
    $retDatas = $cache->get($cacheKey);
    if(!$retDatas)
    {
      require_once __DIR__ . '/CCurlHelper.php';
      $url = 'http://restapi.amap.com/v3/config/district?key=5e8761621897f848028ee8ee6a527e17&showbiz=false&offset=500&output=json&subdistrict=3';
      $curl = new CCurlHelper();
      $ret = $curl->curlGet($url);
      $ret = json_decode($ret,1);
      if($ret)
      {
        if($ret['status']==1)
        {
          $outs = [];
          $datas = $ret['districts'][0]['districts'];
          foreach ($datas as $k=>$dd)
          {
            $v = $datas[$k];

            $c_province = [
              'adcode'=> $v['adcode'],
              'name'=> $v['name'],
            ];

            foreach ($v['districts'] as $k1=>$v1)
            {
              $c_city = [
                'adcode'=> $v1['adcode'],
                'name'=> $v1['name'],
              ];

              $c_area = [];
              foreach ($v1['districts'] as $k2=>$v2)
              {
                $c_area[] = [
                  'adcode'=> $v2['adcode'],
                  'name'=> $v2['name'],
                ];
              }
              $c_city['districts'] = $c_area;

              $c_province['districts'][] = $c_city;

            }

            $outs[] = $c_province;
          }

          $retDatas = $outs;
          $cache->set($cacheKey, $retDatas, 7*86400);
        }
      }
    }

    return $retDatas;
  }

  /**
   * 省份中文转code
   * @param $name
   * @return null
   */
  public static function provinceNameToCode($name)
  {
    $datas = self::getAll();
    $name = preg_replace('/省|市|(自治区)|(特别行政区)|(壮族)|(回族)|(维吾尔族{0,1})/u', '', $name);
    $code = null;
    if(mb_strlen($name,'utf-8') < 2)
    {
      return $code;
    }
    foreach ($datas as $v)
    {
      if( strpos($v['name'], $name) !== false)
      {
        $code = $v['adcode'];
        break;
      }
    }
    return $code;
  }

  /**
   * 城市中文转code
   * @param $provinceCode
   * @param $name
   * @return null
   */
  public static function cityNameToCode($provinceCode, $name)
  {
    $datas = self::getAll();
    $name = preg_replace('/省|市|(自治区)|(特别行政区)|(壮族)|(回族)|(维吾尔族{0,1})/u', '', $name);
    $code = null;
    if(mb_strlen($name,'utf-8') < 2)
    {
      return $code;
    }

    $pDatas = [];
    foreach ($datas as $v)
    {
      if($v['adcode'] === $provinceCode)
      {
        $pDatas = $v['districts'];
        break;
      }
    }

    if(!$pDatas)
    {
      return $code;
    }

    foreach ($pDatas as $v)
    {
      if( strpos($v['name'], $name) !== false)
      {
        $code = $v['adcode'];
        break;
      }
    }
    return $code;
  }


  /**根据区号获取城市或省份信息
   * @param $code
   */
  private static $_cityDatas=[];
  public static function getCityNameByCode($code)
  {
    if(!$code)
    {
      return null;
    }
    if(!self::$_cityDatas)
    {
      $data = [];
      $provinceDatas = self::getAll();
      foreach ($provinceDatas as $k=>$province)
      {
        $data[$province['adcode']] = $province['name'];
        if($province['districts'])
        {
          foreach ($province['districts'] as $city)
          {
            $data[$city['adcode']] = $city['name'];
          }
        }
      }
      self::$_cityDatas = $data;
    }

    return isset(self::$_cityDatas[$code]) ? self::$_cityDatas[$code] : null;
  }

}