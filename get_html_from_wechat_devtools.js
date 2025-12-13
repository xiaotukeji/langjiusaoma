// 在微信开发者工具的Console中运行这些命令来获取完整页面内容

// ============================================
// 方法1：直接获取完整HTML（最简单）
// ============================================
console.log('=== 方法1：获取完整HTML ===');
var fullHTML = document.documentElement.outerHTML;
console.log('HTML长度:', fullHTML.length, '字符');

// 复制到剪贴板（如果支持）
if (navigator.clipboard) {
    navigator.clipboard.writeText(fullHTML).then(() => {
        console.log('✓ HTML已复制到剪贴板');
    });
} else {
    console.log('请手动复制上面的HTML内容');
}

// ============================================
// 方法2：下载为文件
// ============================================
function downloadHTML() {
    var html = document.documentElement.outerHTML;
    var blob = new Blob([html], {type: 'text/html;charset=utf-8'});
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'complete_page_' + new Date().getTime() + '.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    console.log('✓ HTML文件已下载');
}

// 运行：downloadHTML()

// ============================================
// 方法3：获取所有外部资源链接
// ============================================
function getAllResources() {
    var resources = {
        scripts: [],
        stylesheets: [],
        images: [],
        fonts: [],
        other: []
    };
    
    // 获取所有script标签
    document.querySelectorAll('script[src]').forEach(function(script) {
        resources.scripts.push(script.src);
    });
    
    // 获取所有link标签（CSS等）
    document.querySelectorAll('link[href]').forEach(function(link) {
        if (link.rel === 'stylesheet' || link.type === 'text/css') {
            resources.stylesheets.push(link.href);
        } else {
            resources.other.push({rel: link.rel, href: link.href});
        }
    });
    
    // 获取所有图片
    document.querySelectorAll('img[src]').forEach(function(img) {
        resources.images.push(img.src);
    });
    
    // 获取字体
    var styleSheets = document.styleSheets;
    for (var i = 0; i < styleSheets.length; i++) {
        try {
            var rules = styleSheets[i].cssRules || styleSheets[i].rules;
            for (var j = 0; j < rules.length; j++) {
                if (rules[j].type === CSSRule.FONT_FACE_RULE) {
                    var src = rules[j].style.getPropertyValue('src');
                    if (src) {
                        var urlMatch = src.match(/url\(['"]?([^'"]+)['"]?\)/);
                        if (urlMatch) {
                            resources.fonts.push(urlMatch[1]);
                        }
                    }
                }
            }
        } catch(e) {
            // 跨域样式表可能无法访问
        }
    }
    
    console.log('=== 页面资源列表 ===');
    console.log('脚本文件:', resources.scripts);
    console.log('样式文件:', resources.stylesheets);
    console.log('图片文件:', resources.images);
    console.log('字体文件:', resources.fonts);
    console.log('其他资源:', resources.other);
    
    return resources;
}

// 运行：getAllResources()

// ============================================
// 方法4：等待所有资源加载完成后获取HTML
// ============================================
function waitForCompleteLoad(callback) {
    var checkInterval = setInterval(function() {
        // 检查图片是否加载完成
        var images = document.querySelectorAll('img');
        var allImagesLoaded = true;
        for (var i = 0; i < images.length; i++) {
            if (!images[i].complete) {
                allImagesLoaded = false;
                break;
            }
        }
        
        // 检查是否有正在进行的AJAX请求（简单检查）
        // 注意：这只是一个简单的检查，实际可能需要更复杂的逻辑
        
        if (allImagesLoaded && document.readyState === 'complete') {
            clearInterval(checkInterval);
            console.log('✓ 页面加载完成');
            if (callback) callback();
        }
    }, 500);
    
    // 10秒后超时
    setTimeout(function() {
        clearInterval(checkInterval);
        console.log('⚠ 等待超时，但继续获取HTML');
        if (callback) callback();
    }, 10000);
}

// 使用示例：
// waitForCompleteLoad(function() {
//     var html = document.documentElement.outerHTML;
//     console.log('完整HTML长度:', html.length);
//     // 然后可以复制或下载
// });

// ============================================
// 方法5：获取页面所有文本内容（不含HTML标签）
// ============================================
function getTextContent() {
    var text = document.body.innerText || document.body.textContent;
    console.log('=== 页面文本内容 ===');
    console.log('文本长度:', text.length, '字符');
    console.log(text);
    return text;
}

// 运行：getTextContent()

// ============================================
// 快速使用指南
// ============================================
console.log(`
=== 快速使用指南 ===

1. 获取完整HTML：
   - 直接查看上面的输出，或运行：document.documentElement.outerHTML

2. 下载HTML文件：
   - 运行：downloadHTML()

3. 查看所有资源：
   - 运行：getAllResources()

4. 等待加载完成后获取：
   - 运行：waitForCompleteLoad(function() { console.log(document.documentElement.outerHTML); })

5. 获取纯文本：
   - 运行：getTextContent()
`);





