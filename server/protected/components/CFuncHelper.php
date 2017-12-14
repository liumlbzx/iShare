<?php
/**
 * 常用函数
 */
class CFuncHelper
{
  /**
   * 格式化手机
   * @param $str
   * @return string
   */
  public static function format_mobile($str)
  {
    return substr($str, 0, 5) . '****' . substr($str, 9);
  }

  /**
   * 返回上传真实域名
   * @param $file
   * @return mixed|string
   */
  public static function getUploadRealUrl($file = null, $nodefault = true)
  {
    return $file ? str_replace('{URL}', Yii::app()->params['uploadDomain1'], $file) : ($nodefault === true ? null : Yii::app()->params['staticDomain1'] . 'img/loading.png');
  }

  /**
   * 返回上传虚拟域名
   * @param $file
   * @return mixed|string
   */
  public static function getUploadVirtulUrl($file = null, $nodefault = true)
  {
    return str_replace(Yii::app()->params['uploadDomain1'], '{URL}', $file ? $file : ($nodefault === true ? null : Yii::app()->params['staticDomain1'] . 'img/loading.png'));
  }


  /**
   * 删除临时文件
   * @param int $expire
   */
  public static function clearTmp($expire = 86400)
  {
    $tmpPath = Yii::app()->params['uploadPath'] . 'tmp/';
    if (is_dir($tmpPath))
    {
      $now = time();
      foreach (glob($tmpPath . '*') as $file)
      {
        $mtime = filemtime($file);
        if ($now - $mtime > $expire)
        {
          unlink($file);
        }
      }
    }
  }

  /**
   * 临时文件移动到真实文件
   * @param $url
   * @param $targetFile
   * @return string
   */
  public static function fromTmp($url, $targetFile)
  {
    $url = substr($url, 0, strpos($url,'?'));
    $uploadPath = Yii::app()->params['uploadPath'];
    $uploadDomain1 = Yii::app()->params['uploadDomain1'];

    $file = str_replace($uploadDomain1, '', $url);
    $file = str_replace('..', '',$file); //防止复制其它目录
    $file = $uploadPath . $file;

    if( strpos($file, '/tmp/') < 1 )
    {
      return false;
    }

    $pathinfo = pathinfo($file);
    $ext = strtolower( $pathinfo['extension'] );

    $targetFile = sprintf($targetFile, $ext);
    $targetPath = $uploadPath . $targetFile;

    if( file_exists($file) )
    {
      require_once Yii::getPathOfAlias('application.3rd') . '/CDirHelper.php';
      CDirHelper::mkdirs(dirname($targetPath));
      copy($file, $targetPath);
      unlink($file);
      return $targetFile;
    }
    else
    {
      return false;
    }
  }

	/**
	 * 输出JSONP
	 * @param array $data
	 * @return string
	 */
	public static function encodeJSONP($data)
	{
		header('Content-type:text/javascript;charset=utf-8');
		$callback = Yii::app()->request->getParam('callback' , 'jQuery' . time() );
		echo $callback . '(' . CJSON::encode($data) . ')';
    self::disableLog();
    Yii::app()->end();
	}

	/**
	 * 输出json
	 * @param array $data
	 * @return string
	 */
	public static function encodeJSON($data)
	{
		header('Content-type:application/json;charset=utf-8');
		echo CJSON::encode($data);
    self::disableLog();
		Yii::app()->end();
	}

	/**
	 * 关闭日志
	 */
	public static function disableLog()
	{
	  if( !Yii::app()->request->getParam('_showLog') )
    {
      foreach(Yii::app()->log->getRoutes() as $k=>$route)
      {
        if( in_array( $k , ['CProfileLogRoute' , 'CWebLogRoute']) )
        {
          $route->enabled = false;
        }
      }
    }
	}

	//从数组中获取索引值
	public static function kArr($values, $key, $default=null)
  {
    return isset($values[$key]) ? $values[$key] : $default;
  }

  /**
   * 返回html过滤的json
   * @param $arr
   * @return string
   */
  public static function secJSON($arr=[], $isArr=false)
  {
    foreach ($arr as $k=>$v)
    {
      if(is_array($v) || is_object($v))
      {
        $arr[$k] = self::secJSON($v, true);
      }
      else
      {
        $arr[$k] = CHtml::encode($v);
      }
    }
    if($isArr)
    {
      return $arr;
    }
    else
    {
      return json_encode($arr);
    }
  }

}

