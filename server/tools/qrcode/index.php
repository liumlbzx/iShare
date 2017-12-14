<?php
/*
 * 生成链接二维码 by haibao
 * */
include "./phpqrcode.php";

$content = isset($_GET["w"]) ? $_GET["w"] : '';
$errorLevel = isset($_GET["e"]) ? $_GET["e"] : 'L'; 
$PointSize  = isset($_GET["p"]) ? $_GET["p"] : 10;
$margin = isset($_GET["m"]) ? $_GET["m"] : 0;
//preg_match('/http:\/\/([\w\W]*?)\//si', $content, $matches);

QRcode::png($content, false, $errorLevel, $PointSize, $margin);
