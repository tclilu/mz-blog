<?php
/**
 * Created by PhpStorm.
 * User: LiLu
 * Date: 2018/9/2
 * Time: 6:34
 */
header("Content-type: image/png");
$img = @imagecreate(500, 500) or die("Cannot Initialize new GD image stream");
// 背景白色透明
$background_color = imagecolorallocatealpha($img, 255, 255, 255, 127);
// 线条白色
$line_color = imagecolorallocate($img,255,255,255);
imageline($img,250,0,437.5,(sqrt(3)*187.5)/3,$line_color);
imageline($img,375,0,437.5,(sqrt(3)*62.5)/3,$line_color);
imageline($img,250,500,62.5,500-(sqrt(3)*187.5)/3,$line_color);
imageline($img,125,500,62.5,500-(sqrt(3)*62.5)/3,$line_color);
// 绘制  李  字
// 木
// 横
imageline($img,125,125,375,125,$line_color);
// 竖
imageline($img,250,62.5,250,250,$line_color);
// 丿
imageline($img,250,125,140,235,$line_color);
// NA
imageline($img,250,125,360,235,$line_color);
// 子
// 上横
imageline($img,250,255,375,255,$line_color);
// 左斜
imageline($img,375,255,287,343.75,$line_color);
// 右斜
imageline($img,287,343.75,375,431,$line_color);
// 下横
imageline($img,250,431,375,431,$line_color);
// 中横
imageline($img,210,343.75,387.5,343.75,$line_color);
imagepng($img);
imagedestroy($img);