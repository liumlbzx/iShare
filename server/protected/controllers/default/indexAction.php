<?php

class indexAction extends GAction
{
	public function run()
	{
		set_time_limit(0);



    exit;

    var_dump(chr(90));exit;

    //{"birthday":"565891200","household_type":"2","sex":"1","team":"1","mary":"1","edu":"2","nation":"\u6c49\u65cf","address":"\u6d66\u4e1c\u65b0\u533a\u9f99\u4e1c\u5927\u90533#18#208","household":"\u664b\u57ce"}
    Yii::app()->db->schema->refresh();
    var_dump(Yii::app()->db->schema->getTables());
    $this->encodeJSON(200);

    exit('dsadfff');
		
	}

}