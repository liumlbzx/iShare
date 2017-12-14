<?php
class GAction extends CAction
{

	/**
	 * 输出JSONP
   * @param $status
   * @param $mixed
   * @param string $flag
   */
	protected final function encodeJSONP($status,$mixed,$flag='')
	{
    $data = [
      'status' => $status,
      'flag' => $flag
    ];
    if(is_string($mixed))
    {
      $data['msg'] = $mixed;
    }
    else
    {
      $data['data'] = $mixed;
    }
    CFuncHelper::encodeJSONP($data);	}

  /**
   * 输出json
   * @param $status
   * @param $mixed
   * @param string $flag
   */
	protected final function encodeJSON($status, $mixed=[], $flag='')
	{
	  $data = [
	    'status' => $status,
    ];
    if(strlen($flag)>0)
    {
      $data['flag'] = $flag;
    }
    if(is_string($mixed))
    {
      $data['msg'] = $mixed;
    }
    else
    {
      $data['data'] = $mixed;
    }
		CFuncHelper::encodeJSON($data);
	}

	protected function strTrim( $f )
	{
		return array_map( function($v){
			if( is_string($v) )
			{
				return trim($v);
			}
			else
			{
				return $v;
			}
		} , $f );
	}


	/**
	 * 返回处理后的表单提交数据
	 * @param $f
	 * @param $attr
	 * @return array
	 */
	protected function formPost($f,$attr)
	{
		$data = array();
		foreach($attr as $k=>$v)
		{
			if( !$v )
			{
				continue;
			}
			foreach(explode(',' , $v) as $k1=>$v1)
			{
				if( !isset($f[$v1]) )
				{
					continue;
				}
				if($k==='string')
				{
					$data[$v1] = trim(CHtml::encode($f[$v1]));
				}
				elseif( $k === 'int')
				{
					$data[$v1] = intval($f[$v1]);
				}
				elseif( $k === 'float')
				{
					$data[$v1] = floatval($f[$v1]);
				}
				elseif( $k ==='image')
				{
					$data[$v1] = CHtml::encode(CFuncHelper::getUploadVirtulUrl($f[$v1]));
				}
				elseif( $k === 'array')
				{
					$data[$v1] = $f[$v1];
				}
				elseif( $k ==='html')
				{
					$f[$v1] = CFuncHelper::getUploadVirtulUrl($f[$v1]);
					$farr = array(
						"/\s+/is", //过滤多余空白
						"/<(\/?)(script|i?frame|html|body|title|link|meta|\?|\%)([^>]*?)>/is",
						"/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/is",//过滤javascript的on事件
					);
					$tarr = array(
						" ",
						"&lt;$1$2$3&gt;",//如果要直接清除不安全的标签，这里可以留空
						"$1$2",
					);
					$data[$v1] = preg_replace( $farr,$tarr, $f[$v1] );
				}
				elseif( $k === 'no')
				{
					//不处理
					$data[$v1] = $f[$v1];
				}
				else
				{
					//不在规则中的post表单
				}
			}
		}
		return $data;
	}

	/**
	 * 过滤数组，删除不需要的索引
	 * @param $attr
	 * @param $value
	 * @return array
	 */
	public function filterArray($attr,$value)
	{
		if( is_string($attr) )
		{
			$attr = str_replace('t.' , '', $attr);
			$attr = $attr ? explode(',' , $attr) : array();
		}
		$data = array();
		foreach($attr as $v)
		{
			$v = str_replace('`','',$v);
			$data[$v] = isset($value[$v]) ? $value[$v] : null;
		}
		return $data;
	}

  /**
   * 接受参数
   * @param $name
   * @param null $default
   * @return mixed
   */
	public function getPhpInput($name, $default=null)
  {
    $ret = Yii::app()->request->getParam($name, $default);
    if( $ret === $default )
    {
      $ret = CFuncHelper::getPhpInput($name, $default);
    }
    return $ret;
  }

}