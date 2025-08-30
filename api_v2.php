<?php
// 内置配置 - 不依赖外部配置文件
define('BASE_DIR', __DIR__);
define('CONVERTED_IMAGES_DIR', BASE_DIR . '/converted/');
define('SITE_URL', 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']));
define('DEFAULT_IMAGE_COUNT', 1);
define('MAX_IMAGES_PER_REQUEST', 50);
define('CURRENT_IMAGE_MODE', 'random');
define('API_VERSION', '2.0');

// 内置访问权限检查函数
function checkAccess() {
    // 简化的访问检查，可根据需要自定义
    return true;
}

// 检查访问权限
checkAccess();

header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// 设备检测函数（参考api.php）
function isMobile(){
    $useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock=preg_match('|\\(.*?\\)|',$useragent,$matches)>0?$matches[0]:'';
    function CheckSubstrs($substrs,$text){
        foreach($substrs as $substr)
        if(false!==strpos($text,$substr)){
            return true;
        }
        return false;
    }
    $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
    $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');
    $found_mobile=CheckSubstrs($mobile_os_list,$useragent_commentsblock) ||
    CheckSubstrs($mobile_token_list,$useragent);
    if ($found_mobile){
        return true;
    }else{
        return false;
    }
}

// 智能格式检测函数
function detectOptimalFormat($userAgent = '') {
    if (empty($userAgent)) {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }
    
    // 检测是否支持AVIF (最新格式，最小文件)
    if (strpos($userAgent, 'Chrome/') !== false) {
        preg_match('/Chrome\/(\d+)/', $userAgent, $matches);
        if (isset($matches[1]) && intval($matches[1]) >= 85) {
            return 'avif';
        }
    }
    
    // 检测Firefox对AVIF的支持
    if (strpos($userAgent, 'Firefox/') !== false) {
        preg_match('/Firefox\/(\d+)/', $userAgent, $matches);
        if (isset($matches[1]) && intval($matches[1]) >= 93) {
            return 'avif';
        }
    }
    
    // 检测是否支持WebP (广泛支持)
    if (strpos($userAgent, 'Chrome') !== false || 
        strpos($userAgent, 'Opera') !== false || 
        strpos($userAgent, 'Edge') !== false ||
        strpos($userAgent, 'Firefox') !== false ||
        (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Version/14') !== false)) {
        return 'webp';
    }
    
    // 默认返回JPEG (兼容性最好)
    return 'jpeg';
}

// 获取转换后的图片URL
function getConvertedImageUrl($originalImage, $targetFormat) {
    $filename = pathinfo($originalImage['filename'], PATHINFO_FILENAME);
    $deviceType = $originalImage['type'];
    
    // 构建转换后的文件路径
    $convertedPath = CONVERTED_IMAGES_DIR . $deviceType . '/' . $targetFormat . '/' . $filename . '.' . $targetFormat;
    $convertedUrl = SITE_URL . '/converted/' . $deviceType . '/' . $targetFormat . '/' . $filename . '.' . $targetFormat;
    
    // 检查转换后的文件是否存在
    if (file_exists($convertedPath)) {
        return [
            'url' => $convertedUrl,
            'path' => $convertedPath,
            'format' => $targetFormat,
            'converted' => true,
            'size' => filesize($convertedPath)
        ];
    }
    
    // 如果转换后的文件不存在，返回原始文件
    return [
        'url' => $originalImage['url'],
        'path' => isset($originalImage['path']) ? $originalImage['path'] : '',
        'format' => $originalImage['extension'],
        'converted' => false,
        'size' => isset($originalImage['size']) ? $originalImage['size'] : 0
    ];
}

// 获取参数
$count = isset($_GET['count']) ? intval($_GET['count']) : DEFAULT_IMAGE_COUNT;
$format = isset($_GET['format']) ? $_GET['format'] : 'json';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$imageFormat = isset($_GET['img_format']) ? $_GET['img_format'] : 'auto';
$returnType = isset($_GET['return']) ? $_GET['return'] : 'json'; // 新增返回类型参数
$external = isset($_GET['external']) ? $_GET['external'] === 'true' || $_GET['external'] === '1' : false; // 外链模式参数

// 如果没有指定type参数，则自动检测设备类型
if (empty($type)) {
    $type = isMobile() ? 'pe' : 'pc';
}

// 参数验证
$count = max(1, min(MAX_IMAGES_PER_REQUEST, $count));

// 内置图片获取函数
function getImages($type, $count, $external = false) {
    // 外链模式
    if ($external) {
        return getExternalImages($type, $count);
    }
    
    // 本地模式
    $imageDir = BASE_DIR . '/images/' . $type;
    
    if (!is_dir($imageDir)) {
        return [
            'success' => false,
            'message' => '图片目录不存在: ' . $type,
            'images' => [],
            'total_available' => 0
        ];
    }
    
    // 获取所有图片文件
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
    $images = [];
    
    $files = scandir($imageDir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $filePath = $imageDir . '/' . $file;
        if (is_file($filePath)) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($extension, $allowedExtensions)) {
                $images[] = [
                    'filename' => $file,
                    'path' => $filePath,
                    'url' => SITE_URL . '/images/' . $type . '/' . $file,
                    'extension' => $extension,
                    'type' => $type,
                    'size' => filesize($filePath)
                ];
            }
        }
    }
    
    if (empty($images)) {
        return [
            'success' => false,
            'message' => '没有找到可用的图片',
            'images' => [],
            'total_available' => 0
        ];
    }
    
    // 随机选择图片
    shuffle($images);
    $selectedImages = array_slice($images, 0, $count);
    
    return [
        'success' => true,
        'images' => $selectedImages,
        'total_available' => count($images)
    ];
}

// 外链模式图片获取函数
function getExternalImages($type, $count) {
    $linkFile = BASE_DIR . '/' . $type . '.txt';
    
    if (!file_exists($linkFile)) {
        return [
            'success' => false,
            'message' => '外链文件不存在: ' . $type . '.txt',
            'images' => [],
            'total_available' => 0
        ];
    }
    
    // 读取链接文件
    $links = file($linkFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $images = [];
    
    foreach ($links as $index => $link) {
        $link = trim($link);
        if (empty($link)) continue;
        
        // 生成基于索引的文件名
        $fileName = 'external_' . ($index + 1);
        
        $images[] = [
            'filename' => $fileName,
            'path' => '', // 外链模式没有本地路径
            'url' => $link,
            'extension' => 'external', // 标记为外链
            'type' => $type,
            'size' => 0, // 外链模式无法获取文件大小
            'external' => true
        ];
    }
    
    if (empty($images)) {
        return [
            'success' => false,
            'message' => '外链文件中没有有效的链接',
            'images' => [],
            'total_available' => 0
        ];
    }
    
    // 随机选择图片
    shuffle($images);
    $selectedImages = array_slice($images, 0, $count);
    
    return [
        'success' => true,
        'images' => $selectedImages,
        'total_available' => count($images)
    ];
}

// 使用内置函数获取图片
$result = getImages($type, $count, $external);

if (!$result['success']) {
    $response = [
        'success' => false,
        'message' => $result['message'],
        'count' => 0,
        'images' => []
    ];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

$selectedImages = $result['images'];
$totalImages = $result['total_available'];

// 如果只要一张图片且返回类型是重定向，直接重定向
if ($count == 1 && $returnType === 'redirect') {
    $image = $selectedImages[0];
    
    // 智能格式处理
    if ($imageFormat === 'auto') {
        $optimalFormat = detectOptimalFormat();
        $convertedImage = getConvertedImageUrl($image, $optimalFormat);
        $redirectUrl = $convertedImage['url'];
    } elseif ($imageFormat !== 'original' && in_array($imageFormat, ['jpeg', 'webp', 'avif'])) {
        $convertedImage = getConvertedImageUrl($image, $imageFormat);
        $redirectUrl = $convertedImage['url'];
    } else {
        $redirectUrl = $image['url'];
    }
    
    // 清除之前的header并重定向
    header_remove('Content-type');
    header_remove('Access-Control-Allow-Origin');
    header_remove('Access-Control-Allow-Methods');
    header_remove('Access-Control-Allow-Headers');
    
    header('Location: ' . $redirectUrl, true, 302);
    exit;
}

// 智能格式处理（外链模式不支持格式转换）
if (!$external) {
    if ($imageFormat === 'auto') {
        $optimalFormat = detectOptimalFormat();
        
        // 为每个图片应用智能格式
        foreach ($selectedImages as &$image) {
            $convertedImage = getConvertedImageUrl($image, $optimalFormat);
            $image['url'] = $convertedImage['url'];
            $image['format'] = $convertedImage['format'];
            $image['converted'] = $convertedImage['converted'];
            $image['optimal_format'] = $optimalFormat;
            if ($convertedImage['size'] > 0) {
                $image['size'] = $convertedImage['size'];
            }
        }
        unset($image); // 清除引用
    } elseif ($imageFormat !== 'original' && in_array($imageFormat, ['jpeg', 'webp', 'avif'])) {
        // 指定格式处理
        foreach ($selectedImages as &$image) {
            $convertedImage = getConvertedImageUrl($image, $imageFormat);
            $image['url'] = $convertedImage['url'];
            $image['format'] = $convertedImage['format'];
            $image['converted'] = $convertedImage['converted'];
            $image['requested_format'] = $imageFormat;
            if ($convertedImage['size'] > 0) {
                $image['size'] = $convertedImage['size'];
            }
        }
        unset($image); // 清除引用
    }
} else {
    // 外链模式：为每个图片添加外链标记
    foreach ($selectedImages as &$image) {
        $image['format'] = 'external';
        $image['converted'] = false;
        $image['external_mode'] = true;
    }
    unset($image); // 清除引用
}

// 根据格式返回数据
if ($format === 'text' || $format === 'url') {
    header('Content-Type: text/plain; charset=utf-8');
    foreach ($selectedImages as $image) {
        echo $image['url'] . "\n";
    }
} else {
    // JSON格式 (默认)
    $response = [
        'success' => true,
        'count' => count($selectedImages),
        'type' => $type,
        'mode' => CURRENT_IMAGE_MODE,
        'total_available' => $totalImages,
        'timestamp' => time(),
        'api_version' => API_VERSION,
        'image_format' => $imageFormat,
        'return_type' => $returnType,
        'external_mode' => $external,
        'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
        'images' => $selectedImages
    ];
    
    if ($imageFormat === 'auto') {
        $response['detected_format'] = detectOptimalFormat();
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>