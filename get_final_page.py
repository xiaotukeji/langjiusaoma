#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
尝试直接访问最终的目标页面，绕过OAuth授权
"""
import urllib.request
import urllib.parse

# 从OAuth跳转URL中提取的最终目标页面
final_url = 'https://yxwg.langjiu.cn/uspyxwx/wechat/codescan/route?code=FVKXMH1S'

# 也尝试其他可能的URL
alternative_urls = [
    'https://yxwg.langjiu.cn/uspyxwx/wechat/codescan/route?code=FVKXMH1S',
    'http://e.langjiu.cn/?c=FVKXMH1S&skip_auth=1',
    'http://e.langjiu.cn/codescan/route?code=FVKXMH1S',
    'https://yxwg.langjiu.cn/api/v1/api-wxservice/wechat/codescan?code=FVKXMH1S',
]

print("=" * 80)
print("尝试访问最终目标页面")
print("=" * 80)

user_agent = 'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.120 Mobile Safari/537.36 MicroMessenger/8.0.40.2420'

for url in [final_url] + alternative_urls:
    print(f"\n尝试访问: {url}")
    try:
        req = urllib.request.Request(url)
        req.add_header('User-Agent', user_agent)
        req.add_header('Referer', 'https://mp.weixin.qq.com/')
        req.add_header('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8')
        
        response = urllib.request.urlopen(req, timeout=10)
        content_bytes = response.read()
        
        # 尝试不同编码
        encodings = ['utf-8', 'gbk', 'gb2312']
        content = None
        used_encoding = None
        
        for enc in encodings:
            try:
                content = content_bytes.decode(enc)
                used_encoding = enc
                break
            except:
                continue
        
        if content:
            print(f"  [OK] 成功获取内容")
            print(f"  编码: {used_encoding}")
            print(f"  长度: {len(content)} 字符")
            print(f"  预览 (前500字符):")
            print("  " + "-" * 76)
            print("  " + content[:500].replace('\n', '\n  '))
            print("  " + "-" * 76)
            
            # 检查是否包含实际内容
            if len(content) > 2000:  # 如果内容较长，可能是完整页面
                filename = f'final_page_{url.split("/")[-1].split("?")[0]}.html'
                with open(filename, 'w', encoding='utf-8') as f:
                    f.write(content)
                print(f"  [OK] 已保存到: {filename}")
        else:
            print(f"  [FAIL] 无法解码内容")
            
    except urllib.error.HTTPError as e:
        print(f"  [ERROR] HTTP错误: {e.code} - {e.reason}")
        if e.code == 302 or e.code == 301:
            redirect_url = e.headers.get('Location')
            print(f"  重定向到: {redirect_url}")
    except Exception as e:
        print(f"  [ERROR] 错误: {str(e)}")

print("\n" + "=" * 80)
print("分析OAuth流程")
print("=" * 80)

# 解析OAuth URL
oauth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxf49a598785fe0643&redirect_uri=https%3A%2F%2Fyxwg.langjiu.cn%2Fapi%2Fv1%2Fapi-wxservice%2Fwechat%2Fauth%2FgetAccesstoken%3Fscopeflag%3D1%26returl%3Dhttps%253A%252F%252Fyxwg.langjiu.cn%252Fuspyxwx%252Fwechat%252Fauth%252FgetAccesstoken%253Fcidm%253D1%2526returl%253Dhttps%25253A%25252F%25252Fyxwg.langjiu.cn%25252Fuspyxwx%25252Fwechat%25252Fcodescan%25252Froute%25253Fcode%25253DFVKXMH1S%252526sort%25253D%252526echo%25253D%2526appid%253Dwxf49a598785fe0643&response_type=code&state=123&scope=snsapi_userinfo&component_appid=wx74f36f3829aed7c3&connect_redirect=1#wechat_redirect'

parsed = urllib.parse.urlparse(oauth_url)
params = urllib.parse.parse_qs(parsed.query)

print("OAuth参数:")
print(f"  appid: {params.get('appid', [''])[0]}")
print(f"  redirect_uri: {urllib.parse.unquote(params.get('redirect_uri', [''])[0])}")
print(f"  scope: {params.get('scope', [''])[0]}")
print(f"  最终目标: yxwg.langjiu.cn/uspyxwx/wechat/codescan/route?code=FVKXMH1S")

print("\n" + "=" * 80)
print("结论:")
print("=" * 80)
print("这个页面需要微信OAuth授权才能访问完整内容。")
print("建议:")
print("1. 在真实的微信中打开原始链接: http://e.langjiu.cn/?c=FVKXMH1S")
print("2. 完成授权后，在微信中查看完整页面内容")
print("3. 使用微信开发者工具或浏览器开发者工具获取完整HTML")

