<?php

namespace SloopMini\Util\Http;

class CurlHttp {

    private $_header = array();
    private $_lastUrl;
    private $_timeout;
    private $_cookieFile;

    public function __construct($cookieFile = '') {
        $this->addHeader('User-Agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11');
        $this->setTimeout(5);
        if ($cookieFile) {
            if (!file_exists($cookieFile)) {
                @mkdir(dirname($cookieFile));
                @touch($cookieFile);
            }
            if (file_exists($cookieFile)) $this->_cookieFile = $cookieFile;
        }
    }

    public function __destruct() {
        if ($this->_cookieFile) {
            @unlink($this->_cookieFile);
        }
    }

    public function addHeader($key, $value) {
        $this->_header[] = $key . ': ' . $value;
    }

    public function setTimeout($t) {
        $this->_timeout = $t;
    }

    public function sendRequest($url) {
        $this->_lastUrl = $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $urlArr = parse_url($url);
        if ($urlArr['scheme'] == 'https') {
            //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        }
        if ($this->_cookieFile) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, realpath($this->_cookieFile));
            curl_setopt($ch, CURLOPT_COOKIEFILE, realpath($this->_cookieFile));
        }

        $httpContent = curl_exec($ch);

        if ($httpContent === false) {
            echo curl_error($ch);
            exit;
        }
        curl_close($ch);
        return $httpContent;
    }

    public function get($url) {
        return $this->sendRequest($url);
    }


    public function post($url, $postData, $isHead = false) {
        $this->_lastUrl = $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, $isHead);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $urlArr = parse_url($url);
        if ($urlArr['scheme'] == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        }
        if ($this->_cookieFile) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, realpath($this->_cookieFile));
            curl_setopt($ch, CURLOPT_COOKIEFILE, realpath($this->_cookieFile));
        }
        $httpContent = curl_exec($ch);
        if ($httpContent === false) {
            echo curl_error($ch);
            exit;
        }
        curl_close($ch);

        return $httpContent;

    }

    public function getLastUrl() {
        return $this->_lastUrl;
    }

    public function downloadFile($url, $filePath) {
        if (!$url) {
            return false;
        }
        if (!file_exists(dirname($filePath))) {
            @mkdir(dirname($filePath));
        }
        $data = $this->get($url);
        file_put_contents($filePath, $data);
        return true;
    }
}
