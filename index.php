<?php
// 获取基本系统信息
function getBasicSystemInfo() {
    return [
        'php_version' => PHP_VERSION,
        'php_os' => PHP_OS,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'server_name' => $_SERVER['SERVER_NAME'] ?? 'localhost',
        'memory_limit' => ini_get('memory_limit'),
        'memory_usage' => formatBytes(memory_get_usage(true)),
        'current_time' => date('Y-m-d H:i:s'),
        'client_ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ];
}

// 格式化字节数
function formatBytes($size) {
    $units = ['B', 'KB', 'MB', 'GB'];
    for ($i = 0; $size > 1024 && $i < 3; $i++) {
        $size /= 1024;
    }
    return round($size, 2) . ' ' . $units[$i];
}

$info = getBasicSystemInfo();

// 如果请求JSON格式
if (isset($_GET['format']) && $_GET['format'] === 'json') {
    header('Content-Type: application/json');
    echo json_encode($info, JSON_PRETTY_PRINT);
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PicFlow API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            color: #777;
        }
        .status {
            text-align: center;
            color: #28a745;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .api-link {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .api-link a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PicFlow API</h1>
        <div class="status">✅ 服务运行中</div>
        
        <div class="info-item">
            <span class="label">PHP 版本</span>
            <span class="value"><?php echo $info['php_version']; ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">操作系统</span>
            <span class="value"><?php echo $info['php_os']; ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">服务器软件</span>
            <span class="value"><?php echo $info['server_software']; ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">服务器名称</span>
            <span class="value"><?php echo $info['server_name']; ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">内存限制</span>
            <span class="value"><?php echo $info['memory_limit']; ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">内存使用</span>
            <span class="value"><?php echo $info['memory_usage']; ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">当前时间</span>
            <span class="value"><?php echo $info['current_time']; ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">客户端IP</span>
            <span class="value"><?php echo $info['client_ip']; ?></span>
        </div>
        
        

    </div>
</body>
</html>