#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
从Network面板数据中提取页面内容
如果你能导出Network面板的HAR文件，这个脚本可以分析它
"""
import json
import re

def analyze_har_file(har_file_path):
    """分析HAR文件，提取所有有用的内容"""
    try:
        with open(har_file_path, 'r', encoding='utf-8') as f:
            har_data = json.load(f)
        
        print("=" * 80)
        print("分析HAR文件")
        print("=" * 80)
        
        entries = har_data.get('log', {}).get('entries', [])
        
        # 分类请求
        html_responses = []
        json_responses = []
        api_responses = []
        
        for entry in entries:
            request = entry.get('request', {})
            response = entry.get('response', {})
            url = request.get('url', '')
            
            # 检查响应内容
            content = response.get('content', {})
            mime_type = content.get('mimeType', '')
            text = content.get('text', '')
            
            # HTML响应
            if 'text/html' in mime_type and text:
                html_responses.append({
                    'url': url,
                    'status': response.get('status', 0),
                    'content': text[:500]  # 只显示前500字符
                })
            
            # JSON响应
            elif 'application/json' in mime_type and text:
                json_responses.append({
                    'url': url,
                    'status': response.get('status', 0),
                    'content': text
                })
            
            # API相关
            if 'api' in url.lower() or 'route' in url.lower():
                api_responses.append({
                    'url': url,
                    'status': response.get('status', 0),
                    'mime_type': mime_type,
                    'content_length': len(text) if text else 0
                })
        
        # 输出结果
        print(f"\n发现 {len(html_responses)} 个HTML响应:")
        for i, resp in enumerate(html_responses, 1):
            print(f"\n{i}. {resp['url']}")
            print(f"   状态: {resp['status']}")
            print(f"   内容预览: {resp['content']}")
            if resp['status'] == 200:
                # 保存成功的HTML响应
                filename = f'har_html_{i}.html'
                with open(filename, 'w', encoding='utf-8') as f:
                    f.write(resp['content'])
                print(f"   ✓ 已保存到: {filename}")
        
        print(f"\n发现 {len(json_responses)} 个JSON响应:")
        for i, resp in enumerate(json_responses, 1):
            print(f"\n{i}. {resp['url']}")
            print(f"   状态: {resp['status']}")
            try:
                json_data = json.loads(resp['content'])
                print(f"   JSON数据: {json.dumps(json_data, ensure_ascii=False, indent=2)[:500]}")
                # 保存JSON
                filename = f'har_json_{i}.json'
                with open(filename, 'w', encoding='utf-8') as f:
                    json.dump(json_data, f, ensure_ascii=False, indent=2)
                print(f"   ✓ 已保存到: {filename}")
            except:
                print(f"   内容: {resp['content'][:500]}")
        
        print(f"\n发现 {len(api_responses)} 个API相关请求:")
        for resp in api_responses:
            print(f"   {resp['url']}")
            print(f"   状态: {resp['status']}, 类型: {resp['mime_type']}, 长度: {resp['content_length']}")
        
    except FileNotFoundError:
        print(f"错误: 找不到文件 {har_file_path}")
    except json.JSONDecodeError:
        print("错误: HAR文件格式不正确")
    except Exception as e:
        print(f"错误: {str(e)}")

def extract_urls_from_network():
    """从Network面板中提取所有URL"""
    print("=" * 80)
    print("从Network面板提取URL的方法")
    print("=" * 80)
    print("""
1. 在微信开发者工具的Network面板中：
   - 右键点击任意请求
   - 选择 "Copy" → "Copy as cURL"
   - 或者 "Copy" → "Copy request URL"

2. 导出HAR文件：
   - 在Network面板中，右键点击任意位置
   - 选择 "Save all as HAR with content"
   - 保存HAR文件
   - 然后运行: python extract_from_network.py <har文件路径>

3. 手动复制响应内容：
   - 点击每个请求
   - 查看 Response 或 Preview 标签
   - 复制内容并保存
    """)

if __name__ == '__main__':
    import sys
    if len(sys.argv) > 1:
        har_file = sys.argv[1]
        analyze_har_file(har_file)
    else:
        extract_urls_from_network()
        print("\n使用方法:")
        print("  python extract_from_network.py <har文件路径>")





