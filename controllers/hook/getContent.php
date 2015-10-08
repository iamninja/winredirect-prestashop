<?php

/**
* getContent controller
*/
class WinbankRedirectGetContentController
{
    public function __construct($module, $file, $path)
    {
        $this->file = $file;
        $this->module = $module;
        $this->context = Context::getContext();
        $this->_path = $path;
    }

    public function processConfiguration()
    {
        if (Tools::isSubmit('winbankredirect_form')) {
            Configuration::updateValue('WINBANKREDIRECT_CRED_ACQUIRERID', Tools::getValue('WINBANKREDIRECT_CRED_ACQUIRERID'));
            Configuration::updateValue('WINBANKREDIRECT_CRED_MERCHANTID', Tools::getValue('WINBANKREDIRECT_CRED_MERCHANTID'));
            Configuration::updateValue('WINBANKREDIRECT_CRED_POSID', Tools::getValue('WINBANKREDIRECT_CRED_POSID'));
            Configuration::updateValue('WINBANKREDIRECT_CRED_USERNAME', Tools::getValue('WINBANKREDIRECT_CRED_USERNAME'));
            Configuration::updateValue('WINBANKREDIRECT_CRED_PASSWORD', Tools::getValue('WINBANKREDIRECT_CRED_PASSWORD'));

            Configuration::updateValue('WINBANKREDIRECT_OPTS_MAXINSTALLMENTS', Tools::getValue('WINBANKREDIRECT_OPTS_MAXINSTALLMENTS'));
            Configuration::updateValue('WINBANKREDIRECT_OPTS_TRANSACTIONTYPE', Tools::getValue('WINBANKREDIRECT_OPTS_TRANSACTIONTYPE'));

            Configuration::updateValue('WINBANKREDIRECT_URL_DEMO', Tools::getValue('WINBANKREDIRECT_URL_DEMO'));
            Configuration::updateValue('WINBANKREDIRECT_URL_TEST', Tools::getValue('WINBANKREDIRECT_URL_TEST'));
            Configuration::updateValue('WINBANKREDIRECT_URL_LIVE', Tools::getValue('WINBANKREDIRECT_URL_LIVE'));

            Configuration::updateValue('WINBANKREDIRECT_MODE', Tools::getValue('WINBANKREDIRECT_MODE'));

            $current_mode = Tools::getValue('WINBANKREDIRECT_MODE');
            if ($current_mode == 1) {
                Configuration::updateValue('WINBANKREDIRECT_API_URL', Tools::getValue('WINBANKREDIRECT_URL_TEST'));
            } elseif ($current_mode == 2) {
                Configuration::updateValue('WINBANKREDIRECT_API_URL', Tools::getValue('WINBANKREDIRECT_URL_LIVE'));
            } else {
                Configuration::updateValue('WINBANKREDIRECT_API_URL', Tools::getValue('WINBANKREDIRECT_URL_DEMO'));
            }

            $current_transactiontype = Tools::getValue('WINBANKREDIRECT_OPTS_TRANSACTIONTYPE');
        }
    }

    public function renderForm()
    {
        // Create the arrays which will be rendered on configuration page
        $forms_array = array(
            array(
                'name' => 'WINBANKREDIRECT_CRED_ACQUIRERID',
                'label'    => $this->module->l('Acquirer ID'),
                'type' => 'text',
                'class' => 'sm'
            ),
            array(
                'name' => 'WINBANKREDIRECT_CRED_MERCHANTID',
                'label'    => $this->module->l('Merchant ID'),
                'type' => 'text',
                'class' => 'sm'
            ),
            array(
                'name' => 'WINBANKREDIRECT_CRED_POSID',
                'label'    => $this->module->l('PosId'),
                'type' => 'text',
                'class' => 'sm'
            ),
            array(
                'name' => 'WINBANKREDIRECT_CRED_USERNAME',
                'label'    => $this->module->l('Username'),
                'type' => 'text',
                'class' => 'sm'
            ),
            array(
                'name' => 'WINBANKREDIRECT_CRED_PASSWORD',
                'label'    => $this->module->l('Password'),
                'type' => 'text'
            ),
            array(
                'name' => 'WINBANKREDIRECT_OPTS_MAXINSTALLMENTS',
                'label'    => $this->module->l('Maximum installments'),
                'type' => 'text'
            ),
            array(
                'name' => 'WINBANKREDIRECT_URL_DEMO',
                'label'    => $this->module->l('Demo URL'),
                'type' => 'text'
            ),
            array(
                'name' => 'WINBANKREDIRECT_URL_TEST',
                'label'    => $this->module->l('Test URL'),
                'type' => 'text'
            ),
            array(
                'name' => 'WINBANKREDIRECT_URL_LIVE',
                'label'    => $this->module->l('Live URL'),
                'type' => 'text'
            ),
            array(
                'type' => 'radio',
                'label' => $this->module->l('Choose mode:'),
                'name' => 'WINBANKREDIRECT_MODE',
                'desc' => $this->module->l('Choose in which mode to run the module'),
                'is_bool' => false,
                'values' => array(
                    array(
                        'id' => 'mode_demo',
                        'value' => 0,
                        'label' => $this->module->l('Demo')
                    ),
                    array(
                        'id' => 'mode_test',
                        'value' => 1,
                        'label' => $this->module->l('Test')
                    ),
                    array(
                        'id' => 'mode_live',
                        'value' => 2,
                        'label' => $this->module->l('Live')
                    )
                ),
            ),
            array(
                'type' => 'radio',
                'label' => $this->module->l('Request type:'),
                'name' => 'WINBANKREDIRECT_OPTS_TRANSACTIONTYPE',
                'desc' => $this->module->l('Choose whether the transaction type will be "Preauthorization" or "Sale". In case of preauthorization you have to complete the transaction in AdminTool of Piraeus Bank.'),
                'is_bool' => false,
                'values' => array(
                    array(
                        'id' => 'preauthorization',
                        'value' => 0,
                        'label' => $this->module->l('Preauthorization')
                    ),
                    array(
                        'id' => 'sale',
                        'value' => 1,
                        'label' => $this->module->l('Sale')
                    )
                ),
            ),
        );
        $submit_array = array(
            'submit' => array(
                'title' => $this->module->l('Save')
            )
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->module->l('Piraeus Paycenter configuration '),
                    'icon' => 'icon-wrench'
                ),
                'input' => $forms_array,
                'submit' => $submit_array
            ),
        );

        // Build the HelperForm
        $helper = new HelperForm();
        $helper->table = 'winbankredirect';
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int)Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->submit_action = 'winbankredirect_form';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->module->name.'&tab_module='.$this->module->tab.'&module_name='.$this->module->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => array(
                'WINBANKREDIRECT_CRED_ACQUIRERID' => Tools::getValue('WINBANKREDIRECT_CRED_ACQUIRERID', Configuration::get('WINBANKREDIRECT_CRED_ACQUIRERID')),
                'WINBANKREDIRECT_CRED_MERCHANTID' => Tools::getValue('WINBANKREDIRECT_CRED_MERCHANTID', Configuration::get('WINBANKREDIRECT_CRED_MERCHANTID')),
                'WINBANKREDIRECT_CRED_POSID' => Tools::getValue('WINBANKREDIRECT_CRED_POSID', Configuration::get('WINBANKREDIRECT_CRED_POSID')),
                'WINBANKREDIRECT_CRED_USERNAME' => Tools::getValue('WINBANKREDIRECT_CRED_USERNAME', Configuration::get('WINBANKREDIRECT_CRED_USERNAME')),
                'WINBANKREDIRECT_CRED_PASSWORD' => Tools::getValue('WINBANKREDIRECT_CRED_PASSWORD', Configuration::get('WINBANKREDIRECT_CRED_PASSWORD')),
                'WINBANKREDIRECT_OPTS_TRANSACTIONTYPE' => Tools::getValue('WINBANKREDIRECT_OPTS_TRANSACTIONTYPE', Configuration::get('WINBANKREDIRECT_OPTS_TRANSACTIONTYPE')),
                'WINBANKREDIRECT_OPTS_MAXINSTALLMENTS' => Tools::getValue('WINBANKREDIRECT_OPTS_MAXINSTALLMENTS', Configuration::get('WINBANKREDIRECT_OPTS_MAXINSTALLMENTS')),
                'WINBANKREDIRECT_URL_DEMO' => Tools::getValue('WINBANKREDIRECT_URL_DEMO', Configuration::get('WINBANKREDIRECT_URL_DEMO')),
                'WINBANKREDIRECT_URL_TEST' => Tools::getValue('WINBANKREDIRECT_URL_TEST', Configuration::get('WINBANKREDIRECT_URL_TEST')),
                'WINBANKREDIRECT_URL_LIVE' => Tools::getValue('WINBANKREDIRECT_URL_LIVE', Configuration::get('WINBANKREDIRECT_URL_LIVE')),
                'WINBANKREDIRECT_API_URL' => Tools::getValue('WINBANKREDIRECT_API_URL', Configuration::get('WINBANKREDIRECT_API_URL')),
                'WINBANKREDIRECT_MODE' => Tools::getValue('WINBANKREDIRECT_MODE', Configuration::get('WINBANKREDIRECT_MODE')),
            ),
            'languages' => $this->context->controller->getLanguages()
        );

        return $helper->generateForm(array($fields_form));
    }

    public function run()
    {
        $this->processConfiguration();
        $html_confirmation_message = $this->module->display($this->file, 'getContent.tpl');
        $html_form = $this->renderForm();
        return $html_confirmation_message.$html_form;
    }
}
