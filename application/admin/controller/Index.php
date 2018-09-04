<?php
namespace app\admin\controller;

class Index extends BaseAdmin {
    public function index() {
        return $this->fetch('index');
    }
}
