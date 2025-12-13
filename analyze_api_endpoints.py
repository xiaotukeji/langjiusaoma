#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
分析API端点，尝试直接获取数据
如果页面数据是通过API加载的，可以直接调用API
"""
import urllib.request
import json
import urllib.parse

def try_api_endpoints(code='FVKXMH1S'):
    """尝试各种可能的API端点"""
    
    base_urls = [
        'https://yxwg.langjiu.cn',
        'http://e.langjiu.cn',
    ]
    
    api_patterns = [
        '/api/v1/api-wxservice/wechat/codescan',
        '/uspyxwx/wechat/codescan/route',
        '/api/codescan',
        '/api/route',
        '/codescan',
        '/route',
    ]
    
    user_agent = 'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.120 Mobile Safari/537.36 MicroMessenger/8.0.40.2420'
    
    print("=" * 80)
    print("尝试直接访问API端点")
    print("=" * 80)
    
    results = []
    
    for base_url in base_urls:
        for pattern in api_patterns:
            # 尝试GET请求
            for method in ['GET', 'POST']:
                url = f"{base_url}{pattern}?code={code}"
                
                try:
                    req = urllib.request.Request(url)
                    req.add_header('User-Agent', user_agent)
                    req.add_header('Referer', 'https://mp.weixin.qq.com/')
                    
                    if method == 'POST':
                        req.add_header('Content-Type', 'application/x-www-form-urlencoded')
                        data = urllib.parse.urlencode({'code': code}).encode()
                        response = urllib.request.urlopen(req, data=data, timeout=10)
                    else:
                        response = urllib.request.urlopen(req, timeout=10)
                    
                    content = response.read()
                    
                    # 尝试解码
                    try:
                        text = content.decode('utf-8')
                    except:
                        try:
                            text = content.decode('gbk')
                        except:
                            text = content.decode('utf-8', errors='ignore')
                    
                    # 检查是否是JSON
                    try:
                        json_data = json.loads(text)
                        print(f"\n[SUCCESS] {method} {url}")
                        print(f"  返回JSON数据:")
                        print(json.dumps(json_data, ensure_ascii=False, indent=2)[:500])
                        results.append({
                            'url': url,
                            'method': method,
                            'type': 'json',
                            'data': json_data
                        })
                    except:
                        # 检查是否是HTML
                        if '<html' in text.lower() or '<!doctype' in text.lower():
                            if len(text) > 2000:  # 可能是完整页面
                                print(f"\n[SUCCESS] {method} {url}")
                                print(f"  返回HTML，长度: {len(text)} 字符")
                                results.append({
                                    'url': url,
                                    'method': method,
                                    'type': 'html',
                                    'data': text
                                })
                        else:
                            print(f"\n[INFO] {method} {url}")
                            print(f"  返回文本，长度: {len(text)} 字符")
                            print(f"  预览: {text[:200]}")
                
                except urllib.error.HTTPError as e:
                    if e.code not in [404, 403]:
                        print(f"\n[ERROR] {method} {url}")
                        print(f"  HTTP {e.code}: {e.reason}")
                except Exception as e:
                    pass  # 静默失败，继续尝试下一个
    
    # 保存结果
    if results:
        print("\n" + "=" * 80)
        print("保存结果")
        print("=" * 80)
        
        for i, result in enumerate(results, 1):
            if result['type'] == 'json':
                filename = f'api_result_{i}.json'
                with open(filename, 'w', encoding='utf-8') as f:
                    json.dump(result['data'], f, ensure_ascii=False, indent=2)
                print(f"✓ JSON数据已保存到: {filename}")
            elif result['type'] == 'html':
                filename = f'api_result_{i}.html'
                with open(filename, 'w', encoding='utf-8') as f:
                    f.write(result['data'])
                print(f"✓ HTML已保存到: {filename}")
    else:
        print("\n未找到可用的API端点")
        print("建议使用Fiddler抓包方案")

if __name__ == '__main__':
    try_api_endpoints('FVKXMH1S')





