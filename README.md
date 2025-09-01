# PicFlow API

🎮 **专业的随机图片处理服务** - 高效、简洁、智能的随机图片API

## 📋 项目简介

PicFlow API 是一个轻量级的随机图片服务，专为智能设计，支持多种现代图片格式转换，包括 WebP、AVIF、JPEG 等格式。项目采用 PHP 开发，部署简单，性能优异。

## ✨ 项目特性

### 🚀 核心功能
- **多格式支持**: 支持 WebP、AVIF、JPEG、PNG 等主流图片格式,支持不判断同设备兼容性提供不同格式的图片
- **双端适配**: 分别支持 PC 端和 PE 端图片处理,支持设备判断提供不同分辨率图片
- **批量转换**: 支持批量图片格式转换
- **实时监控**: 提供系统信息面板，实时监控服务状态

### 🎯 技术特点
- **轻量级**: 基于 PHP 开发，资源占用少
- **高性能**: 优化的图片处理算法
- **易部署**: 支持多种部署方式
- **跨平台**: 支持 Windows、Linux、macOS

### 📊 系统监控
- PHP 版本信息
- 服务器状态监控
- 内存使用情况
- 客户端访问信息
- JSON API 支持

## 🛠️ 部署指南

### 环境要求

- **PHP**: 7.4 或更高版本
- **Web服务器**: Apache、Nginx 或 IIS
- **系统**: Windows/Linux/macOS

### 快速部署

#### 方法一：Apache/Nginx 部署

1. **下载项目**
   ```bash
   git clone https://github.com/matsuzaka-yuki/PicFlow-API.git
   cd picflow-api
   ```

2. **配置Web服务器**

   **宝塔面板/1Panel**:  
   安装好PHP74+环境,进入面板添加站点,选择PHP74+环境,上传项目到网站根目录,配置完成后访问你的域名即可
      
   **Apache 配置** (`.htaccess`):
   ```apache
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ index.php [QSA,L]
   ```

   **Nginx 配置**:
   ```nginx
   server {
       listen 80;
       server_name your-domain.com;
       root /path/to/picflow-api;
       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           fastcgi_pass 127.0.0.1:9000;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
           include fastcgi_params;
       }
   }
   ```

3. **设置目录权限**
   ```bash
   chmod 755 images/ converted/
   chmod -R 777 converted/
   ```

4. **放置图片**
   你需要在 `converted/` 目录下放置不同设备和不同类型的格式图片，然后访问服务即可。如果你没有这类格式图片,请使用市面上的格式转换工具进行转换。
   你也可以在 `images/` 目录下放置原始图片，然后运行 `convert_images.bat` 进行转换。(Windows),如果不会使用请看[README_CONVERT.md](README_CONVERT.md)

5. **访问服务**
   ```
   http://your-domain.com/
   ```

#### 方法二：PHP 内置服务器（开发环境）

```bash
# 进入项目目录
cd picflow-api

# 启动内置服务器
php -S localhost:8000

# 访问服务
# http://localhost:8000
```




### 目录结构

```
picflow-api/
├── index.php              # 主入口文件
├── README.md              # 项目说明文档
├── images/                # 原始图片目录
│   ├── pc/               # PC端图片
│   └── pe/               # PE端图片
├── converted/             # 转换后图片目录
│   ├── pc/               # PC端转换图片
│   │   ├── webp/         # WebP格式
│   │   ├── avif/         # AVIF格式
│   │   └── jpeg/         # JPEG格式
│   └── pe/               # PE端转换图片
│       ├── webp/         # WebP格式
│       ├── avif/         # AVIF格式
│       └── jpeg/         # JPEG格式
└── .htaccess             # Apache重写规则
```

## 📖 使用说明

### API 接口

[API 文档](API.md)


## 🤝 贡献指南

欢迎提交 Issue 和 Pull Request！


## 📄 许可证

本项目采用 MIT 许可证 - 查看 [LICENSE](LICENSE) 文件了解详情

## 📞 联系方式

- **项目主页**: https://github.com/matsuzaka-yuki/PicFlow-API
- **邮箱**: 3231515355@qq.com

## 🙏 致谢

感谢所有为这个项目做出贡献的开发者！

---


**PicFlow API** - 让随机图片处理更简单 🚀
