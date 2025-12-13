#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
检查服务器是否根据不同的请求返回不同的内容
"""
import urllib.request
import urllib.parse

url = 'http://e.langjiu.cn/?c=FVKXMH1S'

# 尝试不同的请求方式
test_cases = [
    {
        'name': '普通浏览器',
        'headers': {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        }
    },
    {
        'name': '微信Android',
        'headers': {
            'User-Agent': 'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.120 Mobile Safari/537.36 MicroMessenger/8.0.40.2420',
            'Referer': 'https://mp.weixin.qq.com/'
        }
    },
    {
        'name': '微信iOS',
        'headers': {
            'User-Agent': 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/8.0.0',
            'Referer': 'https://mp.weixin.qq.com/'
        }
    },
    {
        'name': '带Cookie',
        'headers': {
            'User-Agent': 'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.120 Mobile Safari/537.36 MicroMessenger/8.0.40.2420',
            'Cookie': 'wechat_redirect=1'
        }
    },
    {
        'name': 'POST请求',
        'method': 'POST',
        'headers': {
            'User-Agent': 'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.120 Mobile Safari/537.36 MicroMessenger/8.0.40.2420',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        'data': 'c=FVKXMH1S'
    }
]

print("=" * 80)
print("测试服务器响应")
print("=" * 80)

results = []

for test in test_cases:
    print(f"\n测试: {test['name']}")
    try:
        req = urllib.request.Request(url, headers=test.get('headers', {}))
        
        if test.get('method') == 'POST' and 'data' in test:
            req.data = test['data'].encode('utf-8')
        
        response = urllib.request.urlopen(req, timeout=10)
        content = response.read().decode('gbk', errors='ignore')
        
        results.append({
            'name': test['name'],
            'length': len(content),
            'content': content,
            'hash': hash(content)
        })
        
        print(f"  响应长度: {len(content)} 字符")
        print(f"  内容哈希: {hash(content)}")
        print(f"  预览: {content[:200].replace(chr(10), ' ')}")
        
    except Exception as e:
        print(f"  错误: {str(e)}")

# 比较结果
print("\n" + "=" * 80)
print("结果比较")
print("=" * 80)

if len(results) > 1:
    first_hash = results[0]['hash']
    all_same = all(r['hash'] == first_hash for r in results)
    
    if all_same:
        print("⚠ 所有请求返回的内容完全相同")
        print("   说明服务器不根据User-Agent返回不同内容")
        print("   页面内容可能是通过JavaScript动态加载的")
    else:
        print("✓ 发现不同的响应内容")
        for r in results:
            if r['hash'] != first_hash:
                print(f"   - {r['name']}: {r['length']} 字符")

print("\n结论:")
print("由于服务器返回的都是相同的检测脚本，完整的页面内容很可能是:")
print("1. 通过JavaScript在客户端动态生成")
print("2. 通过AJAX异步加载")
print("3. 需要特定的微信环境API（如WeixinJSBridge）")
print("\n建议:")
print("- 在微信中打开页面，使用微信开发者工具查看完整内容")
print("- 或者使用浏览器自动化工具（如Selenium）模拟微信环境并执行JavaScript")





