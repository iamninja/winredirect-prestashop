<?php

/**
* Module's main class
*/
class WinbankRedirect extends PaymentModule
{

	function __construct()
	{
		// Module details
		$this->name = 'winbankredirect';
		$this->tab = 'payment_gateways';
		$this->version = '0.1';
		$this->author = 'Vagios Vlachos';
		$this->bootstrap = true;

		// Call parent
		parent::__construct();

		// More details
		$this->displayName = $this->l('Winbank Redirect');
		$this->description = $this->l('Winbank payment module, using redirect method');
	}

	public function install()
	{
		// Register hooks
		if (!parent::install() ||
			!$this->registerHook('displayPayment') ||
			!$this->registerHook('displayPaymentReturn'))
				return false;

		return true;
	}
}