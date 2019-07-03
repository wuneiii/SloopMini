<?php
/**
 * Created by PhpStorm.
 * User: xiaolong
 * Date: 2019/7/4
 * Time: 上午1:03
 */

namespace SloopMini\Test;


use SloopMini\DataAccess\Model;

class ModelHuiyi extends Model {

    public function __construct() {

        $this->init('huiyi', 'id', array(
            'id',
            'strid',
            'title',
            'cover_img',
            'title_img',
            'click',
            'manager_id'

        ), 'ds_abc');

    }


}