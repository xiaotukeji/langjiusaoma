#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
使用Selenium获取完整页面内容
注意：这个方案需要处理OAuth授权，可能需要手动介入
"""
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time

def setup_chrome_with_wechat_ua():
    """配置Chrome使用微信User-Agent"""
    chrome_options = Options()
    
    # 设置微信User-Agent
    user_agent = 'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.120 Mobile Safari/537.36 MicroMessenger/8.0.40.2420(0x28002837) Process/tools NetType/WIFI Language/zh_CN ABI/arm64'
    chrome_options.add_argument(f'user-agent={user_agent}')
    
    # 移动设备模拟
    chrome_options.add_argument('--window-size=375,812')  # iPhone尺寸
    chrome_options.add_experimental_option("mobileEmulation", {
        "deviceName": "iPhone 12 Pro"
    })
    
    # 其他选项
    chrome_options.add_argument('--disable-blink-features=AutomationControlled')
    chrome_options.add_experimental_option("excludeSwitches", ["enable-automation"])
    chrome_options.add_experimental_option('useAutomationExtension', False)
    
    driver = webdriver.Chrome(options=chrome_options)
    return driver

def get_complete_page(url, wait_time=30):
    """
    获取完整页面内容
    
    注意：由于需要OAuth授权，这个函数可能需要手动介入
    """
    driver = setup_chrome_with_wechat_ua()
    
    try:
        print(f"正在访问: {url}")
        driver.get(url)
        
        print("等待页面加载...")
        print("注意：如果跳转到OAuth授权页面，可能需要手动完成授权")
        
        # 等待页面加载
        time.sleep(5)
        
        # 检查是否跳转到授权页面
        current_url = driver.current_url
        if 'weixin.qq.com' in current_url or 'oauth' in current_url.lower():
            print(f"\n检测到OAuth授权页面: {current_url}")
            print("请手动完成授权，然后按回车继续...")
            input()
        
        # 等待页面完全加载
        print("等待页面内容加载...")
        WebDriverWait(driver, wait_time).until(
            EC.presence_of_element_located((By.TAG_NAME, "body"))
        )
        
        # 滚动页面触发懒加载
        print("滚动页面以触发懒加载...")
        driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
        time.sleep(2)
        driver.execute_script("window.scrollTo(0, 0);")
        time.sleep(2)
        
        # 等待所有资源加载
        print("等待所有资源加载完成...")
        time.sleep(5)
        
        # 获取完整HTML
        print("获取完整HTML...")
        complete_html = driver.execute_script("return document.documentElement.outerHTML;")
        
        # 保存
        filename = 'selenium_complete_page.html'
        with open(filename, 'w', encoding='utf-8') as f:
            f.write(complete_html)
        
        print(f"\n✓ 完整HTML已保存到: {filename}")
        print(f"  内容长度: {len(complete_html)} 字符")
        
        return complete_html
        
    except Exception as e:
        print(f"错误: {str(e)}")
        return None
        
    finally:
        print("\n关闭浏览器...")
        driver.quit()

if __name__ == '__main__':
    url = 'http://e.langjiu.cn/?c=FVKXMH1S'
    
    print("=" * 80)
    print("使用Selenium获取完整页面内容")
    print("=" * 80)
    print("\n注意：")
    print("- 需要安装Chrome和ChromeDriver")
    print("- 可能需要手动完成OAuth授权")
    print("- 如果授权失败，建议使用Fiddler抓包方案")
    print("\n" + "=" * 80 + "\n")
    
    # 检查依赖
    try:
        from selenium import webdriver
        print("✓ Selenium已安装")
    except ImportError:
        print("✗ 请先安装Selenium: pip install selenium")
        exit(1)
    
    get_complete_page(url)





