<?php

/**
* ObjectModel for WinbankRedirectTransaction
* Manage DB requests
*/
class WinbankRedirectTransaction extends ObjectModel
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

	// Get ticket from a transaction given the id_cart
	public static function getTicketByCart($id_cart)
	{
		$transaction_ticket = Db::getInstance()->executeS('
			SELECT `ticket` FROM `'._DB_PREFIX_.'winbankredirect_transaction`
			WHERE `id_cart` = '.(int)$id_cart);

		return $transaction_ticket;
	}

	// Set ticket for a transaction given the id_cart
	public static function setTicketByCart($id_cart, $ticket)
	{
		Db::getInstance()->executeS('
			UPDATE `'._DB_PREFIX_.'winbankredirect_transaction`
			SET `ticket` = '.$ticket.'
			WHERE `id_cart` = '.(int)$id_cart);
	}

	// Check if a transaction has a valid ticket assigned, given the id_cart.
	// For now just check if it is hasn't the default value.
	// Maybe later it should also check if it is expired (older than 30 minutes)
	public static function checkTicketValidity($id_cart)
	{
		$ticket = Db::getInstance()->executeS('
			SELECT `ticket` FROM `'._DB_PREFIX_.'winbankredirect_transaction`
			WHERE `id_cart` = '.(int)$id_cart);

		if ($ticket == '0')
			return false;

		return true;
	}

	// Get successfull transactions
	public static function getSuccessfull()
	{
		$transactions = Db::getInstance()->executeS('
			SELECT * FROM `'._DB_PREFIX_.'winbankredirect_transaction`
			WHERE `successfull` > 0');

		return $transactions;
	}

	// Get transaction entry by id
	public static function getEntryById($id)
	{
		$result = Db::getInstance()->executeS('
			SELECT * FROM `'._DB_PREFIX_.'winbankredirect_transaction`
			WHERE `id_winbankredirect_transaction` = '.$id);

		return $result;
	}
}
