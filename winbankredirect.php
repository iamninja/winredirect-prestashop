<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/classes/WinbankRedirectTransaction.php');

/**
* Module's main class
*/
class Winbankredirect extends PaymentModule
{
    public function __construct()
    {
        // Module details
        $this->name = 'winbankredirect';
        $this->tab = 'payments_gateways';
        $this->version = '0.3.1';
        $this->author = 'Vagios Vlachos';
        $this->bootstrap = true;

        // Call parent
        parent::__construct();

        // More details
        $this->displayName = $this->l('Winbank Redirect');
        $this->description = $this->l('Winbank payment module, using redirect method');
    }

    public function loadSQLfile($sql_file)
    {
        // Get SQL file content
        $sql_content = Tools::file_get_contents($sql_file);

        // Replace PREFIX
        $sql_content = str_replace('PREFIX_', _DB_PREFIX_, $sql_content);
        // Create array with the SQL requests
        $sql_requests = preg_split("/;\s*[\r\n]+/", $sql_content);

        // Execute SQL requests
        $result = true;
        foreach ($sql_requests as $request) {
            if (!empty($request)) {
                $result &= Db::getInstance()->execute(trim($request));
            }
        }

        // Return result
        return $result;
    }

    public function install()
    {
        // Register hooks and call parent
        if (!parent::install() ||
            !$this->registerHook('displayPayment') ||
            !$this->registerHook('displayPaymentReturn') ||
            !$this->registerHook('displayAdminOrder') ||
            !$this->registerHook('actionValidateOrder') ||
            !$this->registerHook('actionOrderStatusPostupdate')) {
            return false;
        }

        // Execute install SQL requests
        $sql_file = dirname(__FILE__).'/install/install.sql';
        if (!$this->loadSQLfile($sql_file)) {
            return false;
        }

        // Install admin tab
        if (!$this->installAdminTab('AdminOrders', 'AdminWinbankRedirect', $this->l('Winbank Redirect'))) {
            return false;
        }

        // Initiate configuration values
        // Configuration::updateValue('VALUE_NAME', 'value');

        // Install order states
        if (!$this->installOrderState()) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        // Call parent
        if (!parent::uninstall()) {
            return false;
        }

        // Execute uninstall SQL requests
        $sql_file = dirname(__FILE__).'/install/uninstall.sql';
        if (!$this->loadSQLfile($sql_file)) {
            return false;
        }

        // Uninstall admin tab
        if (!$this->uninstallAdminTab('AdminWinbankRedirect')) {
            return false;
        }

        // Delete configuration values
        // Configuration::deleteByName('VALUE_NAME');

        return true;
    }

    public function installOrderState()
    {
        if (Configuration::get('PS_OS_WINBANKREDIRECT_PREAUTHORIZATION') < 1) {
            $order_state = new OrderState();
            $order_state->send_email = false;
            $order_state->module_name = $this->name;
            $order_state->invoice = false;
            $order_state->color = '#787878';
            $order_state->logable = true;
            $order_state->shipped = false;
            $order_state->unremovable = false;
            $order_state->delivery = false;
            $order_state->hidden = false;
            $order_state->paid = false;
            $order_state->deleted = false;
            $order_state->name = array(
                (int)Configuration::get('PS_LANG_DEFAULT') => pSQL($this->l('Winbank Redirect - Preauthorization'))
            );

            if ($order_state->add()) {
                // Save the order state id in Configuration
                Configuration::updateValue('PS_OS_WINBANKREDIRECT_PREAUTHORIZATION', $order_state->id);

                // Copy the module's logo in the order state logo directory
                copy(
                    dirname(__FILE__).'/logo.png',
                    dirname(__FILE__).'/../../img/os/'.$order_state->id.'.png'
                );
                copy(
                    dirname(__FILE__).'/logo.png',
                    dirname(__FILE__).'/../../img/tmp/order_state_mini_'.$order_state->id.'/logo.png'
                );
            } else {
                return false;
            }
        }
        return true;
    }

    public function installAdminTab($parent, $class_name, $name)
    {
        // Create new admin tab
        $tab = new Tab();
        // Get the parent's id
        $tab->id_parent = (int)Tab::getIdFromClassName($parent);
        // Set name (multilanguage)
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $name;
        }
        // More attributes
        $tab->class_name = $class_name; // controller's name without the Controller suffix
        $tab->module = $this->name;
        $tab->active = 1;
        // Add tab and return
        return $tab->add();
    }

    public function uninstallAdminTab($class_name)
    {
        // Retrieve tab id
        $id_tab = (int)Tab::getIdFromClassName($class_name);
        // Get the tab
        $tab = new Tab((int)$id_tab);
        // Delete the tab
        return $tab->delete();
    }

    public function getHookController($hook_name)
    {
        // Include the controller file
        require_once(dirname(__FILE__).'/controllers/hook/'.$hook_name.'.php');

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

    public function hookDisplayAdminOrder($params)
    {
        $controller = $this->getHookController('displayAdminOrder');
        return $controller->run($params);
    }

    public function hookActionValidateOrder($params)
    {
        $controller = $this->getHookController('actionValidateOrder');
        return $controller->run($params);
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        $controller = $this->getHookController('actionOrderStatusPostupdate');
        return $controller->run($params);
    }

    public function getContent()
    {
        $controller = $this->getHookController('getContent');
        return $controller->run();
    }
}
