#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Fiddler抓包配置指南和辅助脚本
"""
import os
import subprocess
import socket

def get_local_ip():
    """获取本机IP地址"""
    try:
        # 连接到一个远程地址来获取本机IP
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        s.connect(("8.8.8.8", 80))
        ip = s.getsockname()[0]
        s.close()
        return ip
    except:
        return "无法获取"

def check_fiddler_running():
    """检查Fiddler是否在运行"""
    try:
        # 尝试连接Fiddler的默认端口
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        result = sock.connect_ex(('127.0.0.1', 8888))
        sock.close()
        return result == 0
    except:
        return False

def main():
    print("=" * 80)
    print("Fiddler抓包配置指南")
    print("=" * 80)
    
    local_ip = get_local_ip()
    print(f"\n本机IP地址: {local_ip}")
    print(f"Fiddler代理端口: 8888")
    
    if check_fiddler_running():
        print("\n✓ Fiddler正在运行")
    else:
        print("\n✗ Fiddler未运行，请先启动Fiddler")
    
    print("\n" + "=" * 80)
    print("配置步骤:")
    print("=" * 80)
    print("""
1. 下载安装Fiddler Classic
   地址: https://www.telerik.com/download/fiddler

2. 配置Fiddler HTTPS解密
   - Tools → Options → HTTPS
   - 勾选 "Capture HTTPS CONNECTs"
   - 勾选 "Decrypt HTTPS traffic"
   - 点击 "Actions" → "Trust Root Certificate"

3. 配置手机代理
   - 手机连接与电脑相同的WiFi
   - 手机WiFi设置 → 代理 → 手动
   - 主机: """ + local_ip + """
   - 端口: 8888
   - 保存

4. 安装Fiddler证书到手机
   - 手机浏览器访问: http://""" + local_ip + """:8888
   - 点击 "FiddlerRoot certificate"
   - 下载并安装证书
   - Android: 设置 → 安全 → 从存储设备安装
   - iOS: 设置 → 通用 → 关于本机 → 证书信任设置

5. 开始抓包
   - 在手机微信中打开: http://e.langjiu.cn/?c=FVKXMH1S
   - 完成授权流程
   - 在Fiddler中查看所有请求

6. 保存完整HTML
   - 在Fiddler中找到目标页面的请求
   - 状态码应该是200
   - 右键 → "Save" → "Response" → "Response Body"
   - 保存为HTML文件
    """)
    
    print("\n" + "=" * 80)
    print("Fiddler使用技巧:")
    print("=" * 80)
    print("""
- 使用过滤器：在Fiddler中，可以设置过滤器只显示特定域名的请求
- 搜索功能：Ctrl+F 搜索请求内容
- 导出HAR：File → Export Sessions → All Sessions → HTTPArchive v1.2
- 自动保存：可以使用FiddlerScript自动保存特定请求
    """)

if __name__ == '__main__':
    main()





