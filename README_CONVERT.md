# Windows 图片格式转换工具

这是一个简单的Windows批处理脚本，用于自动转换图片格式，支持将PC和PE目录中的图片转换为AVIF、WebP、JPEG等现代格式。

## 功能特性

- ✅ 支持多种输入格式：JPG、PNG、WebP、AVIF、GIF、BMP
- ✅ 输出现代格式：AVIF、WebP、JPEG
- ✅ 批量处理 PC 和 PE 目录
- ✅ 自动创建输出目录结构
- ✅ 显示转换进度和结果统计
- ✅ 生成转换报告

## 系统要求

- Windows 10/11
- ImageMagick (必须安装)

### 安装 ImageMagick

1. 访问 [ImageMagick 官网](https://imagemagick.org/script/download.php#windows)
2. 下载适合您系统的版本（推荐64-bit）
3. 安装时**必须**勾选 "Add application directory to your system path"
4. 重启命令提示符

## 使用方法

1. 将图片文件放入以下目录：
   - `images/pc/` - PC端图片
   - `images/pe/` - 移动端图片

2. 双击运行 `convert_images.bat`

3. 等待转换完成，结果保存在 `converted` 目录中

## 目录结构

转换前：
```
images/
├── pc/
│   ├── image1.jpg
│   └── image2.png
└── pe/
    ├── image3.jpg
    └── image4.png
```

转换后：
```
converted/
├── pc/
│   ├── avif/
│   │   ├── image1.avif
│   │   └── image2.avif
│   ├── webp/
│   │   ├── image1.webp
│   │   └── image2.webp
│   └── jpeg/
│       ├── image1.jpg
│       └── image2.jpg
└── pe/
    ├── avif/
    ├── webp/
    └── jpeg/
```

## 输出报告

转换完成后会生成 `converted/report.txt` 报告文件，包含：
- 转换统计信息
- 成功和失败的文件数量
- 生成时间

## 质量设置

脚本使用以下默认质量设置：
- **AVIF**: 85% (高压缩率，最小文件)
- **WebP**: 85% (平衡压缩率和质量)
- **JPEG**: 90% (高质量，兼容性最好)

## 故障排除

### Q: 提示 "ImageMagick 未安装"
**A:** 
1. 确保已安装 ImageMagick
2. 安装时必须勾选 "Add application directory to your system path"
3. 重启命令提示符后重试

### Q: 找不到图片文件
**A:** 
- 确保图片文件放在 `images\pc\` 或 `images\pe\` 目录中
- 支持的格式：JPG、JPEG、PNG、WebP、AVIF、GIF、BMP

### Q: 转换失败
**A:** 
- 检查磁盘空间是否充足
- 确保图片文件没有损坏
- 以管理员身份运行脚本

### Q: AVIF 转换慢
**A:** 
- AVIF 格式转换较慢是正常现象（高压缩率）
- 可以手动编辑脚本注释掉 AVIF 转换部分

## 性能说明

- **AVIF**: 转换慢，文件最小，现代浏览器支持
- **WebP**: 转换快，文件较小，广泛支持
- **JPEG**: 转换最快，兼容性最好，适合兜底

## 许可证

MIT License - 可自由使用和修改。