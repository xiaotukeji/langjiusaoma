<?php
// +----------------------------------------------------------------------
// | Yzncms [ 御宅男工作室 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://yzncms.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 御宅男 <530765310@qq.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 余额变动管理
// +----------------------------------------------------------------------
namespace app\admin\controller\member;

use app\admin\model\member\MemberAmountLog;
use app\common\controller\Adminbase;
use app\member\model\Member as MemberModel;

class Amountlog extends Adminbase
{
    protected $modelValidate = true;
    protected $modelClass    = null;

    protected function initialize()
    {
        parent::initialize();
        $this->modelClass = new MemberAmountLog;
    }

    public function index()
    {
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            [$page, $limit, $where, $sort, $order] = $this->buildTableParames();
            $list                                  = $this->modelClass
                ->with('member')
                ->where($where)
                ->order($sort, $order)
                ->page($page, $limit)
                ->select();
            $total = $this->modelClass
                ->with('member')
                ->where($where)
                ->count();
            $result = ["code" => 0, "count" => $total, "data" => $list];
            return json($result);
        }
        return $this->fetch();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $row     = $this->request->post("row/a");
            $user_id = isset($row['user_id']) ? $row['user_id'] : 0;
            $amount  = isset($row['amount']) ? $row['amount'] : 0;
            $memo    = isset($row['memo']) ? $row['memo'] : '';
            if (!$user_id || !$amount) {
                $this->error("金额和会员ID不能为空");
            }
            MemberModel::amount($amount, $user_id, $memo);
            $this->success("添加成功");
        }
        return parent::add();
    }

}
