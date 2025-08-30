@echo off
setlocal enabledelayedexpansion

title 简单图片转换工具

echo.
echo ========================================
echo          简单图片转换工具
echo ========================================
echo.

REM 检查 ImageMagick 是否安装
magick -version >nul 2>&1
if %errorlevel% neq 0 (
    echo 错误: ImageMagick 未安装或不在 PATH 中
    echo 请从以下地址下载安装 ImageMagick:
    echo https://imagemagick.org/script/download.php#windows
    echo.
    pause
    exit /b 1
)

echo ImageMagick 已安装，开始转换...
echo.

REM 创建输出目录
if not exist "converted" mkdir "converted"
if not exist "converted\pc" mkdir "converted\pc"
if not exist "converted\pe" mkdir "converted\pe"
if not exist "converted\pc\avif" mkdir "converted\pc\avif"
if not exist "converted\pc\webp" mkdir "converted\pc\webp"
if not exist "converted\pc\jpeg" mkdir "converted\pc\jpeg"
if not exist "converted\pe\avif" mkdir "converted\pe\avif"
if not exist "converted\pe\webp" mkdir "converted\pe\webp"
if not exist "converted\pe\jpeg" mkdir "converted\pe\jpeg"

set /a total_converted=0
set /a total_errors=0

REM 处理 PC 目录
echo 处理 PC 目录...
if exist "images\pc" (
    for %%f in ("images\pc\*.jpg" "images\pc\*.jpeg" "images\pc\*.png" "images\pc\*.webp" "images\pc\*.avif") do (
        if exist "%%f" (
            echo   转换: %%~nxf
            
            REM 转换为 AVIF
            magick "%%f" -quality 85 "converted\pc\avif\%%~nf.avif" >nul 2>&1
            if !errorlevel! equ 0 (
                echo     ? AVIF 完成
                set /a total_converted+=1
            ) else (
                echo     ? AVIF 失败
                set /a total_errors+=1
            )
            
            REM 转换为 WebP
            magick "%%f" -quality 85 "converted\pc\webp\%%~nf.webp" >nul 2>&1
            if !errorlevel! equ 0 (
                echo     ? WebP 完成
                set /a total_converted+=1
            ) else (
                echo     ? WebP 失败
                set /a total_errors+=1
            )
            
            REM 转换为 JPEG
            magick "%%f" -quality 90 -strip "converted\pc\jpeg\%%~nf.jpg" >nul 2>&1
            if !errorlevel! equ 0 (
                echo     ? JPEG 完成
                set /a total_converted+=1
            ) else (
                echo     ? JPEG 失败
                set /a total_errors+=1
            )
        )
    )
) else (
    echo   PC 目录不存在，跳过
)

echo.

REM 处理 PE 目录
echo 处理 PE 目录...
if exist "images\pe" (
    for %%f in ("images\pe\*.jpg" "images\pe\*.jpeg" "images\pe\*.png" "images\pe\*.webp" "images\pe\*.avif") do (
        if exist "%%f" (
            echo   转换: %%~nxf
            
            REM 转换为 AVIF
            magick "%%f" -quality 85 "converted\pe\avif\%%~nf.avif" >nul 2>&1
            if !errorlevel! equ 0 (
                echo     ? AVIF 完成
                set /a total_converted+=1
            ) else (
                echo     ? AVIF 失败
                set /a total_errors+=1
            )
            
            REM 转换为 WebP
            magick "%%f" -quality 85 "converted\pe\webp\%%~nf.webp" >nul 2>&1
            if !errorlevel! equ 0 (
                echo     ? WebP 完成
                set /a total_converted+=1
            ) else (
                echo     ? WebP 失败
                set /a total_errors+=1
            )
            
            REM 转换为 JPEG
            magick "%%f" -quality 90 -strip "converted\pe\jpeg\%%~nf.jpg" >nul 2>&1
            if !errorlevel! equ 0 (
                echo     ? JPEG 完成
                set /a total_converted+=1
            ) else (
                echo     ? JPEG 失败
                set /a total_errors+=1
            )
        )
    )
) else (
    echo   PE 目录不存在，跳过
)

echo.
echo ========================================
echo 转换完成！
echo 成功转换: !total_converted! 个文件
echo 转换失败: !total_errors! 个文件
echo 输出目录: converted
echo ========================================
echo.

REM 生成简单报告
echo 图片转换报告 > "converted\report.txt"
echo 生成时间: %date% %time% >> "converted\report.txt"
echo ========================== >> "converted\report.txt"
echo 成功转换: !total_converted! 个文件 >> "converted\report.txt"
echo 转换失败: !total_errors! 个文件 >> "converted\report.txt"
echo 输出目录: converted >> "converted\report.txt"

echo 报告已保存到: converted\report.txt
echo.

pause