<?php
namespace app\index\controller;

use app\member\controller\MemberBase;
use think\Db;

class Index extends MemberBase
{
    protected $noNeedLogin = ['index', 'register', 'logout', 'forget'];
    
    public function index()
    {
        // 如果 URL 中没有 nocache 参数，自动添加并重定向，避免浏览器历史记录规范化 URL 大小写
        // 从 REQUEST_URI 提取原始参数，使用时间戳+key 生成唯一的 nocache 参数，完全保留大小写
        if (!isset($_GET['nocache'])) {
            $pathinfo = $this->request->pathinfo();
            $domain = $this->request->domain();
            
            // 先从 REQUEST_URI 提取原始 key 值，保持大小写
            $originalKey = '';
            if (isset($_SERVER['REQUEST_URI'])) {
                $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
                if ($queryString) {
                    parse_str($queryString, $params);
                    if (isset($params['key'])) {
                        $originalKey = $params['key'];
                    }
                }
            }
            
            // 如果还是没有获取到 key，尝试从 QUERY_STRING 获取
            if (empty($originalKey) && isset($_SERVER['QUERY_STRING'])) {
                parse_str($_SERVER['QUERY_STRING'], $params);
                if (isset($params['key'])) {
                    $originalKey = $params['key'];
                }
            }
            
            // 如果还是没有，从 $_GET 获取（可能已被转换）
            if (empty($originalKey) && isset($_GET['key'])) {
                $originalKey = $_GET['key'];
            }
            
            // 先查询产品ID（如果key存在）
            $productId = '';
            if (!empty($originalKey)) {
                $product = Db::name('company')->whereRaw('BINARY `keys` = ?', [$originalKey])->where('status', 1)->field('id')->find();
                if ($product && isset($product['id'])) {
                    $productId = $product['id'];
                }
            }
            
            // 生成唯一的 nocache 参数：时间戳+产品ID（纯数字）
            $nocacheValue = time() . ($productId ? $productId : rand(1000, 9999));
            
            // 从 REQUEST_URI 提取原始查询字符串，完全保留原始大小写
            $originalQuery = '';
            if (isset($_SERVER['REQUEST_URI'])) {
                $parsed = parse_url($_SERVER['REQUEST_URI']);
                if (isset($parsed['query']) && !empty($parsed['query'])) {
                    // 直接保留原始查询字符串，手动添加唯一的 nocache 参数
                    $originalQuery = $parsed['query'] . '&nocache=' . $nocacheValue;
                } else {
                    $originalQuery = 'key=' . urlencode($originalKey) . '&nocache=' . $nocacheValue;
                }
            } else {
                // 如果无法获取 REQUEST_URI，使用 QUERY_STRING
                if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
                    $originalQuery = $_SERVER['QUERY_STRING'] . '&nocache=' . $nocacheValue;
                } else {
                    $originalQuery = 'key=' . urlencode($originalKey) . '&nocache=' . $nocacheValue;
                }
            }
            
            $redirectUrl = $domain . $pathinfo . '?' . $originalQuery;
            $this->redirect($redirectUrl, 302);
            return;
        }
        
        // 获取产品ID参数，直接从 REQUEST_URI 解析，避免被浏览器或服务器转换
        $key = '';
        
        // 方法1：从 REQUEST_URI 直接解析，保持原始大小写
        if (isset($_SERVER['REQUEST_URI'])) {
            $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
            if ($queryString) {
                parse_str($queryString, $params);
                if (isset($params['key'])) {
                    $key = $params['key'];
                }
            }
        }
        
        // 方法2：如果方法1没获取到，从 QUERY_STRING 解析
        if (empty($key) && isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $params);
            if (isset($params['key'])) {
                $key = $params['key'];
            }
        }
        
        // 方法3：如果还是没获取到，从 $_GET 获取（可能已被转换）
        if (empty($key) && isset($_GET['key'])) {
            $key = $_GET['key'];
        }
        
        // 调试：如果需要调试，访问时加上 &debug_key=1 参数，查看实际接收到的值
        // 注意：生产环境请删除或注释掉这段调试代码
        if (isset($_GET['debug_key'])) {
            die(json_encode([
                'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? '',
                'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? '',
                '_GET' => $_GET,
                'parsed_key' => $key,
                'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] ?? ''
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        
        // 根据ID查询产品信息，使用 whereRaw 确保区分大小写
        $product = Db::name('company')->whereRaw('BINARY `keys` = ?', [$key])->where('status', 1)->find();
        
        // 设置默认的查询信息
        $query_count = 1;
        $first_query_time = date('Y-m-d H:i:s');
        $last_query_time = date('Y-m-d H:i:s');
        $last_query_ip = $this->request->ip();
        
        // 获取IP地址对应的地理位置
        $location = $this->getlocation($last_query_ip);
        $location_info = '';
        
        // 判断省市区是否为空
        if ($location['province'] != '' || $location['city'] != '' || $location['district'] != '') {
            // 省市区不为空，显示省市区信息
            $location_info = $location['province'] . $location['city'] . $location['district'];
        } else {
            // 省市区为空，显示IP地址
            $location_info = $last_query_ip;
        }
        
        // 处理物流码显示逻辑
        $show_wuliu = true;
        if (isset($product['showWuliu']) && $product['showWuliu'] == 0) {
            $show_wuliu = false;
        }
        
        // 处理封面图显示
        $has_cover = false;
        if (isset($product['cover']) && !empty($product['cover'])) {
            $has_cover = true;
        }
        
        // 将产品信息传给模板
        $this->assign('product', $product);
        
        // 将查询记录信息传给模板
        $this->assign('query_count', $query_count);
        $this->assign('first_query_time', $first_query_time);
        $this->assign('last_query_time', $last_query_time);
        $this->assign('last_query_ip', $last_query_ip);
        $this->assign('location_info', $location_info);
        $this->assign('show_wuliu', $show_wuliu);
        $this->assign('has_cover', $has_cover);
        
        return $this->fetch('/index');
    }

    public function getlocation($ip)
    {
        $apiKey = '5ZQBZ-WQPY5-VWVIX-IXHMX-WPGI5-LAFIR';
        $url = "https://apis.map.qq.com/ws/location/v1/ip?ip={$ip}&key={$apiKey}";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if ($data['status'] == 0) {
            $info = $data['result']['ad_info'];
            $province = $info['province'];
            $city = $info['city'];
            $district = $info['district'];

            return [
                'province' => $province,
                'city' => $city,
                'district' => $district
            ];
        } else {
            return [
                'province' => '',
                'city' => '',
                'district' => '',
            ];
        }
    }




}
