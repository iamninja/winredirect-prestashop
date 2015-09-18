<?php

/**
* displayPayment hook controller
*/
class WinbankRedirectDisplayPaymentController
{

	function __construct($module, $file, $path)
	{
		$this->file = $file;
		$this->module = $module;
		$this->context = Context::getContext();
		$this->_path = $path;
	}

	public function run($params)
	{
		$this->context->controller->addCSS($this->_path.'views/css/winbankredirect.css', 'all');
		return $this->module->display($this->file, 'displayPayment.tpl');
	}
}