<?php

/**
* displayInvoice hook controller
*/
class WinbankRedirectDisplayAdminOrderController
{
    public function __construct($module, $file, $path)
    {
        $this->file = $file;
        $this->module = $module;
        $this->context = Context::getContext();
        $this->_path = $path;
    }

    public function run()
    {
        // Get id_order
        $id_order = (int)Tools::getValue('id_order');

        // Get winbankredirect_transaction row
        $row = WinbankRedirectTransaction::getRowByOrderId((int)$id_order);
        $merchant_reference = $row['merchant_reference'];
        $installments = $row['installments'];
        $is_preauthorization = $row['is_preauthorization'];

        // Assign variables to smarty
        $this->context->smarty->assign(array(
            'merchant_reference' => $merchant_reference,
            'installments' => $installments,
            'is_preauthorization' => $is_preauthorization,
            'row' => $row
        ));

        return $this->module->display($this->file, 'displayAdminOrder.tpl');
    }
}
