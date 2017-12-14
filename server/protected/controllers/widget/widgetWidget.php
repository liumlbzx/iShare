<?php
/**
 * Description
 * Date: 2012-03-14 15:14
 */

Class widgetWidget extends CWidget
{
	public $page = null;
	public function run()
	{
		$this->render( 'application.views.widget.' . $this->page );
	}

}