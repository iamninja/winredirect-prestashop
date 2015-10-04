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
		// Favoring Prestashop's Tools::getValue($key, $default_value) over using the superglobals directly
		// $number_of_installments = 0; // default to 0
		// if ($_POST['number_of_installments'])
		// 	$number_of_installments = $_POST['number_of_installments'];
		$number_of_installments = Tools::getValue('number_of_installments', 0);

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
		// Set request type
		if (Configuration::get('WINBANKREDIRECT_OPTS_TRANSACTIONTYPE') == 0)
		{
			$requestType = '00'; // preauthorization
			$expirePreauth = 30; // hardcode max possible value
		}
		else
		{
			$requestType = '02'; // sale
			$expirePreauth = 0;
		}
		// Generate merchant reference
		$merchantReference = $this->generateMerchantReference();

		// Total amount to pay
		$total_amount = $this->context->cart->getOrderTotal(true, Cart::BOTH);

		// Number of installments
		// TODO Add extra check here if the number is valid
		$number_of_installments = Tools::getValue('number_of_installments', 0);

		// Parameters to send back
		$parameters = 'number_of_installments='.$number_of_installments;

		$data = array(
			'AcquirerId' => Configuration::get('WINBANKREDIRECT_CRED_ACQUIRERID'),
			'MerchantId' => Configuration::get('WINBANKREDIRECT_CRED_MERCHANTID'),
			'PosId' => Configuration::get('WINBANKREDIRECT_CRED_POSID'),
			'Username' => Configuration::get('WINBANKREDIRECT_CRED_USERNAME'),
			'Password' => Configuration::get('WINBANKREDIRECT_CRED_PASSWORD'),
			'RequestType' => $requestType,
			'CurrencyCode' => 978, // hardcoded to Euro
			'MerchantReference' => $merchantReference,
			'Amount' => (float)$total_amount,
			'Installments' => (int)$number_of_installments,
			'ExpirePreauth' => (int)$expirePreauth,
			'Bnpl' => 0, // hardcode value. Unused variable
			'Parameters' => $parameters
		);

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
		// Generate merchant reference
		$merchantReference = $this->generateMerchantReference();
		// Create transaction entry
		$this->createTransactionEntry($merchantReference);
		// Set ticket on previous entry
		WinbankRedirectTransaction::setTicketByCart($id_cart, $this->ticketRequest());
	}

	private function ticketRequest()
	{
		// For now generate a random hash
		$source = '';
		for ($i = 1; $i < 10; $i++)
			$source .= mt_rand(0, 9);

		$ticket = hash('sha256', $source);
		return $ticket;

		// Create new soap client instance
		// $client = new SoapClient('https://paycenter.piraeusbank.gr/services/tickets/issuer.asmx?WSDL');


	}

	private function generateMerchantReference()
	{
		$merchantReference = date("dmYHis");
		return $merchantReference;
	}

	private function createTransactionEntry($merchantReference)
	{
		$WinbankRedirectTransaction = new WinbankRedirectTransaction();
		$WinbankRedirectTransaction->id_cart = $this->context->cart->id;
		$WinbankRedirectTransaction->merchant_reference = $merchantReference;
		$WinbankRedirectTransaction->installments = Tools::getValue('number_of_installments', 0);
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