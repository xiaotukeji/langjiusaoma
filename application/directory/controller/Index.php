<?php
namespace app\directory\controller;
use app\admin\model\AdminCompany as Admin_Company;
use app\member\controller\MemberBase;
use think\facade\Cookie;
use think\facade\Hook;

class Index extends MemberBase
{

    protected $noNeedLogin = ['index', 'register', 'logout', 'forget'];

    //初始化
    protected function initialize()
    {
        parent::initialize();
        $auth = $this->auth;

        //监听注册登录退出的事件
        Hook::add('user_login_successed', function ($user) use ($auth) {
            $expire = $this->request->post('keeplogin') ? 30 * 86400 : 0;
            Cookie::set('uid', $user->id, $expire);
            Cookie::set('token', $auth->getToken(), $expire);
        });
        Hook::add('user_register_successed', function ($user) use ($auth) {
            Cookie::set('uid', $user->id);
            Cookie::set('token', $auth->getToken());
        });
        Hook::add('user_delete_successed', function ($user) use ($auth) {
            Cookie::delete('uid');
            Cookie::delete('token');
        });
        Hook::add('user_logout_successed', function ($user) use ($auth) {
            Cookie::delete('uid');
            Cookie::delete('token');
        });
    }



    public function index()
    {

        $keywords = input('keywords','');
        $country = input('country','');
        $city = input('city','');
        /*$pages = input('pages',1);*/

        $where = '';

        if ($keywords){
            if($country){
                $where = $where."(address like '%$keywords%' or name like '%$keywords%' or CompanyWeb like '%$keywords%') ";
                //$where = $where." and address like '%$city%' and Country = '$country'" ;
                $where = $where."  and Country = '$country'" ;

            }else{
                $where = $where."(address like '%$keywords%' or name like '%$keywords%' or CompanyWeb like '%$keywords%') ";
                /*$where = $where." and address like '%$city%'" ;*/

            }

        }else{
            if ($country){
                $where = $where."address like '%$city%' and Country = '$country'" ;
            }else{
                $where = $where."address like '%$city%'" ;
            }

        }
        $where = $where.'and status=1';


        $list  = Admin_Company::where($where)
            /*->page($pages, 2)*/

            ->order('id DESC')->paginate(10,false,['query'=>request()->param() ]);

        foreach($list as $k => $v){

            $list[$k]['PrimaryService'] = explode(',',$v['PrimaryService']);

        }



        $count = Admin_Company::where('')
                    ->count();


        $this->assign('list',$list);
        $this->assign('keywords',$keywords);

        $this->assign([

            'keyword'     => $keywords,
            'country'     => $country,
            'list'        => $list,
/*            'pages'       => $pages,
            'count'      => $count*/
        ]);



        return $this->fetch('/index');


    }

    public function detail(){

        $company_id = input('company_id',0);

        if ($company_id > 0){
            $company_info  = Admin_Company::where('id',$company_id)->find();

            $PrimaryService = explode(',',$company_info['PrimaryService']);
            $this->assign('PrimaryService',$PrimaryService);
            $this->assign('company_info',$company_info);
        }else{

            $this->error('Company is not Exist');
        }



        return $this->fetch('/detail');

    }



}
