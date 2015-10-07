<?php

/**
* actionValidateOrder hook controller
*/
class WinbankRedirectActionValidateOrderController
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
		// If this order did not made with this module do nothing
		if ($params['order']->module != $this->module)
			return true;

		// Update winbankredirect_transaction entry
		$id_order = (int)$params['order']->id;
		$id_cart = (int)$params['cart']->id;
		WinbankRedirectTransaction::setOrderIdByCartId($id_cart, $id_order);
		WinbankRedirectTransaction::setSuccessfulByOrderId($id_order, 1);

		return true;
	}
}