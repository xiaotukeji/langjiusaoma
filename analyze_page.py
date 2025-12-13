#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
分析页面JavaScript逻辑，尝试找出完整内容的加载方式
"""
import re
import urllib.request

# 读取当前获取到的HTML
with open('temp_page.html', 'r', encoding='utf-8') as f:
    html = f.read()

print("=" * 80)
print("分析页面结构和JavaScript逻辑")
print("=" * 80)

# 提取所有script标签内容
scripts = re.findall(r'<script[^>]*>(.*?)</script>', html, re.DOTALL | re.I)
print(f"\n发现 {len(scripts)} 个script标签\n")

for i, script in enumerate(scripts, 1):
    print(f"Script {i}:")
    print("-" * 80)
    print(script)
    print("-" * 80)
    
    # 分析script内容
    if 'isWeixin' in script:
        print("\n分析:")
        print("  - 检测微信环境")
        if 'if (!isWeixin)' in script:
            print("  - 如果不是微信环境，显示错误信息")
        if 'isWeixin' in script and 'if (!isWeixin)' not in script:
            print("  - 如果是微信环境，可能继续执行其他代码")
    
    # 查找可能的URL或API调用
    urls = re.findall(r'["\']([^"\']*(?:http|api|ajax|get|post|fetch|load|url)[^"\']*)["\']', script, re.I)
    if urls:
        print(f"  发现可能的URL/API: {urls}")
    
    # 查找可能的变量或函数调用
    functions = re.findall(r'(\w+)\s*\([^)]*\)', script)
    if functions:
        print(f"  发现函数调用: {set(functions)}")

# 检查是否有内联的HTML内容（在字符串中）
html_in_strings = re.findall(r'["\']([^"\']*<[^>]+>[^"\']*)["\']', html, re.I)
if html_in_strings:
    print(f"\n在字符串中发现HTML片段:")
    for h in html_in_strings[:3]:  # 只显示前3个
        print(f"  {h[:100]}...")

# 尝试查找可能的其他端点
print("\n" + "=" * 80)
print("尝试查找可能的API端点或数据源")
print("=" * 80)

base_url = 'http://e.langjiu.cn'
code = 'FVKXMH1S'

# 可能的端点模式
endpoints = [
    f'{base_url}/index.php?c={code}',
    f'{base_url}/api.php?c={code}',
    f'{base_url}/data.php?c={code}',
    f'{base_url}/get.php?c={code}',
    f'{base_url}/ajax.php?c={code}',
    f'{base_url}/?c={code}&format=json',
    f'{base_url}/?c={code}&ajax=1',
    f'{base_url}/?c={code}&callback=jsonp',
]

for endpoint in endpoints:
    try:
        req = urllib.request.Request(endpoint)
        req.add_header('User-Agent', 'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.120 Mobile Safari/537.36 MicroMessenger/8.0.40.2420')
        response = urllib.request.urlopen(req, timeout=5)
        data = response.read()
        
        # 尝试解码
        try:
            content = data.decode('gbk')
            if len(content) > 200 and content != html:
                print(f"\n✓ 发现不同内容: {endpoint}")
                print(f"  长度: {len(content)} 字符")
                print(f"  预览: {content[:300]}")
        except:
            try:
                content = data.decode('utf-8')
                if len(content) > 200 and content != html:
                    print(f"\n✓ 发现不同内容: {endpoint}")
                    print(f"  长度: {len(content)} 字符")
                    print(f"  预览: {content[:300]}")
            except:
                if len(data) > 200:
                    print(f"\n✓ 发现二进制数据: {endpoint}")
                    print(f"  长度: {len(data)} 字节")
    except Exception as e:
        pass

print("\n" + "=" * 80)
print("分析完成")
print("=" * 80)





