<?php
namespace SloopMini\Http;

class SocketHttp {

    private $_header;

    private $_waitResponse = true;

    private $_sep = "\r\n";

    private $_sockTimeOut = 6;

    private $_rawResponse = null;

    private $_responseHeader = null;

    private $_responseBody = null;

    private $_lastUrl = null;


    public function __construct() {
        if (!function_exists('fsockopen')) {
            throw new \Exception('fsockopen function not exists');
        }
        $this->setHeader('Accept', '*/*');
        $this->setHeader('Host', '');
        $this->setHeader('User-Agent', 'Next Kxi httpClient');
    }

    public function setKeepAlive($bool) {
        $keep = $bool === true ? 'keep-alive' : 'close';
        $this->setHeader('Connection', $keep);
    }

    public function setUserAgent($ua) {
        $this->setHeader('User-Agent', $ua);
    }

    public function setTimeOut($sec) {
        $this->_sockTimeOut = $sec;
    }

    public function discardResponse() {
        $this->_waitResponse = false;
    }

    public function setHeader($key, $value) {
        $this->_header [$key] = $value;
    }

    public function getHeader($key) {
        return $this->_header[$key];
    }

    public function getLastUrl() {
        return 'http://' . $this->_lastUrl;
    }

    public function genHeader() {

        /**  冒号前不能有空白,rfc规定 */
        foreach ($this->_header as $key => $value) {
            $ret .= $key . ": " . $value . $this->_sep;
        }
        return $ret;
    }

    public function post($url, $postData) {
        if ($url == null) {
            //NK_Log::error('NK_Http::post() $url = null');
            return false;
        }
        $url = parse_url($url);
        if ($url['scheme'] == 'https') {
            //TODO
        }
        $server = $url['host'];
        if ($url['port'] == '') {
            $port = 80;
            $this->setHeader('Host', $server);
        } else {
            $port = $url['port'];
            $this->setHeader('Host', $server . ':' . $port);
        }

        if (is_array($postData)) {
            foreach ($postData as $key => $value) {
                $postValue .= urlencode($key) . "=" . urlencode($value) . '&';
            }
            $postValue = substr($postValue, 0, (strlen($postValue) - 1));
        } elseif (is_string($postData)) {
            $postValue = $postData;
        }

        $this->_lastUrl = $this->getHeader('Host') . $url['path'];

        $header = "POST " . $url['path'] . " HTTP/1.1" . $this->_sep;
        $header .= $this->genHeader();
        $header .= "Content-Length: " . strlen($postValue) . $this->_sep;
        $header .= "Content-Type: application/x-www-form-urlencoded" . $this->_sep;
        $header .= $this->_sep;
        $header .= $postValue;

        return $this->sendHttpRequest($server, $port, $header);

    }

    public function get($url, $getData = array()) {
        if ($url == null) {
            //NK_Log::error('NK_Http::get() $url = null');
            return false;
        }
        $url = parse_url($url);
        if ($url['scheme'] == 'https') {
            //TODO
        }
        $server = $url['host'];
        if ($url['port'] == '') {
            $port = 80;
            $this->setHeader('Host', $server);
        } else {
            $port = $url['port'];
            $this->setHeader('Host', $server . ':' . $port);
        }
        if (is_array($getData)) {
            if ($url['query'] == '') {
                $getValue = '?';
            } else {
                $getValue = '?' . $url['query'] . '&';
            }
            foreach ($getData as $key => $value) {
                $getValue .= urlencode($key) . "=" . urlencode($value) . "&";
            }
            $getValue = substr($getValue, 0, (strlen($getValue) - 1));
        }
        $this->_lastUrl = $this->getHeader('Host') . $url['path'] . $getValue;

        $header = "GET " . $url['path'] . $getValue . " HTTP/1.1" . $this->_sep;
        $header .= $this->genHeader();
        $header .= $this->_sep;


        return $this->sendHttpRequest($server, $port, $header);
    }


    private function sendHttpRequest($server, $port, $header) {
        //NK_Log::info(__FILE__ , __LINE__  , 'NK_Http::sendHttpRequest('.$server.":".$port.')');

        $fsocket = fsockopen($server, $port, $errno, $errstr, $this->_sockTimeOut);
        if (!$fsocket) {
            //TODO
            //NK_Log::error(__FILE__ , __LINE__ , 'fsockopen('.$server.','.$port.') error!' );
            return false;
        }
        fwrite($fsocket, $header);

        if (!$this->_waitResponse) {
            fclose($fsocket);
            //NK_Log::info(__FILE__ , __LINE__  , 'NK_Http::_waitResponse = true; Close Sock;');
            return true;
        }

        while (!feof($fsocket)) {

            $out .= fgets($fsocket, 2048);

        }
        fclose($fsocket);
        $this->_rawResponse = $out;

        /**   transfer-encoding: chunked  */

        $pos = strpos($this->_rawResponse, $this->_sep . $this->_sep);
        if ($pos) {
            $this->_responseHeader = substr($out, 0, $pos);
            $this->_responseBody = substr($out, $pos);
            if (strpos(strtolower($this->_responseHeader), "transfer-encoding: chunked") !== false) {
                $this->_responseBody = $this->unchunkHttp11($this->_responseBody);
            }
        }

        return $this->_responseBody;
    }

    public function getRawResponse() {
        return $this->_rawResponse;
    }

    public function getResponseBody() {
        return $this->_responseBody;
    }

    public function getResponseHeader() {
        return $this->_responseHeader;
    }


    private function unchunkHttp11($data) {
        $fp = 0;
        $outData = "";
        while ($fp < strlen($data)) {
            $rawnum = substr($data, $fp, strpos(substr($data, $fp), "\r\n") + 2);
            $num = hexdec(trim($rawnum));
            $fp += strlen($rawnum);
            $chunk = substr($data, $fp, $num);
            $outData .= $chunk;
            $fp += strlen($chunk);
        }
        return $outData;
    }

}

?>
