#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
获取完整页面内容的脚本
尝试多种方法获取 http://e.langjiu.cn/?c=FVKXMH1S 的完整HTML
"""
import urllib.request
import urllib.parse
import re

def get_page_with_ua(url, user_agent, description=""):
    """使用指定的User-Agent获取页面"""
    try:
        req = urllib.request.Request(url)
        req.add_header('User-Agent', user_agent)
        req.add_header('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8')
        req.add_header('Accept-Language', 'zh-CN,zh;q=0.9,en;q=0.8')
        req.add_header('Referer', 'https://mp.weixin.qq.com/')
        req.add_header('Accept-Encoding', 'identity')  # 不使用压缩，避免编码问题
        
        response = urllib.request.urlopen(req, timeout=15)
        content_bytes = response.read()
        
        # 尝试不同编码
        encodings = ['gbk', 'utf-8', 'gb2312', 'big5']
        for enc in encodings:
            try:
                content = content_bytes.decode(enc)
                return content, enc
            except:
                continue
        
        # 如果都失败，尝试忽略错误
        return content_bytes.decode('utf-8', errors='ignore'), 'utf-8-ignore'
        
    except Exception as e:
        return None, str(e)

def extract_resources(html_content):
    """从HTML中提取外部资源链接"""
    resources = {
        'scripts': [],
        'stylesheets': [],
        'images': [],
        'links': []
    }
    
    # 提取script src
    scripts = re.findall(r'<script[^>]*src=["\']([^"\']+)["\']', html_content, re.I)
    resources['scripts'] = scripts
    
    # 提取link href (CSS等)
    links = re.findall(r'<link[^>]*href=["\']([^"\']+)["\']', html_content, re.I)
    resources['links'] = links
    
    # 提取img src
    images = re.findall(r'<img[^>]*src=["\']([^"\']+)["\']', html_content, re.I)
    resources['images'] = images
    
    # 提取可能的AJAX URL
    ajax_urls = re.findall(r'["\']([^"\']*(?:api|ajax|get|post|fetch)[^"\']*)["\']', html_content, re.I)
    resources['ajax'] = ajax_urls
    
    return resources

def main():
    url = 'http://e.langjiu.cn/?c=FVKXMH1S'
    
    # 多个微信User-Agent
    user_agents = [
        ('Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.120 Mobile Safari/537.36 MicroMessenger/8.0.40.2420(0x28002837) Process/tools NetType/WIFI Language/zh_CN ABI/arm64', 
         '微信Android最新版'),
        ('Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/8.0.0', 
         '微信iOS'),
        ('Mozilla/5.0 (Linux; Android 10; SM-G975F) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.108 Mobile Safari/537.36 MicroMessenger/7.0.20.1781', 
         '微信Android旧版'),
    ]
    
    best_content = None
    best_encoding = None
    best_length = 0
    
    print("=" * 80)
    print("开始获取完整页面内容...")
    print("=" * 80)
    
    for ua, desc in user_agents:
        print(f"\n尝试: {desc}")
        print(f"User-Agent: {ua[:60]}...")
        
        content, encoding = get_page_with_ua(url, ua, desc)
        
        if content:
            length = len(content)
            print(f"✓ 成功获取内容")
            print(f"  编码: {encoding}")
            print(f"  长度: {length} 字符")
            print(f"  预览 (前800字符):")
            print("  " + "-" * 76)
            preview = content[:800].replace('\n', '\n  ')
            print(f"  {preview}")
            print("  " + "-" * 76)
            
            # 提取资源
            resources = extract_resources(content)
            if resources['scripts']:
                print(f"  发现 {len(resources['scripts'])} 个外部脚本")
            if resources['links']:
                print(f"  发现 {len(resources['links'])} 个外部链接")
            if resources['ajax']:
                print(f"  发现 {len(resources['ajax'])} 个可能的AJAX端点")
            
            # 保存最长的内容
            if length > best_length:
                best_content = content
                best_encoding = encoding
                best_length = length
        else:
            print(f"✗ 获取失败: {encoding}")
    
    # 保存最佳结果
    if best_content:
        output_file = 'temp_page.html'
        with open(output_file, 'w', encoding='utf-8') as f:
            f.write(best_content)
        
        print("\n" + "=" * 80)
        print(f"✓ 已保存完整内容到: {output_file}")
        print(f"  内容长度: {best_length} 字符")
        print(f"  使用编码: {best_encoding}")
        print("=" * 80)
        
        # 分析内容
        print("\n内容分析:")
        print(f"  - 包含 <script> 标签: {best_content.count('<script')} 个")
        print(f"  - 包含 <div> 标签: {best_content.count('<div')} 个")
        print(f"  - 包含 <img> 标签: {best_content.count('<img')} 个")
        print(f"  - 包含 '微信' 关键词: {best_content.count('微信')} 次")
        
        # 检查是否有动态加载的迹象
        if 'document.write' in best_content or 'innerHTML' in best_content or 'appendChild' in best_content:
            print("  ⚠ 检测到动态内容加载，可能需要执行JavaScript才能看到完整内容")
        
        if 'ajax' in best_content.lower() or 'fetch' in best_content.lower() or 'xmlhttprequest' in best_content.lower():
            print("  ⚠ 检测到AJAX请求，内容可能通过异步加载")
            
    else:
        print("\n✗ 未能获取到页面内容")
    
    print("\n完成！")

if __name__ == '__main__':
    main()





