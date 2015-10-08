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

        // Get cart id
        $id_cart = $this->context->cart->id;

        // Set language code
        // TODO Make it a little more general?
        // Accepted values: en-US, el-GR, ru-RU, de-DE
        $language_code = $this->context->language->language_code;

        // Get merchant reference
        $merchant_reference = WinbankRedirectTransaction::getMerchantReferenceByCartId($id_cart);

        // Set params to be sent with the bank link
        $param_back_link = '';

        // Set URL to make POST request
        $api_url = '';

        // Assign cart data to smarty
        $this->context->smarty->assign(array(
            'nb_products' => $this->context->cart->nbProducts(),
            'total_amount' => $this->context->cart->getOrderTotal(true, Cart::BOTH),
            'shipping_amount' => $this->context->cart->getOrderTotal(true, Cart::ONLY_SHIPPING),
            'products_amount' => $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING),
            'path' => $this->module->getPathUri(),
            'api_url' => $api_url,
            'number_of_installments' => $number_of_installments,
            'acquirer_id' => Configuration::get('WINBANKREDIRECT_CRED_ACQUIRERID'),
            'merchant_id' => Configuration::get('WINBANKREDIRECT_CRED_MERCHANTID'),
            'pos_id' => Configuration::get('WINBANKREDIRECT_CRED_POSID'),
            'user' => Configuration::get('WINBANKREDIRECT_CRED_USERNAME'),
            'language_code' => $language_code,
            'merchant_reference' => $merchant_reference,
            'param_back_link' => $param_back_link
        ));
    }

    public function postProcess()
    {
        // Gather data
        // Set request type
        if (Configuration::get('WINBANKREDIRECT_OPTS_TRANSACTIONTYPE') == 0) {
            $requestType = '00'; // preauthorization
            $expirePreauth = 30; // hardcode max possible value
            $is_preauthorization = 1;
        } else {
            $requestType = '02'; // sale
            $expirePreauth = 0; // have to be 0 in this case
            $is_preauthorization = 0;
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

        // Get ticket
        $response = $this->ticketRequest($data);
        // print_r($response);

        if ($response->IssueNewTicketResult->ResultCode == 0) {
            $tran_ticket = $response->IssueNewTicketResult->TranTicket;
        } else {
            // Temporarily
            $trans_ticket = 'ticket_error';
            $error_message = $response->IssueNewTicketResult->ResultDescription;
            // $this->returnError($error_message);
        }

        // Hash data


        // Create database entry for this id_cart if not exists

        // All in one for temporarily
        $id_cart = $this->context->cart->id;
        // delete all instead of checking for existance
        // TODO deletion should not be happening here.
        // Existance and overwrite should not be used.
        // Delete or cart related entries which are unsuccessful at validation
        WinbankRedirectTransaction::deleteUnsuccessfulByCartId($id_cart);
        // Generate merchant reference
        $merchantReference = $this->generateMerchantReference();
        // Create transaction entry
        $this->createTransactionEntry($merchantReference, $is_preauthorization);
        // Set ticket on previous entry
        WinbankRedirectTransaction::setTicketByCart($id_cart, $trans_ticket);
    }

    private function ticketRequest($data)
    {
        // For now generate a random hash
        // $source = '';
        // for ($i = 1; $i < 10; $i++)
        // 	$source .= mt_rand(0, 9);

        // $ticket = hash('sha256', $source);
        // return $ticket;

        // Create new soap client instance
        $params = array(
            'trace' => 1,
            'exceptions' => 0,
            'use' => SOAP_LITERAL,
            'style' => SOAP_DOCUMENT
        );
        $client = new SoapClient('https://paycenter.piraeusbank.gr/services/tickets/issuer.asmx?WSDL', $params);

        $response = $client->IssueNewTicket(array('Request' => $data));
        return $response;
    }

    private function generateMerchantReference()
    {
        $shop_and_customer_id =
            $this->context->customer->id_shop.
            $this->context->customer->id;
        $merchantReference = $shop_and_customer_id.date("dmYHis");
        return $merchantReference;
    }

    private function createTransactionEntry($merchantReference, $is_preauthorization)
    {
        $WinbankRedirectTransaction = new WinbankRedirectTransaction();
        $WinbankRedirectTransaction->id_cart = $this->context->cart->id;
        $WinbankRedirectTransaction->id_order = 0;
        $WinbankRedirectTransaction->current_state = 0;
        $WinbankRedirectTransaction->merchant_reference = $merchantReference;
        $WinbankRedirectTransaction->installments = Tools::getValue('number_of_installments', 0);
        $WinbankRedirectTransaction->is_preauthorization = $is_preauthorization;
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

    public function returnError($result)
    {
        echo Tools::json_encode(array('error' => $result));
        exit;
    }
}
