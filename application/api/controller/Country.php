<?php
// +----------------------------------------------------------------------
// | Yzncms [ 御宅男工作室 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://yzncms.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | fastadmin: https://www.fastadmin.net/
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 邮箱验证码接口
// +----------------------------------------------------------------------
namespace app\api\controller;

use app\common\controller\Api;

use app\member\model\Member;
use think\Db;
use think\facade\Hook;
use think\facade\Validate;




/**
 * @title 邮箱验证码接口
 * @controller api\controller\Ems
 * @group base
 */



class Country extends Api
{
    public function getCountryList(){//根据一级分类获取二级分类


        //一级分类
        /*$country_list = DB::name("sup_country")->order('english_name asc')->select();*/


        $country_list = DB::name("country")->field("english_name,telcode")->order('english_name asc')->select();

        $option_str = '';
        foreach ($country_list as $k =>$v){

            $option_str = $option_str.'<option value="'.$v['english_name'].'">'.'+'.$v['telcode'].': '.$v['english_name'].'</option>';

        }

        var_dump($option_str);



       /* return json_encode($country_list,JSON_UNESCAPED_UNICODE);*/

    }













}
