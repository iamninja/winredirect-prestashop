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

	public function getHookController($hook_name)
	{
		// Include the controller file
		require_once(dirname(__FILE__).'/controllers/hook/'. $hook_name.'.php');

		// Build the controller name
		$controller_name = $this->name.$hook_name.'Controller';

		// Instantiate controller
		$controller = new $controller_name($this, __FILE__, $this->_path);

		// Return the controller
		return $controller;
	}

	public function hookDisplayPayment($params)
	{
		$controller = $this->getHookController('displayPayment');
		return $controller->run($params);
	}

	public function getContent()
	{
		$controller = $this->getHookController('getContent');
		return $controller->run();
	}
}