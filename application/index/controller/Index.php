<?php
namespace app\index\controller;

use app\member\controller\MemberBase;
use think\Db;

class Index extends MemberBase
{
    protected $noNeedLogin = ['index', 'register', 'logout', 'forget'];
    
    public function index()
    {
        // 域名跳转判断：如果来源域名是rukou_url，则跳转到luodi_url
        $currentHost = $this->request->host(true); // 获取当前访问的域名（不包含协议和端口）
        // 查询入口域名配置
        $rukouUrl = Db::name('config')->where('name', 'rukou_url')->value('value');
        if (!empty($rukouUrl)) {
            // 去除协议，只比较域名部分
            $rukouHost = parse_url($rukouUrl, PHP_URL_HOST);
            if (empty($rukouHost)) {
                // 如果没有协议，直接使用原值
                $rukouHost = $rukouUrl;
            }
            // 比较当前域名是否匹配入口域名
            if ($currentHost === $rukouHost) {
                // 查询落地域名配置
                $luodiUrl = Db::name('config')->where('name', 'luodi_url')->value('value');
                if (!empty($luodiUrl)) {
                    // 确保落地域名有协议
                    if (!preg_match('/^https?:\/\//', $luodiUrl)) {
                        $luodiUrl = 'https://' . $luodiUrl;
                    }
                    // 获取当前请求的完整路径和参数（从REQUEST_URI提取，确保完整保留）
                    $requestPath = '';
                    $queryString = '';
                    
                    if (isset($_SERVER['REQUEST_URI'])) {
                        $parsed = parse_url($_SERVER['REQUEST_URI']);
                        // 获取路径部分（包含/index等）
                        if (isset($parsed['path']) && !empty($parsed['path'])) {
                            $requestPath = $parsed['path'];
                        }
                        // 获取查询字符串（包含所有参数）
                        if (isset($parsed['query']) && !empty($parsed['query'])) {
                            $queryString = $parsed['query'];
                        }
                    } else {
                        // 如果REQUEST_URI不可用，使用pathinfo和QUERY_STRING
                        $requestPath = $this->request->pathinfo();
                        if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
                            $queryString = $_SERVER['QUERY_STRING'];
                        }
                    }
                    
                    // 构建跳转URL：只替换域名，保留完整路径和参数
                    $redirectUrl = rtrim($luodiUrl, '/');
                    if (!empty($requestPath)) {
                        // 确保路径以/开头
                        $requestPath = '/' . ltrim($requestPath, '/');
                        $redirectUrl .= $requestPath;
                    }
                    if (!empty($queryString)) {
                        $redirectUrl .= '?' . $queryString;
                    }
                    // 输出过渡页面，显示加载中，1秒后跳转
                    $html = '<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>加载中...</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #fff;
        }
        .loading-container {
            text-align: center;
            padding: 40px;
        }
        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 30px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .loading-text {
            font-size: 18px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <div class="loading-spinner"></div>
        <div class="loading-text">加载中，请稍后</div>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = "' . htmlspecialchars($redirectUrl, ENT_QUOTES, 'UTF-8') . '";
        }, 1000);
    </script>
</body>
</html>';
                    echo $html;
                    exit;
                }
            }
        }
        
        // 如果没有key参数，重定向到后台管理页面
        $key = '';
        // 从 REQUEST_URI 提取原始 key 值，保持大小写
        if (isset($_SERVER['REQUEST_URI'])) {
            $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
            if ($queryString) {
                parse_str($queryString, $params);
                if (isset($params['key'])) {
                    $key = $params['key'];
                }
            }
        }
        // 如果还是没有获取到 key，尝试从 QUERY_STRING 获取
        if (empty($key) && isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $params);
            if (isset($params['key'])) {
                $key = $params['key'];
            }
        }
        // 如果还是没有，从 $_GET 获取（可能已被转换）
        if (empty($key) && isset($_GET['key'])) {
            $key = $_GET['key'];
        }
        
        // 如果没有key参数，重定向到后台管理页面
        if (empty($key)) {
            $this->redirect(url('admin/index/index'));
            return;
        }
        
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
        
        if (!$product) {
            // 产品不存在
            $this->assign('product', null);
            $this->assign('query_times', 0);
            return $this->fetch('/index');
        }
        
        // 获取当前数据库中的查询次数，如果没有query_times字段或为null，默认为0
        $current_query_times = 0;
        if (isset($product['query_times']) && $product['query_times'] !== null && $product['query_times'] !== '') {
            $current_query_times = intval($product['query_times']);
        }
        
        // 每次扫码，查询次数增加1（用于更新数据库）
        $new_query_times = $current_query_times + 1;
        
        // 更新数据库中的查询次数
        Db::name('company')->where('id', $product['id'])->update([
            'query_times' => $new_query_times,
            'update_time' => time()
        ]);
        
        // 如果是第一次查询（更新前是0），尝试记录首次查询时间
        // 如果字段不存在，使用try-catch忽略错误
        if ($current_query_times == 0) {
            try {
                Db::name('company')->where('id', $product['id'])->update([
                    'first_query_time' => time()
                ]);
            } catch (\Exception $e) {
                // 字段不存在，忽略错误，不影响主流程
            }
        }
        
        // 传递给模板的query_times是更新后的值（用于显示）
        $query_times = $new_query_times;
        
        // 设置查询信息
        $query_count = $new_query_times;
        // 安全获取首次查询时间，如果字段不存在则使用当前时间
        $first_query_time = date('Y-m-d H:i:s');
        if (isset($product['first_query_time']) && $product['first_query_time'] && $product['first_query_time'] > 0) {
            $first_query_time = date('Y-m-d H:i:s', $product['first_query_time']);
        }
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
        $this->assign('query_times', $query_times);
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
