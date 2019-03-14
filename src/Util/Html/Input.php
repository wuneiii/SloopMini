<?php

namespace SloopMini\Util\Html;

class Input {

    static public function select($name, $option = array(), $selected = '', $attr = array('class' => '', 'id' => '')) {
        $_attrStr = '';
        if (count($attr) != '') {
            foreach ($attr as $_name => $_value) {
                $_attrStr .= $_name . '="' . $_value . '"';
            }
        }
        $ret = '<select name="' . $name . '" ' . $_attrStr . '>';
        if (count($option) == 0) {
            $ret .= '<option>请选择</option>';
            return $ret;
        }
        foreach ($option as $k => $v) {
            $prob = '';
            if ($selected == $k) {
                $prob = 'selected';
            }
            $ret .= '<option value="' . $k . '" ' . $prob . '>' . $v . '</option>';
        }
        return $ret .= '</select>';
    }


    static public function text($name, $value = '', $attr = array('class' => '', 'id' => '')) {
        $_attrStr = '';
        if (count($attr) != '') {
            foreach ($attr as $_name => $_value) {
                $_attrStr .= $name . '=' . $_value;
            }
        }
        return '<input type="text" value="' . $value . '" name="' . $name . '" ' . $_attrStr . '></input>';
    }

    public static function textarea($name, $value = '', $attr = array('class' => '', 'id' => '')) {
        return '
            <textarea name="' . $name . '" rows=5 cols=60>' . $value . '</textarea>';
    }

    public static function submit($text = 'submit') {
        return '
            <input type="submit" value="' . $text . '">';
    }

    public static function hidden($name, $value) {
        return '
            <input type="hidden" name="' . $name . '" value="' . $value . '">';
    }

    public static function checkbox($name, $value, $selected = '', $attr) {
        $_attrStr = '';
        $ret = '';
        if (count($attr) != '') {
            foreach ($attr as $_name => $_value) {
                $_attrStr .= $_name . '="' . $_value . '"';
            }
        }

        if (count($value) == 0) {
            $ret .= '无选项';
            return $ret;
        }
        if ($selected == '') {
            $selected = array();
        }
        foreach ($value as $k => $v) {
            $prob = '';
            if (in_array($k, $selected)) {
                $prob = ' checked="checked"';
            }
            $ret .= '<label><input type="checkbox" value="' . $k . '" ' . $prob . ' ' . $_attrStr . '>' . $v . '</label><br>';
        }
        return $ret;

    }

}