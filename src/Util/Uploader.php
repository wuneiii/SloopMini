<?php

namespace SloopMini\Util;


class Uploader {

    private $file;
    private $validExt;

    public function __construct() {
        $this->validExt = Config::getInstance()->get('validExt');
        if (!$this->validExt) {
            $this->validExt = array();
        }
    }

    public function setFile($file) {
        $this->file = $file;
    }

    public function setValidExt($ext) {
        if (is_string($ext)) {
            $ext = array($ext);
        }
        $this->validExt = $ext;
    }

    public function uploadToLocalDir($toDir, $desFileName = '') {
        if (empty($this->file)) {
            return false;
        }
        if (!file_exists($toDir)) {
            if (!mkdir($toDir, '0755', true)) {
                return false;
            }
        }
        $fileExt = self::getExt($this->file['name']);
        if ($this->validExt && !in_array($fileExt, $this->validExt)) {
            return false;
        }

        if (!$desFileName) {
            $desFileName = self::getRandFileName($fileExt);
        }

        if (!copy($this->file['tmp_name'], $toDir . $desFileName)) {
            return false;
        }
        return $desFileName;
    }

    public static function getRandFileName($ext) {
        return @date("Ymd_His_", time()) . rand() . '.' . $ext;
    }

    public static function getExt($fileName) {
        $ex = explode('.', $fileName);
        if (is_array($ex)) {
            return $ex[count($ex) - 1];

        }
        return $fileName;
    }
}
