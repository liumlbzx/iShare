<?php

class logoutAction extends Action
{
  public function run()
  {
    $auth = new Admin();
    $auth->logout();
    $this->encodeJSON(200);

  }

}