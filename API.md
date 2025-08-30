# PicFlow API文档 (API v2.0)

## 概述

这是一个独立的PHP图片API，用于随机获取游戏图片。支持PC端和移动端图片，具备智能格式检测和图片转换功能。

## 基本信息

- **API版本**: 2.0
- **协议**: HTTP/HTTPS
- **响应格式**: JSON (默认) / Text / URL重定向
- **请求方法**: GET
- **字符编码**: UTF-8

## 接口地址

```
GET /api_v2.php
```



## 请求参数(本地模式),如果需要使用外链方案请看EXTERNAL_MODE.md

| 参数名 | 类型 | 必填 | 默认值 | 说明 |
|--------|------|------|--------|------|
| `count` | int | 否 | 1 | 返回图片数量，范围：1-50 |
| `type` | string | 否 | 自动检测 | 设备类型：`pc` 或 `pe` |
| `format` | string | 否 | json | 返回格式：`json`、`text`、`url` |
| `img_format` | string | 否 | auto | 图片格式：`auto`、`original`、`jpeg`、`webp`、`avif` |
| `return` | string | 否 | json | 返回类型：`json` 或 `redirect` |

### 参数详细说明

#### `count` - 图片数量
- 最小值：1
- 最大值：50
- 超出范围会自动调整到边界值

#### `type` - 设备类型
- `pc`: PC端图片（横屏）
- `pe`: 移动端图片（竖屏）
- 不指定时会根据User-Agent自动检测

#### `format` - 返回格式
- `json`: 返回JSON格式数据（默认）
- `text`: 返回纯文本URL列表
- `url`: 同text格式

#### `img_format` - 图片格式
- `auto`: 智能检测最优格式（推荐）
- `original`: 返回原始格式
- `jpeg`: 强制JPEG格式
- `webp`: 强制WebP格式
- `avif`: 强制AVIF格式

#### `return` - 返回类型
- `json`: 返回JSON数据
- `redirect`: 直接重定向到图片（仅当count=1时有效）

## 响应格式

### JSON响应 (默认)

```json
{
    "success": true,
    "count": 1,
    "type": "pc",
    "mode": "random",
    "total_available": 150,
    "timestamp": 1640995200,
    "api_version": "2.0",
    "image_format": "auto",
    "return_type": "json",
    "detected_format": "webp",
    "user_agent": "Mozilla/5.0...",
    "images": [
        {
            "filename": "game_image_001.jpg",
            "path": "/path/to/images/pc/game_image_001.jpg",
            "url": "https://example.com/images/pc/game_image_001.jpg",
            "extension": "jpg",
            "type": "pc",
            "size": 245760,
            "format": "webp",
            "converted": true,
            "optimal_format": "webp"
        }
    ]
}
```

### Text/URL响应

```
https://example.com/images/pc/game_image_001.jpg
https://example.com/images/pc/game_image_002.jpg
```

### 错误响应

```json
{
    "success": false,
    "message": "没有找到可用的图片",
    "count": 0,
    "images": []
}
```

## 智能格式检测

API会根据客户端User-Agent自动选择最优图片格式：

### AVIF格式支持
- Chrome 85+
- Firefox 93+

### WebP格式支持
- Chrome (所有版本)
- Opera (所有版本)
- Edge (所有版本)
- Firefox (所有版本)
- Safari 14+

### 默认格式
- 不支持现代格式的浏览器返回JPEG

## 使用示例

### 1. 获取单张PC图片（JSON格式）
```bash
curl "https://example.com/api_v2.php?type=pc&count=1"
```

### 2. 获取多张移动端图片
```bash
curl "https://example.com/api_v2.php?type=pe&count=5"
```

### 3. 直接重定向到图片
```bash
curl -L "https://example.com/api_v2.php?count=1&return=redirect"
```

### 4. 获取纯文本URL列表
```bash
curl "https://example.com/api_v2.php?count=3&format=text"
```

### 5. 强制指定图片格式
```bash
curl "https://example.com/api_v2.php?img_format=webp&count=1"
```

### 6. 自动检测设备类型和最优格式
```bash
curl "https://example.com/api_v2.php?count=1&img_format=auto"
```

## JavaScript调用示例

### 基础调用
```javascript
fetch('/api_v2.php?count=1&type=pc')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('图片URL:', data.images[0].url);
        }
    });
```

### 使用async/await
```javascript
async function getRandomImage() {
    try {
        const response = await fetch('/api_v2.php?count=1&img_format=auto');
        const data = await response.json();
        
        if (data.success) {
            return data.images[0].url;
        }
    } catch (error) {
        console.error('获取图片失败:', error);
    }
}
```

### jQuery示例
```javascript
$.getJSON('/api_v2.php', {
    count: 1,
    type: 'pc',
    img_format: 'auto'
}, function(data) {
    if (data.success) {
        $('#image').attr('src', data.images[0].url);
    }
});
```

## 目录结构要求

API需要以下目录结构：

```
项目根目录/
├── api_v2.php          # API文件
├── images/             # 原始图片目录
│   ├── pc/            # PC端图片
│   └── pe/            # 移动端图片
└── converted/         # 转换后图片目录（可选）
    ├── pc/
    │   ├── jpeg/
    │   ├── webp/
    │   └── avif/
    └── pe/
        ├── jpeg/
        ├── webp/
        └── avif/
```

## 支持的图片格式

### 输入格式（原始图片）
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)
- WebP (.webp)
- AVIF (.avif)

### 输出格式（转换后）
- JPEG - 兼容性最好
- WebP - 体积小，广泛支持
- AVIF - 最新格式，体积最小

## 性能优化建议

1. **使用CDN**: 将图片文件部署到CDN以提高访问速度
2. **启用缓存**: 在Web服务器层面启用静态文件缓存
3. **图片预处理**: 预先转换图片到不同格式以减少实时处理
4. **压缩优化**: 对原始图片进行适当压缩

## 错误代码

| 错误信息 | 说明 | 解决方案 |
|----------|------|----------|
| "图片目录不存在" | 指定类型的图片目录不存在 | 检查images/pc或images/pe目录 |
| "没有找到可用的图片" | 目录中没有支持的图片文件 | 添加图片文件到对应目录 |

## 安全注意事项

1. **路径遍历防护**: API已内置路径安全检查
2. **文件类型验证**: 只允许指定的图片格式
3. **请求频率限制**: 建议在Web服务器层面设置请求频率限制
4. **CORS配置**: 已配置允许跨域访问

## 更新日志

### v2.0
- 移除外部配置文件依赖
- 添加智能格式检测
- 支持AVIF格式
- 优化移动端检测
- 添加重定向模式

## 技术支持

如有问题或建议，请检查：
1. 目录结构是否正确
2. 图片文件是否存在
3. 文件权限是否正确
4. PHP版本是否兼容（推荐PHP 7.4+）