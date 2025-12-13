<?php
// +----------------------------------------------------------------------
// | Yzncms [ 御宅男工作室 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2007 http://yzncms.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 御宅男 <530765310@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller\basic;

use app\admin\model\AdminUser as Admin_User;
use app\admin\model\AdminMonitor as Admin_Monitor;

use app\admin\model\AuthGroup as AuthGroupModel;
use app\common\controller\Adminbase;
use util\Tree;

/**
 * 管理员管理
 */
class Monitor extends Adminbase
{
    protected $searchFields     = 'id,name,mobile';
    protected $childrenGroupIds = [];
    protected $childrenAdminIds = [];
    protected function initialize()
    {
        parent::initialize();
        $this->modelClass = new Admin_Monitor;

/*        $this->childrenAdminIds = $this->auth->getChildrenAdminIds(true);
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds(true);*/





    }

    /**
     * 管理员管理列表
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($page, $limit, $where, $sort, $order) = $this->buildTableParames();

/*            $childrenGroupIds = $this->childrenGroupIds;
            $groupName        = AuthGroupModel::where('id', 'in', $childrenGroupIds)
                ->column('id,title');*/

            $list = $this->modelClass
                ->where($where)
               /* ->where('id', 'in', $this->childrenAdminIds)*/
                ->field("*", true)
                ->order($sort, $order)
                ->paginate($limit);

/*            foreach ($list as $k => &$v) {
                $v['groups']      = $groupName[$v['roleid']] ?? '未知';
            }
            unset($v);*/
            $result = ["code" => 0, 'count' => $list->total(), "data" => $list->items()];
            return json($result);
        }
        return $this->fetch();
    }

    /**
     * 添加管理员
     */
    public function add()
    {
        $monitor_model =   new Admin_Monitor();
        if ($this->request->isPost()) {
            $this->token();
            $params             = $this->request->post('');
/*            $result             = $this->validate($params, 'AdminUser.insert');
            $passwordinfo       = encrypt_password($params['password']); //对密码进行处理
            $params['password'] = $passwordinfo['password'];
            $params['encrypt']  = $passwordinfo['encrypt'];*/
/*            if (true !== $result) {
                return $this->error($result);
            }
            if (!in_array($params['roleid'], $this->childrenGroupIds)) {
                $this->error('没有权限操作！');
            }*/
            try {
                $monitor_model->save($params);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success("添加成功！", url('index'));
        }
        return $this->fetch();
    }

    /**
     * 管理员编辑
     */
    public function edit()
    {
        $id  = $this->request->param('id/d', 0);
        $row = $this->modelClass->get($id);
        if (!$row) {
            $this->error('记录未找到');
        }
        if (!in_array($row->id, $this->childrenAdminIds)) {
            $this->error('没有权限操作！');
        }
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post('');
            $result = $this->validate($params, 'AdminUser.update');
            if (true !== $result) {
                return $this->error($result);
            }
            if (!in_array($params['roleid'], $this->childrenGroupIds)) {
                $this->error('没有权限操作！');
            }
            //密码为空，表示不修改密码
            if (isset($params['password']) && $params['password']) {
                $passwordinfo       = encrypt_password($params['password']); //对密码进行处理
                $params['encrypt']  = $passwordinfo['encrypt'];
                $params['password'] = $passwordinfo['password'];

            } else {
                unset($params['password'], $params['encrypt']);
            }
            try {
                $row->allowField(true)->save($params);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success("修改成功！");
        }
        $this->assign("data", $row);
        return $this->fetch();
    }

    /**
     * 管理员删除
     */
    public function del()
    {


        if (false === $this->request->isPost()) {
            $this->error('未知参数');
        }
        $id = $this->request->param('id/d');
        if (empty($id)) {
            $this->error('请指定需要删除的企业ID！');
        }


                try {
                    $this->modelClass->destroy($id);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
                $this->success("删除成功！");


    }

    //批量更新.
    public function multi()
    {
        // 管理员禁止批量操作
        $this->error();
    }

}
