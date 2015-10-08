<?php

/**
* actionOrderStatusPostUpdate hook controller
*/
class WinbankRedirectActionOrderStatusPostUpdateController
{
    public function __construct($module, $file, $path)
    {
        $this->file = $file;
        $this->module = $module;
        $this->context = Context::getContext();
        $this->_path = $path;
    }

    public function run($params)
    {
        $id_order = $params['id_order'];
        $new_status = $params['newOrderStatus'];

        WinbankRedirectTransaction::setCurrentStateByOrderId($id_order, $new_status);
    }
}
