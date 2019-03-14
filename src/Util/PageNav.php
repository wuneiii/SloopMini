<?php

namespace SloopMini\Util;


class PageNav {
    private $perPage;
    private $total;
    // 显示页码连接的个数
    private $pageBoxNum;
    private $pageParamName;


    public function __construct($paramName = 'page', $perPage = 10, $pageBoxNum = 10) {
        $this->setPageParamName($paramName);
        $this->setPageBoxNum($pageBoxNum);
        $this->setPerPage($perPage);
        $this->total = 0;
    }

    public function setPerPage($n) {
        $n = intval($n);
        if (!$n) {
            $n = 10;
        }
        $this->perPage = $n;
    }

    public function setPageParamName($paramName) {
        $this->pageParamName = $paramName;
    }

    public function setTotal($n) {
        $this->total = intval($n);
    }

    // 保证$n 是奇数，当前页左右边页码数相等
    public function setPageBoxNum($n) {
        if (!($n % 2)) {
            $n++;
        }
        $this->pageBoxNum = intval($n);
    }

    public function getPerPage() {
        return $this->perPage;
    }

    /**
     * 当前页数，不能设置，只能获取
     * 最小是1
     */
    public function getCurrentPage() {
        $req = Request::getInstance();
        $page = $req->getInt($this->pageParamName);
        if ($page <= 0) {
            $page = 1;
        }
        return $page;
    }

    private function getLimit() {
        $curPage = $this->getCurrentPage();
        $start = ($curPage - 1) * $this->perPage;
        return sprintf(' LIMIT %d,%d ', $start, $this->perPage);
    }

    public function getPageNavHtml() {
        $pagerHtml = '';

        if ($this->total <= $this->perPage) {
            return false;
        }


        $pageCurrent = $this->getCurrentPage();
        $pageTotal = ceil($this->total / $this->perPage);


        // 计算当前页在全局中的位置
        // 用来判断是否前后显示省略号
        $halfPageBoxNum = floor($this->pageBoxNum / 2);


        for ($i = 1; $i <= $pageTotal; $i++) {

            // 当前页
            if ($i == $pageCurrent) {
                $pagerHtml .= $this->htmlDiv($this->htmlA($i), 'pager_current pager');
                continue;
            }


            if ($i == 1 || $i == $pageTotal) {
                $url = $this->getPageUrl($i);
                $pagerHtml .= $this->htmlDiv($this->htmlA($i, $url), 'pager_tail pager');
                continue;
            }


            if ($i < ($pageCurrent - $halfPageBoxNum)) {
                $i = $pageCurrent - $halfPageBoxNum - 1;
                $pagerHtml .= $this->htmlDiv('...');
                continue;
            }
            if ($i > ($pageCurrent + $halfPageBoxNum)) {
                $i = $pageTotal - 1;
                $pagerHtml .= $this->htmlDiv('...');
                continue;
            }

            $url = $this->getPageUrl($i);
            $pagerHtml .= $this->htmlDiv($this->htmlA($i, $url), 'pager');
        }


        return $this->htmlDiv($pagerHtml, 'pager_container');

    }

    private
    function htmlA($content, $href = 'javascript:;') {
        return sprintf('<a href="%s">%s</a>', $href, $content);
    }

    private
    function htmlDiv($content, $class = '') {
        if ($class) {
            $class = ' class="' . $class . '"';
        }
        return sprintf('<div%s>%s</div>', $class, $content);
    }

    private
    function getPageUrl($page) {
        $request = Request::getInstance();
        $param = $request->getUrlParam();
        $param[$this->pageParamName] = $page;
        return $request->getUrlPath() . '?' . http_build_query($param);
    }
}