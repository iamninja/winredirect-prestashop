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
		$this->addRowAction('view'); // row actions
		$this->fields_list = array(
			'id_winbankredirect_transaction' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25,
				'search' => false
			),
			'id_cart' => array(
				'title' => $this->l('Cart ID'),
				'width' => 25,
				'search' => false
			),
			'id_order' => array(
				'title' => $this->l('Order ID'),
				'width' => 25
			),
			'current_state' => array(
				'title' => $this->l('Status'),
				'width' => 100,
				'search' => true,
				'type' => 'select'
			),
			'merchant_reference' => array(
				'title' => $this->l('Merchant Reference'),
				'width' => 100
			),
			'installments' => array(
				'title' => $this->l('Installments'),
				'width' => 25
			),
			'is_preauthorization' => array(
				'title' => $this->l('Preauthorization'),
				'width' => 25,
				'type' => 'bool'
			)
		);

		// Enable bootstrap
		$this->bootstrap = true;

		// Call parent method
		parent::__construct();
	}

	public function renderView()
    {
        // Get order by id_order
    	$id = Tools::getValue('id_winbankredirect_transaction');
    	$id_order = WinbankRedirectTransaction::getOrderIdById($id);
    	$order = new Order($id_order);

    	// TODO Check validity of id_order first
    	if (!Validate::isLoadedObject($order)) {
            $this->errors[] = Tools::displayError('The order cannot be found within your database.');
    	}

        // Redirect to order view
    	$link = $this->context->link->getAdminLink('AdminOrders');
    	$link .= '&vieworder&id_order='.$id_order;
    	$this->redirect_after = $link;
    	$this->redirect();
    }
}