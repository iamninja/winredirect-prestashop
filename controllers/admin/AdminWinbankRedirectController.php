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

        // Enable bootstrap
        $this->bootstrap = true;

        // Call parent method
        parent::__construct();

        // Get order states names
        $statuses_array = array();
        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status) {
            $statuses_array[$status['id_order_state']] = $status['name'];
        }

        $this->fields_list = array(
            'id_winbankredirect_transaction' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25,
                'search' => false
            ),
            'id_order' => array(
                'title' => $this->l('Order ID'),
                'width' => 30
            ),
            'current_state' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'list' => $statuses_array,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'current_state',
                'callback' => 'getOrderStateName',
                'color' => 'red'
            ),
            'merchant_reference' => array(
                'title' => $this->l('Merchant Reference'),
                'width' => 80
            ),
            'installments' => array(
                'title' => $this->l('Installments'),
                'width' => 30,
                'align' => 'center'
            ),
            'is_preauthorization' => array(
                'title' => $this->l('Preauthorization'),
                'width' => 10,
                'align' => 'center',
                'type' => 'bool',
                'callback' => 'showYesNo'
            ),
            'successful' => array(
                'title' => $this->l('Successful'),
                'width' => 10,
                'align' => 'center',
                'type' => 'bool',
                'callback' => 'showYesNo'
            ),
        );

        // Maybe it's better to show unsuccessful transactions too (for "debugging", using merchant_reference)
        // $this->_where = 'AND a.`successful` = 1';

        $this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
			LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state`
            AND osl.`id_lang` = '.(int)$this->context->language->id.')';
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

    public function renderList()
    {
        // Get order states names
        $statuses_array = array();
        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status) {
            $statuses_array[$status['id_order_state']] = $status['name'];
        }

        $this->tpl_list_vars['updateOrderStatus_mode'] = true;
        $this->tpl_list_vars['current_states'] = $statuses_array;
        $this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
        $this->tpl_list_vars['POST'] = $_POST;

        return parent::renderList();
    }

    public function getOrderStateName($echo, $tr)
    {
        // Get order states names
        $statuses_array = array();
        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status) {
            $statuses_array[$status['id_order_state']] = $status['name'];
        }

        return $statuses_array[$echo];
    }

    public function showYesNo($echo, $tr)
    {
        if ($echo == 0) {
            return "No";
        } else {
            return "Yes";
        }
    }
}
