<?php
/**
 * Created by 大人.
 * Date: 13-3-28
 */

class CyzmHelper
{
	/**
	 * @var SESSION prefix for supplement. for example "SESSION_VAR_PREFIX_sessPrefix"
	 */
	public $sessPrefix;
	/**
	 * @var int width
	 */
	public $width = 100;
	/**
	 * @var int height
	 */
	public $height = 30;
	/**
	 * @var int padding
	 */
	public $padding = 2;
	/**
	 * @var int letter offset
	 */
	public $offset = 2;
	/**
	 * @var int background color
	 */
	public $backColor = 0xFFFFFF;
	/**
	 * @var int font color
	 */
	public $fontColor = 0x2040A0;
	/**
	 * @var int transparent boolen
	 */
	public $transparent = false;
	/**
	 * @var int code length if code is empty
	 */
	public $length = 4;
	/**
	 * @var int fontfile path
	 */
	public $fontFile;
	/**
	 * @var int if code is null , it will call $this->code();
	 */
	public $code;

	public function __construct($sessPrefix = null, $width=0, $height=0, $transparent = false)
	{
		$this->backColor = '0x' . dechex(mt_rand(200,255)) . dechex(mt_rand(200,255)) . dechex(mt_rand(200,255)) + 0;
		$this->length = mt_rand(4,4);
		$this->width = $width ? $width : mt_rand(100,120);
		$this->height = $height ? $height : mt_rand(30,35);
    $this->transparent = $transparent;
		Yii::app()->session->open();
		$sessPrefix && $this->sessPrefix = $sessPrefix;
	}

	/**
	 * Runs the action.
	 */
	public function run()
	{
		if( !$this->code )
		{
			$this->generateVerifyCode();
		}
		Yii::app()->session[$this->getSessionKey()] = $this->code;
		$this->renderImage( $this->code );
		Yii::app()->end();
	}

	/**
	 * @param $input
	 * @param $caseSensitive
	 * @return bool
	 */
	public function validate($input,$caseSensitive = false)
	{
		$code = Yii::app()->session[$this->getSessionKey()];
		if( !$input || !$code )
		{
			$valid = false;
		}
		else
		{
			$valid = $caseSensitive ? ($input === $code) : !strcasecmp($input,$code);
		}
		unset(Yii::app()->session[$this->getSessionKey()]);
		return $valid;
	}

	/**
	 *
	 */
	private function generateVerifyCode()
	{
		$str = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz";
		$str = str_shuffle($str);
    $code = substr($str, 0, $this->length);
		$this->code = $code;
	}

	/**
	 * Returns the session variable name used to store verification code.
	 * @return string the session variable name
	 */
	private function getSessionKey()
	{
		return __CLASS__ . '_' . $this->sessPrefix;
	}

	/**
	 * Renders the CAPTCHA image based on the code.
	 * @param string $code the verification code
	 * @return string image content
	 */
	private function renderImage($code)
	{
		$image = imagecreatetruecolor($this->width,$this->height);
		$backColor = imagecolorallocate(
			$image,
			(int)($this->backColor % 0x1000000 / 0x10000),
			(int)($this->backColor % 0x10000 / 0x100),
			$this->backColor % 0x100
		);

		imagefilledrectangle($image, 0, 0, $this->width, $this->height, $backColor);
		imagecolordeallocate($image,$backColor);

		if($this->transparent)
		{
			imagecolortransparent($image,$backColor);
		}


		if($this->fontFile === null)
		{
			$this->fontFile = __DIR__ . '/Duality.ttf';
		}

		$length = strlen($code);
		$box = imagettfbbox(30,0,$this->fontFile, $code);
		$w = $box[4] - $box[0] + $this->offset * ($length - 1);
		$h = $box[1] - $box[5];
		$scale = min(($this->width - $this->padding * 2) / $w,($this->height - $this->padding * 2) / $h);
		$x = 10;
		$y = round($this->height * 27 / 40);
		for($i = 0; $i < $length; ++$i)
		{
			$this->fontColor = 	'0x' . dechex(mt_rand(0,200)) . dechex(mt_rand(0,200)) . dechex(mt_rand(0,200)) + 0;

			$foreColor = imagecolorallocate(
				$image,
				(int)($this->fontColor % 0x1000000 / 0x10000),
				(int)($this->fontColor % 0x10000 / 0x100),
				$this->fontColor % 0x100
			);

			$fontSize = (int)(mt_rand(26,32) * $scale * 0.8);
			$angle = mt_rand(-45,45);
			$letter = $code[$i];
			$box = imagettftext($image, $fontSize, $angle, $x, $y, $foreColor, $this->fontFile, $letter);
			$x = $box[2] + $this->offset;
		}

		imagecolordeallocate($image,$foreColor);

		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Transfer-Encoding: binary');
		header("Content-type: image/png");
		imagepng($image);
		imagedestroy($image);
	}

}