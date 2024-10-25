<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjBookingTicketModel extends pjAppModel
{
		
	protected $table = 'bookings_tickets';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'booking_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'ticket_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'price_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'seat_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'unit_price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'is_used', 'type' => 'enum', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjBookingTicketModel($attr);
	}
}
?>