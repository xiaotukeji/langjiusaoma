# Fiddler配置HTTPS解密 - 详细步骤

## 问题说明

你看到的提示表示：
- Fiddler检测到了HTTPS连接（加密流量）
- 但无法查看内容，因为流量是加密的
- 需要配置Fiddler来解密HTTPS流量

## 解决步骤

### 步骤1：启用HTTPS解密

1. **打开Fiddler**
2. **点击菜单：Tools → Options**
3. **切换到 HTTPS 标签页**
4. **勾选以下选项：**
   - ✅ **Capture HTTPS CONNECTs** （捕获HTTPS连接）
   - ✅ **Decrypt HTTPS traffic** （解密HTTPS流量）
   - ✅ **Ignore server certificate errors** （忽略服务器证书错误，可选）

### 步骤2：安装Fiddler根证书（重要！）

1. **在HTTPS标签页中，点击 "Actions" 按钮**
2. **选择 "Trust Root Certificate"** （信任根证书）
3. **会弹出Windows安全提示，点击 "是" 或 "Yes"**
4. **如果成功，会显示 "The root certificate was successfully installed"**

### 步骤3：在手机上安装证书（如果抓手机流量）

1. **查看Fiddler显示的IP地址和端口**
   - 通常在Fiddler右上角显示，例如：`192.168.1.100:8888`

2. **在手机浏览器中访问：**
   ```
   http://你的电脑IP:8888
   ```
   例如：`http://192.168.1.100:8888`

3. **下载证书：**
   - 点击页面上的 **"FiddlerRoot certificate"** 链接
   - 下载证书文件

4. **安装证书：**
   
   **Android手机：**
   - 下载证书后，打开设置
   - 安全 → 加密与凭据 → 从存储设备安装
   - 选择下载的证书文件
   - 输入设备密码/PIN码
   - 证书名称可以随意填写，如"Fiddler"
   - 点击确定

   **iPhone：**
   - 下载证书后，打开设置
   - 通用 → 关于本机 → 证书信任设置
   - 找到Fiddler证书，启用完全信任

### 步骤4：验证配置

1. **重启Fiddler**（建议重启以确保配置生效）

2. **测试HTTPS网站：**
   - 在浏览器中访问：`https://www.baidu.com`
   - 在Fiddler中应该能看到请求和响应内容（不再是加密的）

3. **查看请求：**
   - 在Fiddler左侧列表中，HTTPS请求应该显示为：
     - 绿色锁图标（已解密）
     - 可以查看Request和Response内容

### 步骤5：开始抓包

1. **确保Fiddler正在捕获流量：**
   - 查看Fiddler左下角，应该显示 "Capturing"（正在捕获）

2. **配置手机代理：**
   - 手机WiFi设置 → 代理 → 手动
   - 主机：你的电脑IP
   - 端口：8888

3. **在手机微信中打开目标页面**

4. **在Fiddler中查看：**
   - 应该能看到所有请求（包括HTTPS）
   - 可以查看完整的Request和Response

## 常见问题

### Q1: 点击"Trust Root Certificate"没有反应？
**A:** 
- 尝试以管理员身份运行Fiddler
- 或者手动安装证书（见下方）

### Q2: 证书安装失败？
**A:**
- 确保以管理员身份运行Fiddler
- 检查Windows防火墙设置
- 尝试手动导出证书：
  - Tools → Options → HTTPS → Actions → Export Root Certificate to Desktop
  - 然后手动安装证书

### Q3: 手机无法访问 http://IP:8888？
**A:**
- 检查手机和电脑是否在同一WiFi网络
- 检查Windows防火墙是否允许8888端口
- 尝试关闭防火墙测试

### Q4: 仍然看到加密内容？
**A:**
- 确认已勾选 "Decrypt HTTPS traffic"
- 确认已安装根证书
- 重启Fiddler
- 清除浏览器缓存后重试

### Q5: 某些网站仍然无法解密？
**A:**
- 某些应用使用了证书绑定（Certificate Pinning）
- 微信可能使用了证书绑定，需要额外配置
- 可以尝试使用其他抓包工具（如Charles）

## 微信特殊处理

如果微信仍然无法抓包，可能是因为：
1. **微信使用了证书绑定**
2. **微信使用了自定义加密**

**解决方案：**
- 尝试使用 **Charles**（对微信支持更好）
- 或者使用 **mitmproxy** + **JustTrustMe**（Android）
- 或者使用 **Postern**（Android代理工具）

## 验证是否成功

成功配置后，你应该能看到：
- ✅ HTTPS请求显示为绿色锁图标
- ✅ 可以查看Request Headers和Body
- ✅ 可以查看Response Headers和Body
- ✅ 不再是 "CONNECT tunnel" 提示



