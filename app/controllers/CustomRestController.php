<?php
abstract class CustomRestController extends BaseController {

    abstract function getModel();

    public function index() {
        return $this->getModel()->all();
    }

    public function show($id) {
        return $this->getModel()->find($id);
    }
}