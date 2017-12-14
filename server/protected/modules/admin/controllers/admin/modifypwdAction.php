<?php

class modifypwdAction extends Action
{
  public function run()
  {
    $do = $this->request->getParam('do');

    if($do==='post')
    {
      $f = [];

      foreach (['oldpassword','password', 'password_reply'] as $v)
      {
        $f[$v] = $this->getPhpInput($v);
      }

      if(!$f['password'])
      {
        $this->encodeJSON(500, '新密码不能为空');
      }

      if($f['password'] != $f['password_reply'])
      {
        $this->encodeJSON(500, '两次输入的新密码不一样');
      }

      $row = Syscfg::getValue('adminAccount', true);

      if(!$row)
      {
        $this->encodeJSON(500, '修改失败');
      }

      if($row['password'] != $f['oldpassword'])
      {
        $this->encodeJSON(500, '原密码不正确');
      }

      $row['password'] = $f['password'];
      Syscfg::setValue('adminAccount', $row, true);

      $this->encodeJSON(200);

    }

  }

}