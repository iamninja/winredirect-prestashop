<?php

/**
* Payment Confirmation controller
*/
class WinbankRedirectPaymentConfirmationModuleFrontController extends ModuleFrontController
{

	public function initContent()
	{
		// Disable left and right columns
		$this->display_column_left = false;
		$this->display_column_right = false;

		// Call parent
		parent::initContent();

		// Set template to use
		$this->setTemplate('paymentConfirmation.tpl');

		// Assign cart data to smarty
		$this->context->smarty->assign(array(
			'nb_products' => $this->context->cart->nbProducts(),
			'total_amount' => $this->context->cart->getOrderTotal(true, Cart::BOTH),
			'path' => $this->module->getPathUri(),
		));
	}
}