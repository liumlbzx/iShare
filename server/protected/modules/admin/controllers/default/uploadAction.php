<?php

class uploadAction extends Action
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

  private function _post()
  {
    $type = $this->request->getParam('type');

    $allowExt = [];
    $allowSize = 0;
    $fileName = '';
    $uploadPath = Yii::app()->params['uploadPath'];
    $uploadDomain1 = Yii::app()->params['uploadDomain1'];

    //商品缩略图
    if( $type === 'goods_thumb' )
    {
      $allowExt = ['jpg', 'jpeg'];
      $allowSize = 500 * 1024;
      $fileName = 'tmp/' . md5(time().mt_rand(10000,99999)) . '.%s';
    }

    if( !$allowSize )
    {
      $this->encodeJSON(500, '禁止上传');
    }

    $file = CUploadedFile::getInstanceByName('file');
    if(!$file)
    {
      $this->encodeJSON(500, '上传失败');
    }
    if($file->getSize() > $allowSize)
    {
      if( $allowSize < 1024)
      {
        $maxSize = $allowSize . ' Byte';
      }
      elseif ($allowSize < 1024 * 1024)
      {
        $maxSize = intval($allowSize/1024) . ' K';
      }
      elseif ($allowSize < 1024 * 1024 * 1024)
      {
        $maxSize = intval($allowSize/1024/1024) . ' M';
      }
      else
      {
        $maxSize = intval($allowSize/1024/1024/1024) . ' G';
      }
      $this->encodeJSON(500, '文件不能超过 '.$maxSize);
    }

    $ext = strtolower($file->getExtensionName());
    if( !in_array($ext, $allowExt) )
    {
      $this->encodeJSON(500, '只允许上传 '. implode('、', $allowExt) .' 类型文件');
    }

    $fileName = sprintf($fileName, $ext);
    require_once(Yii::getPathOfAlias('application.3rd').'/CDirHelper.php');
    $fileDir = dirname($uploadPath . $fileName);
    CDirHelper::mkdirs($fileDir, 0777);
    $file->saveAs($uploadPath . $fileName);

    //删除一天之前的文件
    CFuncHelper::clearTmp();

    $this->encodeJSON(200, [
      'file' => $uploadDomain1 . $fileName . '?_='.time()
    ]);

  }


}