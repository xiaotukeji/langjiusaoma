#!/usr/bin/env python
# -*- coding: utf-8 -*-
import urllib.request
import urllib.parse

url = 'http://e.langjiu.cn/?c=FVKXMH1S'

# 尝试不同的User-Agent和请求方式
user_agents = [
    # 微信Android
    'Mozilla/5.0 (Linux; Android 10; SM-G975F) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.108 Mobile Safari/537.36 MicroMessenger/7.0.20.1781',
    # 微信iOS
    'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/8.0.0',
    # 完整微信User-Agent
    'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.120 Mobile Safari/537.36 MicroMessenger/8.0.40.2420(0x28002837) Process/tools NetType/WIFI Language/zh_CN ABI/arm64',
]

print("尝试获取完整页面内容...\n")

for i, ua in enumerate(user_agents, 1):
    print(f"尝试方法 {i}: 使用微信User-Agent")
    try:
        req = urllib.request.Request(url)
        req.add_header('User-Agent', ua)
        req.add_header('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8')
        req.add_header('Accept-Language', 'zh-CN,zh;q=0.9')
        req.add_header('Referer', 'https://mp.weixin.qq.com/')
        
        response = urllib.request.urlopen(req, timeout=10)
        content = response.read()
        
        # 尝试不同编码
        encodings = ['gbk', 'utf-8', 'gb2312']
        decoded_content = None
        
        for enc in encodings:
            try:
                decoded_content = content.decode(enc)
                print(f"  成功使用 {enc} 编码解码")
                break
            except:
                continue
        
        if decoded_content:
            print(f"  内容长度: {len(decoded_content)} 字符")
            print(f"  前500字符预览:\n{decoded_content[:500]}\n")
            
            # 如果内容比之前的更长，保存它
            if len(decoded_content) > 200:
                with open('temp_page.html', 'w', encoding='utf-8') as f:
                    f.write(decoded_content)
                print(f"  ✓ 已保存到 temp_page.html\n")
        else:
            print(f"  无法解码内容\n")
            
    except Exception as e:
        print(f"  错误: {str(e)}\n")

# 尝试访问可能的API端点
print("\n尝试查找API端点...")
api_endpoints = [
    'http://e.langjiu.cn/api?c=FVKXMH1S',
    'http://e.langjiu.cn/index.php?c=FVKXMH1S',
    'http://e.langjiu.cn/data?c=FVKXMH1S',
    'http://e.langjiu.cn/get?c=FVKXMH1S',
]

for endpoint in api_endpoints:
    try:
        req = urllib.request.Request(endpoint)
        req.add_header('User-Agent', user_agents[0])
        response = urllib.request.urlopen(req, timeout=5)
        data = response.read()
        print(f"  {endpoint}: 响应长度 {len(data)} 字节")
        if len(data) > 100:
            try:
                print(f"    内容: {data.decode('gbk', errors='ignore')[:200]}")
            except:
                print(f"    内容: {data[:200]}")
    except Exception as e:
        pass

print("\n完成！")





