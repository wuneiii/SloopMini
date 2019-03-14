<?php

namespace SloopMini\Util;

class Request {

    private $method;
    private $host;
    private $remoteIp;
    private $refer;
    private $port;

    private $requestUrl;
    private $urlPath;
    private $urlParam;
    private $arrPost;
    private $arrGet;
    private $arrFile;

    private static $instance;

    public static function getInstance() {
        if (null == self::$instance) {
            self::$instance = new Request();
        }
        return self::$instance;
    }


    private function __construct() {
        $this->host = $_SERVER['HTTP_HOST'];
        $this->port = $_SERVER['SERVER_PORT'];
        $this->remoteIp = $this->getIp();
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->requestUrl = $_SERVER['REQUEST_URI'];

        $urlParse = parse_url($this->requestUrl);
        $this->urlPath = $urlParse['path'];
        if (isset($urlParse['query'])) {
            parse_str($urlParse['query'], $this->urlParam);
        }
        //todo::检查输入
        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->refer = $_SERVER['HTTP_REFERER'];
        }

        $this->arrPost = $this->checkInject($_POST);
        $this->arrGet = $this->checkInject($_GET);
        if (isset($_FILES)) {
            $this->arrFile = $_FILES;
        }
    }

    public function isMobile() {
        //正则表达式,批配不同手机浏览器UA关键词。

        $regex_match = "/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";

        $regex_match .= "htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";

        $regex_match .= "blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";

        $regex_match .= "symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";

        $regex_match .= "jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";

        $regex_match .= ")/i";


        return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
    }

    public function getParam($key) {
        if (isset($this->arrGet[$key])) {
            return $this->arrGet[$key];
        } else if (isset($this->arrPost[$key])) {
            return $this->arrPost[$key];
        }
        return null;
    }

    public function getInt($key) {
        return intval($this->getParam($key));
    }

    public function getString($key) {
        return strval($this->getParam($key));
    }


    public function getFile($key) {
        if (isset($this->arrFile[$key])) {
            return $this->arrFile[$key];
        }
        return null;
    }

    public function getPost() {
        return $this->arrPost;
    }

    public function getGet() {

        return $this->arrGet;
    }

    public function isPost() {
        return strtolower($this->method) == 'post';
    }

    /**
     * @return mixed
     */
    public function getRequestUrl() {
        return $this->requestUrl;
    }

    /**
     * @return mixed
     */
    public function getUrlPath() {
        return $this->urlPath;
    }

    /**
     * @return mixed
     */
    public function getUrlParam() {
        return $this->urlParam;
    }


    public function getRefer() {
        return $this->refer;
    }

    public function getHost() {
        return $this->host;
    }

    public function getMethod() {
        return $this->method;
    }


    private function checkInject($arr) {
        return $arr;
    }

    private function getIp() {
        $ip = null;
        // TODO ::检查输入， 这些自动都可以被手动设置s
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $ip = getenv("HTTP_X_FORWARDED_FOR");
            } elseif (getenv("HTTP_CLIENT_IP")) {
                $ip = getenv("HTTP_CLIENT_IP");
            } else {
                $ip = getenv("REMOTE_ADDR");
            }
        }
        return $ip;
    }

}