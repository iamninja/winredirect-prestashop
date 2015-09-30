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

		// Debug
		// $cart234 = $this->context->cart;
		// $cart_methods = get_class_methods(new Cart());
		// print_r(get_object_vars($this->context));
		// $foo = $this->context->foo;

		// Get POST params
		$number_of_installments = $_POST['number_of_installments'];

		// Call parent
		parent::initContent();

		// Set template to use
		$this->setTemplate('paymentConfirmation.tpl');

		// Assign cart data to smarty
		$this->context->smarty->assign(array(
			'nb_products' => $this->context->cart->nbProducts(),
			'total_amount' => $this->context->cart->getOrderTotal(true, Cart::BOTH),
			'shipping_amount' => $this->context->cart->getOrderTotal(true, Cart::ONLY_SHIPPING),
			'products_amount' => $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING),
			'path' => $this->module->getPathUri(),
			'number_of_installments' => $number_of_installments,
			// foo var
			// 'foo' => $foo,
		));
	}

	public function postProcess()
	{
		// Gather data to make ticket request
		// $foo = $this->context->cart->foo;
	}

	private function ticketRequest($params)
	{
		# code...
	}
}