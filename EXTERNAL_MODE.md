# PicFlow API v2 外链模式说明

## 概述

外链模式允许 PicFlow API 从外部链接文件（pc.txt 和 pe.txt）中随机返回图片链接，而不是从本地图片目录中获取图片。

## 使用方法

### 基本参数

在原有 API 参数基础上，添加 `external=true` 参数即可启用外链模式：

```
api_v2.php?external=true&type=pc&count=3
```

### 完整参数列表

| 参数 | 说明 | 默认值 | 示例 |
|------|------|--------|------|
| `external` | 启用外链模式 | false | `external=true` |
| `type` | 设备类型 | 自动检测 | `type=pc` 或 `type=pe` |
| `count` | 返回图片数量 | 1 | `count=5` |
| `format` | 返回格式 | json | `format=json` 或 `format=text` |
| `return` | 返回类型 | json | `return=redirect`（仅count=1时有效） |

### 外链文件格式

- **pc.txt**: PC端图片链接集合，一行一个链接
- **pe.txt**: 移动端图片链接集合，一行一个链接

文件示例：
```
https://example.com/image1.jpg
https://example.com/image2.png
https://example.com/image3.webp
```

## 功能特点

### 支持的功能
- ✅ 随机选择图片链接
- ✅ 指定返回数量
- ✅ JSON/文本格式返回
- ✅ 重定向模式（count=1时）
- ✅ 设备类型区分（pc/pe）

### 不支持的功能
- ❌ 智能格式转换（img_format参数无效）
- ❌ 图片格式优化
- ❌ 文件大小信息
- ❌ 本地路径信息

## API 响应示例

### JSON 格式响应
```json
{
    "success": true,
    "count": 2,
    "type": "pc",
    "mode": "random",
    "total_available": 10,
    "timestamp": 1640995200,
    "api_version": "2.0",
    "image_format": "auto",
    "return_type": "json",
    "external_mode": true,
    "user_agent": "Mozilla/5.0...",
    "images": [
        {
            "filename": "external_1",
            "path": "",
            "url": "https://example.com/image1.jpg",
            "extension": "external",
            "type": "pc",
            "size": 0,
            "external": true,
            "format": "external",
            "converted": false,
            "external_mode": true
        },
        {
            "filename": "external_5",
            "path": "",
            "url": "https://example.com/image5.png",
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

### 文本格式响应
```
https://example.com/image1.jpg
https://example.com/image5.png
```

## 使用示例

### 获取3张PC端外链图片
```bash
curl "http://your-domain.com/api_v2.php?external=true&type=pc&count=3"
```

### 获取1张移动端图片并重定向
```bash
curl -L "http://your-domain.com/api_v2.php?external=true&type=pe&count=1&return=redirect"
```

### 获取文本格式的链接列表
```bash
curl "http://your-domain.com/api_v2.php?external=true&type=pc&count=5&format=text"
```

## 错误处理

### 常见错误
1. **外链文件不存在**: 当 pc.txt 或 pe.txt 文件不存在时
2. **文件内容为空**: 当外链文件中没有有效链接时

### 错误响应示例
```json
{
    "success": false,
    "message": "外链文件不存在: pc.txt",
    "count": 0,
    "images": []
}
```

## 兼容性说明

外链模式与现有的本地模式完全兼容，可以通过 `external` 参数进行切换：

- `external=false` 或不设置：使用本地图片模式
- `external=true`：使用外链模式

## 注意事项

1. 外链模式不支持图片格式转换和优化
2. 无法获取外链图片的实际文件大小
3. 外链图片的可用性取决于外部服务器
4. 建议定期检查外链文件中链接的有效性