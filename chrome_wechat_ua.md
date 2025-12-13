# Chrome设置微信User-Agent获取完整页面内容

## 方法1：开发者工具（推荐）

1. 打开Chrome浏览器
2. 按 `F12` 打开开发者工具
3. 按 `Ctrl+Shift+P` (Mac: `Cmd+Shift+P`) 打开命令面板
4. 输入 "Network conditions" 或 "网络条件"
5. 在Network conditions面板中：
   - 取消勾选 "Use browser default"
   - 选择 "Custom..." 
   - 粘贴以下User-Agent：
   ```
   Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.120 Mobile Safari/537.36 MicroMessenger/8.0.40.2420(0x28002837) Process/tools NetType/WIFI Language/zh_CN ABI/arm64
   ```
6. 访问：http://e.langjiu.cn/?c=FVKXMH1S
7. 页面加载后，右键 → "查看网页源代码" 或按 `Ctrl+U` 获取完整HTML

## 方法2：启动参数

如果Chrome安装在默认位置，可以运行 `open_with_wechat_ua.bat`

或者手动运行：
```bash
chrome.exe --user-agent="Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.120 Mobile Safari/537.36 MicroMessenger/8.0.40.2420(0x28002837) Process/tools NetType/WIFI Language/zh_CN ABI/arm64" "http://e.langjiu.cn/?c=FVKXMH1S"
```

## 方法3：使用扩展

安装 "User-Agent Switcher" 扩展，可以快速切换User-Agent

## 获取完整HTML的方法

设置好User-Agent并打开页面后：

1. **方法A：查看源代码**
   - 右键页面 → "查看网页源代码"
   - 或按 `Ctrl+U`
   - 复制全部内容

2. **方法B：开发者工具**
   - 按 `F12` 打开开发者工具
   - 在 Elements 标签中，右键 `<html>` 标签
   - 选择 "Copy" → "Copy outerHTML"
   - 这会复制完整的HTML

3. **方法C：控制台命令**
   - 按 `F12` 打开开发者工具
   - 在 Console 中输入：
   ```javascript
   document.documentElement.outerHTML
   ```
   - 复制输出的完整HTML

## 注意事项

- 如果页面内容是通过JavaScript动态加载的，需要等待页面完全加载后再获取HTML
- 可以在Console中运行 `document.documentElement.outerHTML` 获取最终渲染的HTML
- 如果页面有AJAX请求，可以在Network标签中查看所有请求





