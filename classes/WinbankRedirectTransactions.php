<?php

/**
* ObjectModel for WinbankRedirectTransactions
* Manage DB requests
*/
class WinbankRedirectTransactions extends ObjectModel
{
	public $id_winbankredirect_transaction;
	public $id_cart;
	public $installments;
	public $ticket;
	public $successful;

	public static $definition = array(
		'table' => 'winbankredirect_transaction',
		'primary' => 'id_winbankredirect_transaction',
		'multilang' => 'false',
		'fields' => array(
			'id_cart' => array(
				'type' => self::TYPE_INT,
				'validate' => 'isUnsignedId',
				'required' => true
			),
			'installments' => array(
				'type' => self::TYPE_INT,
				'validate' => 'isUnsignedInt',
				'required' => true
			),
			'ticket' => array(
				'type' => self::TYPE_STRING
			),
			'successful' => array(
				'type' => self::TYPE_BOOL,
				'validate' => 'isUnsignedInt',
				'required' => true
			),
		),
	);
}

