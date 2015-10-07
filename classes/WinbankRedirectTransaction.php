<?php

/**
* ObjectModel for WinbankRedirectTransaction
* Manage DB requests
*/
class WinbankRedirectTransaction extends ObjectModel
{
	public $id_winbankredirect_transaction;
	public $id_cart;
	public $merchantReference;
	public $installments;
	public $ticket;
	public $successful;

	public static $definition = array(
		'table' => 'winbankredirect_transaction',
		'primary' => 'id_winbankredirect_transaction',
		'multilang' => false,
		'fields' => array(
			'id_cart' => array(
				'type' => self::TYPE_INT,
				'validate' => 'isUnsignedId',
				'required' => true
			),
			'id_order' => array(
				'type' => self::TYPE_INT,
				'validate' => 'isUnsignedId'
			),
			'current_state' => array(
				'type' => self::TYPE_INT,
				'validate' => 'isUnsignedId'
			),
			'merchant_reference' => array(
				'type' => self::TYPE_STRING,
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
			'is_preauthorization' => array(
				'type' => self::TYPE_BOOL,
				'validate' => 'isUnsignedInt',
				'required' => true
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
		// $return = Db::getInstance()->executeS('
		// 	UPDATE `'._DB_PREFIX_.'winbankredirect_transaction`
		// 	SET `ticket` = '.$ticket.'
		// 	WHERE `id_cart` = '.(int)$id_cart);

		// return $return;

		// Better practice
		$data = array('ticket' => $ticket);
		$where = 'id_cart = '.(int)$id_cart;
		return Db::getInstance()->update('winbankredirect_transaction', $data, $where);
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

	// Get transaction entry by id_cart
	public static function getEntryByCartId($id_cart)
	{
		$result = Db::getInstance()->executeS('
			SELECT * FROM `'._DB_PREFIX_.'winbankredirect_transaction`
			WHERE `id_cart` = '.$id_cart);

		return $result;
	}

	// Delete transactions by id_cart
	public static function deleteUnsuccessfulByCartId($id_cart)
	{
		// $result = Db::getInstance()->executeS('
		// 	DELETE FROM `'._DB_PREFIX_.'winbankredirect_transaction`
		// 	WHERE `id_cart` = '.$id_cart' AND `successful` = 0');

		// return $result;

		// Better practice
		$where = 'id_cart = '.(int)$id_cart.' AND successful = 0';
		return Db::getInstance()->delete('winbankredirect_transaction', $where);
	}

	// Get merchant reference by id_cart
	public static function getMerchantReferenceByCartId($id_cart)
	{
		$merchant_reference = Db::getInstance()->executeS('
			SELECT `merchant_reference` FROM `'._DB_PREFIX_.'winbankredirect_transaction`
			WHERE `id_cart` = '.(int)$id_cart);

		return $merchant_reference[0]['merchant_reference'];
	}

	// Set id_order by id_cart
	public static function setOrderIdByCartId($id_cart, $id_order)
	{
		$data = array('id_order' => $id_order);
		$where = 'id_cart = '.(int)$id_cart;
		return Db::getInstance()->update('winbankredirect_transaction', $data, $where);
	}

	// Set current_state by id_cart
	public static function setCurrentStateByCartId($id_cart, $current_state)
	{
		$data = array('current_state' => $current_state);
		$where = 'id_cart = '.(int)$id_cart;
		return Db::getInstance()->update('winbankredirect_transaction', $data, $where);
	}

	// Set current_state by id_order
	public static function setCurrentStateByOrderId($id_order, $current_state)
	{
		$data = array('current_state' => $current_state);
		$where = 'id_order = '.(int)$id_order;
		return Db::getInstance()->update('winbankredirect_transaction', $data, $where);
	}

	// Get current_state by id
	public static function getCurrentStateById($id)
	{
		$current_state = Db::getInstance()->executeS('
			SELECT `current_state` FROM `'._DB_PREFIX_.'winbankredirect_transaction`
			WHERE `id_winbankredirect_transaction` = '.(int)$id);

		return $current_state[0]['current_state'];
	}

	// Get id_order by id
	public static function getOrderIdById($id)
	{
		$id_order = Db::getInstance()->executeS('
			SELECT `id_order` FROM `'._DB_PREFIX_.'winbankredirect_transaction`
			WHERE `id_winbankredirect_transaction` = '.(int)$id);

		return $id_order[0]['id_order'];
	}

	// Get entry by id_order
	public static function getRowByOrderId($id_order)
	{
		$sql = '
		SELECT * FROM `'._DB_PREFIX_.'winbankredirect_transaction`
		WHERE `id_order` = '.(int)$id_order;

		$row = Db::getInstance()->getRow($sql);
		return $row;
	}
}

