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
		$number_of_installments = 0; // default to 0
		if ($_POST['number_of_installments'])
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
		// Gather data


		// Hash data


		// Create database entry for this id_cart if not exists

		//Get ticket
		// $ticket = $this->ticketRequest();

		// Save ticket to database
		// WinbankRedirectTransaction::setTicketByCart($id_cart, $ticket);

		// All in one for temporarily
		$id_cart = $this->context->cart->id;
		// delete all instead of checking for existance
		WinbankRedirectTransaction::deleteUnsuccessfulByCartId($id_cart);
		$this->createTransactionEntry();
	}

	private function ticketRequest()
	{
		// For now generate a random hash
		$source = '';
		for ($i = 1; $i < 10; $i++)
			$source .= mt_rand(0, 9);

		$ticket = hash('sha256', $source);
		return $ticket;
	}

	private function createTransactionEntry()
	{
		$WinbankRedirectTransaction = new WinbankRedirectTransaction();
		$WinbankRedirectTransaction->id_cart = $this->context->cart->id;
		$WinbankRedirectTransaction->installments = $_POST['number_of_installments'] ?: 0;
		// Temporarily. Assign ticket on creation
		$WinbankRedirectTransaction->ticket = $this->ticketRequest();
		$WinbankRedirectTransaction->successful = 0;
		$WinbankRedirectTransaction->add();
	}

	private function deleteTransactionEntryIfExists($id_cart)
	{
		$entries = WinbankRedirectTransaction::getEntryByCartId($id_cart);
		$ids = array();
		var_dump($entries);
		foreach ($entries as $entry) {
			$ids[] = $entry['id_winbankredirect_transaction'];
		}
		var_dump($ids);
		WinbankRedirectTransaction::deleteSelection($ids);
	}

}