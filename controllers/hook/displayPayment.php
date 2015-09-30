<?php

/**
* displayPayment hook controller
*/
class WinbankRedirectDisplayPaymentController
{

	function __construct($module, $file, $path)
	{
		$this->file = $file;
		$this->module = $module;
		$this->context = Context::getContext();
		$this->_path = $path;
		// $this->winbankredirect_installments = 1;
	}

	public function run($params)
	{
		// Check if the cart is qualified for installments
		$total_amount = $this->context->cart->getOrderTotal(true, Cart::BOTH);
		$number_of_installments = (int)($total_amount / 30);

		// Debug
		// Add foo variable to context
		// $this->context->foo = "bar";
		// print_r(get_object_vars(Context::getContext()));

		// Assign cart data to smarty
		$this->context->smarty->assign(array(
			'nb_products' => $this->context->cart->nbProducts(),
			'total_amount' => $this->context->cart->getOrderTotal(true, Cart::BOTH),
			'shipping_amount' => $this->context->cart->getOrderTotal(true, Cart::ONLY_SHIPPING),
			'products_amount' => $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING),
			'path' => $this->module->getPathUri(),
			'number_of_installments' => $number_of_installments,
			// foo var
			// 'foo' => $this->context->foo,
		));

		$this->context->controller->addCSS($this->_path.'views/css/winbankredirect.css', 'all');
		return $this->module->display($this->file, 'displayPayment.tpl');
	}
}