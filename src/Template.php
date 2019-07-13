<?php
/**
 * Created by PhpStorm.
 * User: xiaolong
 * Date: 2019/7/13
 * Time: 下午10:32
 */

namespace SloopMini;


class Template {

    public static $instance;

    static public function getInstance() {
        if (null == self::$instance) {
            self::$instance = new Template();
        }
        return self::$instance;
    }


    private $tplData = array();

    private $tplRootPath = '';
    private $tplFileExt  = '.php';


    private function __construct() {
        $config = Config::getInstance();
        $this->tplRootPath = $config->get('template_path');
        $this->tplFileExt = $config->get('template_file_ext');
    }


    public function setRootPath($path) {
        if ($path) {
            Config::getInstance()->loadConfig(array('template_path' => $path));
            $this->tplRootPath = $path;
        }
    }


    public function assign($key, $value) {
        $this->tplData[$key] = $value;
    }

    public function assignArray(array $vars) {
        if (!is_array($vars)) return;
        foreach ($vars as $key => $value) {
            $this->assign($key, $value);
        }
    }

    public function getTplRealFile($tplName) {
        if (substr($this->tplRootPath, -1) != '/') {
            $this->tplRootPath .= "/";
        }

        return $this->tplRootPath . $tplName . "." . $this->tplFileExt;
    }


    public function includeTpl($tplName) {
        extract($this->tplData);
        $realTplFile = $this->getTplRealFile($tplName);
        include_once $realTplFile;
    }

    public function render($tplName) {

        $realTplFile = $this->getTplRealFile($tplName);
        $this->includeTpl($realTplFile);

    }
}