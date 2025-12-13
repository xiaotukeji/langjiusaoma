<?php
namespace app\about\controller;

use app\member\controller\MemberBase;

class Index extends MemberBase
{
    protected $noNeedLogin = ['index', 'register', 'logout', 'forget'];
    public function index()
    {
        return $this->fetch('/index');


    }




}
