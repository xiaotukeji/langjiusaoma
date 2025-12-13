#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Fiddler HTTPS配置快速检查脚本
"""
import os
import subprocess
import socket

def check_fiddler_running():
    """检查Fiddler是否运行"""
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        result = sock.connect_ex(('127.0.0.1', 8888))
        sock.close()
        return result == 0
    except:
        return False

def get_local_ip():
    """获取本机IP"""
    try:
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        s.connect(("8.8.8.8", 80))
        ip = s.getsockname()[0]
        s.close()
        return ip
    except:
        return "无法获取"

def main():
    print("=" * 80)
    print("Fiddler HTTPS配置检查")
    print("=" * 80)
    
    # 检查Fiddler是否运行
    if check_fiddler_running():
        print("\n✓ Fiddler正在运行")
    else:
        print("\n✗ Fiddler未运行")
        print("  请先启动Fiddler")
        return
    
    local_ip = get_local_ip()
    print(f"\n本机IP: {local_ip}")
    print(f"Fiddler端口: 8888")
    
    print("\n" + "=" * 80)
    print("解决 'CONNECT tunnel' 问题的步骤：")
    print("=" * 80)
    
    print("""
1. 在Fiddler中：
   Tools → Options → HTTPS标签

2. 勾选以下选项：
   ✓ Capture HTTPS CONNECTs
   ✓ Decrypt HTTPS traffic
   ✓ Ignore server certificate errors (可选)

3. 点击 "Actions" 按钮
   选择 "Trust Root Certificate"
   在弹出的确认对话框中点击 "是"

4. 如果成功，会显示：
   "The root certificate was successfully installed"

5. 重启Fiddler（重要！）

6. 测试：
   - 在浏览器访问 https://www.baidu.com
   - 在Fiddler中应该能看到请求内容（不再是加密的）

7. 如果抓手机流量，还需要：
   - 在手机浏览器访问: http://""" + local_ip + """:8888
   - 下载并安装FiddlerRoot certificate
   - Android: 设置 → 安全 → 从存储设备安装
   - iOS: 设置 → 通用 → 关于本机 → 证书信任设置
    """)
    
    print("\n" + "=" * 80)
    print("如果仍然无法解密，可能的原因：")
    print("=" * 80)
    print("""
1. 微信使用了证书绑定（Certificate Pinning）
   → 解决方案：使用Charles或mitmproxy

2. 证书未正确安装
   → 解决方案：以管理员身份运行Fiddler，重新安装证书

3. 某些应用使用了自定义加密
   → 解决方案：尝试其他抓包工具
    """)
    
    print("\n" + "=" * 80)
    print("替代方案：")
    print("=" * 80)
    print("""
如果Fiddler无法解密微信流量，可以尝试：

1. Charles Proxy
   - 对微信支持更好
   - 下载：https://www.charlesproxy.com/

2. mitmproxy
   - 命令行工具，功能强大
   - 安装：pip install mitmproxy

3. 使用真实微信 + Chrome远程调试
   - 在手机微信中打开页面
   - 使用Chrome远程调试功能
    """)

if __name__ == '__main__':
    main()



