# PicFlow API v2.0 文档

PicFlow API v2.0 是一个智能图片服务API，支持多种图片格式转换、设备自适应和外链模式。

## 基本信息

- **API版本**: 2.0
- **请求方式**: GET
- **响应格式**: JSON (默认) / TEXT / 重定向
- **字符编码**: UTF-8

## 核心特性

- 🎯 **智能格式检测**: 根据用户浏览器自动选择最优图片格式 (AVIF/WebP/JPEG)
- 📱 **设备自适应**: 自动检测移动端/桌面端，返回对应尺寸图片
- 🔄 **多格式支持**: 支持 JPEG、WebP、AVIF 等现代图片格式
- 🌐 **外链模式**: 支持从外部链接获取图片
- ⚡ **高性能**: 只处理转换后的优化图片，提升响应速度

## 请求地址

```
GET /api_v2.php
```

## 请求参数

### 基础参数

| 参数名 | 类型 | 默认值 | 说明 |
|--------|------|--------|------|
| `count` | int | 1 | 返回图片数量，范围: 1-50 |
| `type` | string | auto | 设备类型: `pc`(桌面端) / `pe`(移动端) / auto(自动检测) |
| `format` | string | json | 响应格式: `json` / `text` / `url` |
| `return` | string | json | 返回类型: `json` / `redirect`(直接重定向到图片) |

### 图片格式参数

| 参数名 | 类型 | 默认值 | 说明 |
|--------|------|--------|------|
| `img_format` | string | auto | 图片格式: `auto`(智能选择) / `jpeg` / `webp` / `avif` |

### 外链模式参数

| 参数名 | 类型 | 默认值 | 说明 |
|--------|------|--------|------|
| `external` | boolean | false | 外链模式: `true` / `false` / `1` / `0` |

## 使用模式

### 1. 本地模式 (默认)

本地模式从服务器的 `converted` 目录获取已转换的优化图片。

#### 目录结构
```
converted/
├── pc/          # 桌面端图片
│   ├── jpeg/    # JPEG格式
│   ├── webp/    # WebP格式
│   └── avif/    # AVIF格式
└── pe/          # 移动端图片
    ├── jpeg/    # JPEG格式
    ├── webp/    # WebP格式
    └── avif/    # AVIF格式
```

#### 请求示例

```bash
# 获取1张自动格式的图片
GET /api_v2.php

# 获取5张桌面端WebP格式图片
GET /api_v2.php?count=5&type=pc&img_format=webp

# 获取移动端图片并直接重定向
GET /api_v2.php?type=pe&return=redirect

# 获取AVIF格式图片
GET /api_v2.php?img_format=avif&count=3
```

### 2. 外链模式

外链模式从预配置的外部链接获取图片，适用于CDN或第三方图片服务。

#### 配置文件
- `pc.txt`: 桌面端图片链接列表
- `pe.txt`: 移动端图片链接列表

每行一个图片URL，支持任何可访问的图片链接。

#### 请求示例

```bash
# 启用外链模式获取图片
GET /api_v2.php?external=true

# 外链模式获取5张移动端图片
GET /api_v2.php?external=true&type=pe&count=5

# 外链模式直接重定向
GET /api_v2.php?external=1&return=redirect
```

## 响应格式

### JSON响应 (默认)

```json
{
  "success": true,
  "count": 2,
  "type": "pc",
  "mode": "random",
  "total_available": 150,
  "timestamp": 1640995200,
  "api_version": "2.0",
  "image_format": "auto",
  "return_type": "json",
  "external_mode": false,
  "detected_format": "webp",
  "user_agent": "Mozilla/5.0...",
  "images": [
    {
      "filename": "image1.webp",
      "path": "/path/to/converted/pc/webp/image1.webp",
      "url": "https://example.com/converted/pc/webp/image1.webp",
      "extension": "webp",
      "type": "pc",
      "size": 45678,
      "source": "converted",
      "format": "webp"
    },
    {
      "filename": "image2.webp",
      "path": "/path/to/converted/pc/webp/image2.webp",
      "url": "https://example.com/converted/pc/webp/image2.webp",
      "extension": "webp",
      "type": "pc",
      "size": 52341,
      "source": "converted",
      "format": "webp"
    }
  ]
}
```

### 外链模式响应

```json
{
  "success": true,
  "count": 1,
  "type": "pc",
  "external_mode": true,
  "images": [
    {
      "filename": "external_1",
      "path": "",
      "url": "https://cdn.example.com/image1.jpg",
      "extension": "external",
      "type": "pc",
      "size": 0,
      "external": true,
      "format": "external",
      "converted": false,
      "external_mode": true
    }
  ]
}
```

### TEXT响应

当 `format=text` 或 `format=url` 时，返回纯文本格式：

```
https://example.com/converted/pc/webp/image1.webp
https://example.com/converted/pc/webp/image2.webp
```

### 重定向响应

当 `return=redirect` 且 `count=1` 时，直接重定向到图片URL：

```
HTTP/1.1 302 Found
Location: https://example.com/converted/pc/webp/image1.webp
```

## 智能格式检测

API会根据用户的浏览器User-Agent自动检测支持的最优图片格式：

### 支持的格式优先级

1. **AVIF** - 最新格式，文件最小
   - Chrome 85+
   - Firefox 93+

2. **WebP** - 广泛支持，优秀压缩
   - Chrome (所有版本)
   - Firefox
   - Edge
   - Safari 14+

3. **JPEG** - 兜底格式，最佳兼容性
   - 所有浏览器

### 格式检测示例

```bash
# 自动检测最优格式
GET /api_v2.php?img_format=auto

# 强制指定格式
GET /api_v2.php?img_format=webp
```

## 错误处理

### 错误响应格式

```json
{
  "success": false,
  "message": "错误描述",
  "count": 0,
  "images": []
}
```

### 常见错误

| 错误信息 | 原因 | 解决方案 |
|----------|------|----------|
| `没有找到转换后的图片，请检查 converted 目录` | converted目录为空或不存在 | 确保converted目录存在且包含图片文件 |
| `没有找到 webp 格式的图片` | 指定格式的图片不存在 | 检查对应格式目录是否有图片 |
| `外链文件不存在: pc.txt` | 外链模式配置文件缺失 | 创建对应的.txt配置文件 |
| `外链文件中没有有效的链接` | 配置文件为空或格式错误 | 检查.txt文件内容格式 |

## 性能优化建议

### 1. 图片预处理
- 提前将图片转换为多种格式 (JPEG/WebP/AVIF)
- 按设备类型优化图片尺寸
- 使用适当的压缩质量

### 2. 缓存策略
- 启用浏览器缓存
- 使用CDN加速图片分发
- 考虑服务端缓存API响应

### 3. 外链模式优化
- 使用高性能的图片CDN
- 定期检查外链有效性
- 合理配置链接数量

## 使用示例

### JavaScript调用

```javascript
// 获取随机图片
fetch('/api_v2.php?count=5&img_format=webp')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      data.images.forEach(img => {
        console.log(img.url);
      });
    }
  });

// 直接使用图片URL
const imgUrl = '/api_v2.php?return=redirect&img_format=auto';
document.getElementById('myImg').src = imgUrl;
```

### PHP调用

```php
// 获取API数据
$response = file_get_contents('http://yoursite.com/api_v2.php?count=3&type=pc');
$data = json_decode($response, true);

if ($data['success']) {
    foreach ($data['images'] as $image) {
        echo '<img src="' . $image['url'] . '" alt="' . $image['filename'] . '">';
    }
}
```

### HTML直接使用

```html
<!-- 直接重定向到图片 -->
<img src="/api_v2.php?return=redirect&img_format=webp" alt="Random Image">

<!-- 背景图片 -->
<div style="background-image: url('/api_v2.php?return=redirect&type=pc')"></div>
```

## 版本更新日志

### v2.0
- ✨ 新增智能格式检测功能
- ✨ 支持AVIF格式
- ✨ 优化目录扫描逻辑，只处理converted目录
- ✨ 改进外链模式支持
- 🔧 移除original参数，专注转换后图片
- 🔧 优化错误提示信息
- 🔧 改进性能和响应速度

### v1.x
- 基础图片API功能
- 支持JPEG和WebP格式
- 基本的设备检测

## 技术支持

如有问题或建议，请联系技术支持团队。

---

**PicFlow API v2.0** - 让图片服务更智能、更高效！