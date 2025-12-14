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

use app\admin\model\AdminCompany as Admin_Company;
use app\common\controller\Adminbase;
use think\Db;

/**
 * 产品管理
 */
class Company extends Adminbase
{
    protected $searchFields = 'id,name,shengchanpihao,wuliuma';

    /**
     * 生成唯一的随机密钥
     * @return string 20位随机字符串
     */
    protected function generateUniqueKey()
    {
        // 包含数字和大小写字母的字符集
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $length = strlen($chars) - 1;

        // 生成20位随机字符串
        $key = '';
        for ($i = 0; $i < 20; $i++) {
            $key .= $chars[mt_rand(0, $length)];
        }

        // 检查是否已存在
        $exists = Db::name('company')->where('keys', $key)->find();
        if ($exists) {
            // 如果已存在，递归重新生成
            return $this->generateUniqueKey();
        }

        return $key;
    }

    protected function initialize()
    {
        parent::initialize();
        $this->modelClass = new Admin_Company;
    }

    /**
     * 产品列表
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($page, $limit, $where, $sort, $order) = $this->buildTableParames();

            // 如果没有指定排序，则默认按创建时间降序
            if (!$sort) {
                $sort = 'create_time';
                $order = 'desc';
            }

            $list = $this->modelClass
                ->where($where)
                ->field("*", true)
                ->order($sort, $order)
                ->paginate($limit);

            $result = ["code" => 0, 'count' => $list->total(), "data" => $list->items()];
            return json($result);
        }
        
        // 获取入口域名配置，用于生成复制链接
        $rukouUrl = Db::name('config')->where('name', 'rukou_url')->value('value');
        $linkBaseUrl = '';
        if (!empty($rukouUrl)) {
            // 确保有协议
            if (!preg_match('/^https?:\/\//', $rukouUrl)) {
                $linkBaseUrl = 'https://' . $rukouUrl;
            } else {
                $linkBaseUrl = $rukouUrl;
            }
        } else {
            // 如果没有配置，使用当前域名
            $linkBaseUrl = $this->request->domain();
        }
        
        $this->assign('linkBaseUrl', $linkBaseUrl);
        return $this->fetch();
    }

    /**
     * 导入excel表格
     */
    public function import()
    {
        $company_model = new Admin_Company();

        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post('');

            $path = $params['excel_file'];

            $baseurl = $_SERVER['DOCUMENT_ROOT'];//网站根目录
            $path = $baseurl.$path;

            $rs = $this->do_import_company($path);
            $this->success("导入成功！".$rs, url('index'));
        }
        return $this->fetch();
    }

    /**
     * 批量新增产品
     */
    public function do_import_company($path)
    {
        //实例化PHPExcel类
        $PHPExcel = new \PHPExcel();
        //默认用excel2007读取excel，若格式不对，则用之前的版本进行读取
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($path)) {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($path)) {
                /* $this->error($path);*/
            }
        }

        //读取Excel文件
        $PHPExcel = $PHPReader->load($path);
        //读取excel文件中的第一个工作表
        $sheet = $PHPExcel->getSheet(0)->toArray();

        //循环输出数据
        foreach($sheet as $k => $v){
            if ($k > 0 && $k < 500){
                $data = [
                    'name' => $v[0],
                    'shengchanpihao' => $v[1],
                    'produce_time' => $v[2],
                    'jiujingdu' => $v[3],
                    'rongliang' => $v[4],
                    'wuliuma' => $v[5],
                    'wuliuma2' => $v[6],
                    'status' => 1,
                ];

                // 限制物流码长度为12个字符
                if (isset($data['wuliuma']) && strlen($data['wuliuma']) > 12) {
                    $data['wuliuma'] = substr($data['wuliuma'], 0, 12);
                }

                // 限制物流码2长度为12个字符
                if (isset($data['wuliuma2']) && strlen($data['wuliuma2']) > 12) {
                    $data['wuliuma2'] = substr($data['wuliuma2'], 0, 12);
                }

                if ($data['name']){
                    $rs = DB::name('company')->insert($data);
                }else{
                    break;
                }
            }
        }
    }

    /**
     * 添加产品
     */
    public function add()
    {
        $company_model = new Admin_Company();

        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post('');

            // 限制物流码长度为12个字符
            if (isset($params['wuliuma']) && strlen($params['wuliuma']) > 12) {
                $params['wuliuma'] = substr($params['wuliuma'], 0, 12);
            }

            // 限制物流码2长度为12个字符
            if (isset($params['wuliuma2']) && strlen($params['wuliuma2']) > 12) {
                $params['wuliuma2'] = substr($params['wuliuma2'], 0, 12);
            }

            // 添加随机密钥
            $params['keys'] = $this->generateUniqueKey();

            try {
                $company_model->save($params);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success("添加成功！", url('index'));
        }
        return $this->fetch();
    }

    /**
     * 产品编辑
     */
    public function edit()
    {
        $id = $this->request->param('id/d', 0);
        $row = $this->modelClass->get($id);
        if (!$row) {
            $this->error('记录未找到');
        }

        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post('');

            // 限制物流码长度为12个字符
            if (isset($params['wuliuma']) && strlen($params['wuliuma']) > 12) {
                $params['wuliuma'] = substr($params['wuliuma'], 0, 12);
            }

            // 限制物流码2长度为12个字符
            if (isset($params['wuliuma2']) && strlen($params['wuliuma2']) > 12) {
                $params['wuliuma2'] = substr($params['wuliuma2'], 0, 12);
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
     * 产品删除
     */
    public function del()
    {
        if (false === $this->request->isPost()) {
            $this->error('未知参数');
        }
        $id = $this->request->param('id/a', '');
        if (empty($id)) {
            $this->error('请指定需要删除的产品ID！');
        }

        try {
            // 支持批量删除
            if(is_array($id)) {
                $this->modelClass->destroy($id);
            } else {
                $this->modelClass->destroy($id);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success("删除成功！");
    }

    /**
     * 批量更新
     */
    public function multi()
    {
        if (false === $this->request->isPost()) {
            $this->error('未知参数');
        }
        $ids = $this->request->param('id/a', null);
        if (empty($ids)) {
            $this->error('参数错误！');
        }
        if (!is_array($ids)) {
            $ids = [0 => $ids];
        }
        $value = $this->request->param('value', 0);
        if ($this->request->has('param')) {
            $param = $this->request->param('param/s');

            if ($param) {
                $pk       = $this->modelClass->getPk();
                $adminIds = $this->getDataLimitAdminIds();
                $where    = [];
                if (is_array($adminIds)) {
                    $where[] = [$this->dataLimitField, 'in', $adminIds];
                }
                $where[] = [$pk, 'in', $ids];
                $count   = 0;
                Db::startTrans();
                try {
                    $list = $this->modelClass->where($where)->select();
                    foreach ($list as $item) {
                        $item->{$param} = $value;
                        // 如果是物流码字段，限制长度为12
                        if (($param === 'wuliuma' || $param === 'wuliuma2') && strlen($value) > 12) {
                            $item->{$param} = substr($value, 0, 12);
                        } else {
                            $item->{$param} = $value;
                        }
                        $count += $item->save();
                    }
                    Db::commit();
                    $this->success('更新成功');
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }


            }
        }

    }

    /**
     * 插入空记录
     */
    public function addEmpty()
    {
        if ($this->request->isPost()) {
            try {
                // 创建空记录数据
                $data = [
                    //'name' => '新产品' . date('YmdHis'),
                    'name' => '',
                    'shengchanpihao' => '',
                    'produce_time' => date('Y-m-d'),
                    'jiujingdu' => '',
                    'rongliang' => '',
                    'wuliuma' => '',
                    'wuliuma2' => '',
                    'status' => 1,
                    'keys' => $this->generateUniqueKey(),
                    'create_time' => time(), // 添加创建时间字段，用于排序
                ];

                // 限制物流码长度为12个字符
                if (isset($data['wuliuma']) && strlen($data['wuliuma']) > 12) {
                    $data['wuliuma'] = substr($data['wuliuma'], 0, 12);
                }

                // 限制物流码2长度为12个字符
                if (isset($data['wuliuma2']) && strlen($data['wuliuma2']) > 12) {
                    $data['wuliuma2'] = substr($data['wuliuma2'], 0, 12);
                }

                // 插入数据
                $result = $this->modelClass->save($data);

                if ($result) {
                    $this->success('添加成功');
                } else {
                    $this->error('添加失败');
                }
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        $this->error('非法请求');
    }

    /**
     * 复制产品
     */
    public function copy()
    {
        $id = $this->request->param('id/d', 0);
        $row = $this->modelClass->get($id);
        if (!$row) {
            $this->error('记录未找在找到');
        }

        try {
            // 获取原记录数据
            $data = $row->toArray();

            // 移除ID和时间字段，准备创建新记录
            unset($data['id']);
            if (isset($data['create_time'])) {
                unset($data['create_time']);
            }
            if (isset($data['update_time'])) {
                unset($data['update_time']);
            }

            // 修改名称，添加复制标记
            //$data['name'] = $data['name'];

            // 生成新的随机密钥
            $data['keys'] = $this->generateUniqueKey();

            // 设置创建时间
            $data['create_time'] = time();

            // 限制物流码长度为12个字符
            if (isset($data['wuliuma']) && strlen($data['wuliuma']) > 12) {
                $data['wuliuma'] = substr($data['wuliuma'], 0, 12);
            }

            // 限制物流码2长度为12个字符
            if (isset($data['wuliuma2']) && strlen($data['wuliuma2']) > 12) {
                $data['wuliuma2'] = substr($data['wuliuma2'], 0, 12);
            }

            // 创建新记录
            $result = $this->modelClass->save($data);


        } catch (\Exception $e) {

            $this->error($e->getMessage());
        }
        if ($result) {
            $this->success('复制成功');
        } else {
            $this->error('复制失败');
        }
    }

}
