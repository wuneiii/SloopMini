<?php

namespace SloopMini\Util;


class ImageCut {

    /**
     *
     * 生成缩略图
     * @param $filePath
     * @param $outputPath
     * @param $minEdge
     * @return bool
     */
    public static function genThumbnail($filePath, $outputPath, $minEdge) {

        if (!file_exists($filePath)) {
            return false;
        }
        $src = imagecreatefromstring(file_get_contents($filePath));
        $width = imagesx($src);
        $height = imagesy($src);

        if (max($width, $height) <= $minEdge) {
            if ($filePath == $outputPath) {
                return true;
            }
            imagejpeg($src, $outputPath);
            imagedestroy($src);
            return true;
        }


        if ($width > $height) {
            $newHeight = $minEdge;
            $newWidth = ($width * $newHeight) / $height;
        } else {
            $newWidth = $minEdge;
            $newHeight = ($height * $newWidth) / $width;
        }
        $thumb = imagecreatetruecolor($newWidth, $newHeight);

        imagecopyresampled($thumb, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        imagejpeg($thumb, $outputPath);
        imagedestroy($thumb);
        imagedestroy($src);
        return true;


    }


    /**
     *
     * 从图片中截取出来一个最大的方块
     * @param $filePath
     * @param $outputPath
     * @param $squareSize
     * @return bool
     */
    public static function cutMaxSquare($filePath, $outputPath, $targetSize = 0) {

        if (!file_exists($filePath)) {
            return false;
        }
        $src = imagecreatefromstring(file_get_contents($filePath));
        $width = imagesx($src);
        $height = imagesy($src);
        $size = min($width, $height);

        if ($width > $height) {
            $y = 0;
            $x = ($width - $height) / 2;
        } else {
            $x = 0;
            $y = ($height - $width) / 2;
        }


        // 如果$squareSize 不设置，则截取图片最大的方框
        // 如果设置了这个值，截图范围最大就这么大
        if (!$targetSize) {
            $targetSize = $size;
        }

        $des = imagecreatetruecolor($targetSize, $targetSize);
        imagecopyresized($des, $src, 0, 0, $x, $y, $targetSize, $targetSize, $size, $size);


        if ($des == false) {
            return false;
        }

        imagejpeg($des, $outputPath);
        imagedestroy($des);
        imagedestroy($src);
        return true;
    }

}