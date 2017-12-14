<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class GController extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout;
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu = array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs = array();

	public $pageConfig = array(
		'header' => '',
		'firstPageLabel' => '&lt;&lt;',
		'lastPageLabel' => '&gt;&gt;',
		'prevPageLabel' => '&lt;',
		'nextPageLabel' => '&gt;',
		'maxButtonCount' => 10,
		'cssFile' => false,
		'selectedPageCssClass' => 'active',
		'hiddenPageCssClass' => 'disabled',
		'htmlOptions' => array('class' => 'pagination')
	);

	//request_time
	public $time;

	/**
	 * @var CHttpRequest
	 */
	public $request;

	public function init()
	{
		$this->time = time();
		$this->request = Yii::app()->request;
		Yii::app()->user->loginUrl = Yii::app()->createUrl('www/user/login');

		$arr = array(
      //上传文件目录
			'uploadDomain1' => str_replace('/server','',Yii::app()->getBaseUrl(true)) . '/upload/',
			'uploadPath' => str_replace('/server', '', Yii::getPathOfAlias('webroot')) . '/upload/',
			//开启数据缓存
			'cache' => array(
				'enable' => true,
				'expire' => array(
					'db' => 86400, //数据库默认缓存
				),
			),
		);

		if(preg_match('/^(192\.168)|(127\.0)/', Yii::app()->request->serverName))
		{
			//本地开发模式的配置
		}

		Yii::app()->params->mergeWith($arr);

		Yii::app()->params['title'] = Yii::app()->params['website']['webName'];

	}

}
