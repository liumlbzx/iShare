<?php

class CExportXls
{

  public $header = [
    'aaa.bbb' => '姓名',
    'aaa.ccc' => '性别'
  ];

  public $datas = [
    [
      'aaa' => [
        'bbb' => '张三',
        'ccc' => 2
      ]
    ]
  ];

  public $keyToValue = [
    'aaa.ccc' => [
      1 => '男',
      2 => '女'
    ]
  ];

  public $fileName = '下载.xls';

  public $width = [];

  public function export($header = [], $datas = [], $keyToValue = [], $fileName = '下载', $width=[])
  {
    $this->header = $header;
    $this->datas = $datas;
    $this->keyToValue = $keyToValue;
    $this->fileName = $fileName;
    $this->width = $width;
    $this->_export();
  }

  /**
   * 导出社保
   * @param array $header
   * @param array $datas
   * @param array $keyToValue
   * @param string $fileName
   * @param string $tpl
   * @param integer $startRow
   */
  public function exportShebao($header = [], $datas = [], $keyToValue = [], $fileName = '下载', $tpl='', $startRow=1)
  {
    require_once __DIR__ . '/phpexcel/PHPExcel.php';

    $objExcel = PHPExcel_IOFactory::createReader("Excel5")->load($tpl);

    $objExcel->setActiveSheetIndex(0);

    $objActSheet = $objExcel->getActiveSheet();

    //设置当前活动sheet的名称
    $objActSheet->setTitle($fileName);

    $i = $startRow;
    $objActSheet->insertNewRowBefore($i+1, count($datas)+1);

    foreach ($datas as $row)
    {
      $i++;
      $j = 1;
      foreach ($header as $key => $value)
      {
        $objActSheet->setCellValueExplicit(
          $this->_getCol($j++) . $i,
          $this->_getValue($row, $key, $keyToValue),
          PHPExcel_Cell_DataType::TYPE_STRING
        );
      }
    }

    //输出内容到浏览器
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header('Content-Disposition:inline;filename="' . $fileName . '.xls"');
    header("Content-Transfer-Encoding: binary");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    PHPExcel_IOFactory::createWriter($objExcel, 'Excel5')->save('php://output');

  }

  private function _export()
  {
    require_once __DIR__ . '/phpexcel/PHPExcel.php';
    spl_autoload_unregister(array(
      'YiiBase',
      'autoload'
    ));

    $header = $this->header;
    $datas = $this->datas;
    $keyToValue = $this->keyToValue;

    $objExcel = new PHPExcel();
    $objWriter = new PHPExcel_Writer_Excel5($objExcel);
    $objExcel->setActiveSheetIndex(0);
    $objActSheet = $objExcel->getActiveSheet();
    //设置当前活动sheet的名称
    $objActSheet->setTitle($this->fileName);
    //*************************************
    //设置单元格内容
    $i = 1;
    foreach ($header as $key => $value)
    {
      $col = $this->_getCol($i);
      $objActSheet->setCellValueExplicit($col . '1', $value, PHPExcel_Cell_DataType::TYPE_STRING);
      $width = isset($this->width[$key]) ? $this->width[$key] : (mb_strlen($value, 'utf-8') * 2 + 2);
      $objActSheet->getColumnDimension($col)->setWidth($width);
      $i++;
    }

    $i = 1;
    foreach ($datas as $row)
    {
      $i++;
      $j = 1;
      foreach ($header as $key => $value)
      {
        $objActSheet->setCellValueExplicit($this->_getCol($j++) . $i, $this->_getValue($row, $key, $keyToValue), PHPExcel_Cell_DataType::TYPE_STRING);
      }
    }

    //输出内容到浏览器
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header('Content-Disposition:inline;filename="' . $this->fileName . '.xls"');
    header("Content-Transfer-Encoding: binary");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    $objWriter->save('php://output');

    spl_autoload_register(array(
      'YiiBase',
      'autoload'
    ));
  }

  private $_cols = [0=>'Z',1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z'];

  private function _getCol($num)
  {//递归方式实现根据列数返回列的字母标识
    if($num==0)
    {
      return '';
    }
    return $this->_getCol((int)(($num-1)/26)).$this->_cols[$num%26];
  }

  private function _getValue($row, $key, $keyToValue=[])
  {
    $keys = explode('.', $key);

    $value = $row;
    foreach ($keys as $k)
    {
      if( is_array($value) )
      {
        if( isset($value[$k]) )
        {
          $value = $value[$k];
        }
        else
        {
          $value = null;
          break;
        }
      }
    }

    if( is_array($keyToValue) && isset($keyToValue[$key]) && isset($keyToValue[$key][$value]) )
    {
      $value = $keyToValue[$key][$value];
    }

    return $value;

  }

}