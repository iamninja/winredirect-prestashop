<?php

/**
* Admin Panel controller
*/
class AdminWinbankRedirectController extends ModuleAdminController
{
	public function __construct()
	{
		// Set variables
		$this->table = 'winbankredirect_transaction'; // database name
		$this->className = 'WinbankRedirectTransaction'; // ObjectModel class
		$this->fields_list = array(
			'id_winbankredirect_transaction' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'id_cart' => array(
				'title' => $this->l('Cart ID'),
				'width' => 25
			),
			'id_order' => array(
				'title' => $this->l('Order ID'),
				'width' => 25
			),
			'merchant_reference' => array(
				'title' => $this->l('Merchant Reference'),
				'width' => 150
			),
			'installments' => array(
				'title' => $this->l('Installments'),
				'width' => 25
			),
			'successful' => array(
				'title' => $this->l('Sale'),
				'width' => 25
			),
		);

		// Enable bootstrap
		$this->bootstrap = true;

		// Call parent method
		parent::__construct();
	}
}