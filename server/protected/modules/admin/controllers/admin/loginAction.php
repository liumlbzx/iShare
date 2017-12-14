<?php

class loginAction extends Action
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
    $f = [];
    foreach (['username','password'] as $v)
    {
      $f[$v] = $this->getPhpInput($v);
    }

    $data = new Admin();
    $ret = $data->login($f['username'], $f['password']);

    if( $ret )
    {
      $this->encodeJSON(200, $ret);
    }
    else
    {
      $errors = $data->errors;
      $this->encodeJSON(500, array_values($errors)[0]);
    }
  }

}