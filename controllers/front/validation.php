<?php

/**
* Validates the cart into an order
*/
class WinbankRedirectValidationModuleFrontController extends ModuleFrontController
{

	public function postProcess()
	{
		// Get GET data
		$data = Tools::getAllValues();

		// Validate data
		// Check ResultCode
		if (!($data['ResultCode'] == 0))
			$this->returnError('There was an error. '.$data['ResultDescription']);
		else
		{
			if ($data['StatusFlag'] == 'Failure')
				$this->returnError('Transaction not approved by the Issuer.');
		}

		// Check ResponseCode
		if (!$this->checkResponseCode($data['ResponseCode']))
			$this->returnError('Transaction not approved by the Issuer.');

		// Check HashKey
		if(!($this->haskKeyCalculation($data) == $data['HashKey']))
			$this->returnError('There was an error.');

		// Get more data
		$cart = $this->context->cart;
		$currency = $this->context->currency;
		$customer = new Costumer($cart->id_customer);
		$total = (float)$cart->getOrderTotal(true, Cart::BOTH);
		$extra_vars = array();
		// Maybe add some check for the data above

		// TODO
		// Check cart and customer validity (using $data['Parameters'])
		// If check fails on this stage the order has to be placed as problematic

		// TODO
		// If transaction type (WINBANKREDIRECT_OPTS_TRANSACTIONTYPE) is preauthorization
		// an special order state must be used
		if (Configuration::get('WINBANKREDIRECT_OPTS_TRANSACTIONTYPE') == 0)
			$this->module->validateOrder($cart->id, Configuration::get('PS_OS_WINBANKREDIRECT_PREAUTHORIZATION'), $total, $this->module->displayName, NULL, $extra_vars, (int)$currency->id, false, $customer->secure_key);

		// TODO
		// If transaction type (WINBANKREDIRECT_OPTS_TRANSACTIONTYPE) is sale
		// a normal (i.e. one of the prestashop's built in) state can be used.
		// Gonna use PS_OS_PREPARATION
		if (Configuration::get('WINBANKREDIRECT_OPTS_TRANSACTIONTYPE') == 1)
			$this->module->validateOrder($cart->id, Configuration::get('PS_OS_PREPARATION'), $total, $this->module->displayName, NULL, $extra_vars, (int)$currency->id, false, $customer->secure_key);
	}

	public function returnError($result)
	{
		echo Tools::json_encode(array('error' => $result));
		exit;
	}

	public function returnSuccess($result)
	{
		echo Tools::json_encode(array('retrun_link' => $result));
	}

	public function haskKeyCalculation($data = Tools::getAllValues())
	{
		// Build the string
		$string = $data['TranTicket'].
				  Configuration::get('WINBANKREDIRECT_CRED_POSID').
				  Configuration::get('WINBANKREDIRECT_CRED_ACQUIRERID').
				  $data['MerchantReference'].
				  $data['ApprovalCode'].
				  $data['Parameters'].
				  $data['ResponseCode'].
				  $data['SupportReferenceId'].
				  $data['AuthStatus'].
				  $data['PackageNo'].
				  $data['StatusFlag'];

		// Calculate hash
		$hash = hash('hash256', $string);
		return $hash;
	}

	public function hashKeyVerification($value='')
	{
		# code...
	}

	public function checkResponseCode($response_code)
	{
		// Transaction approval
		if ($response_code == '00' ||
			$response_code == '08' ||
			$response_code == '10' ||
			$response_code == '16')
				return true;

		// An successful transaction has already been performed with
		// with the same MerchantReference
		if ($response_code == '11')
			return true;

		// Transaction declined
		return false;
	}
}
