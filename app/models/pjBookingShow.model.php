<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjBookingShowModel extends pjAppModel
{
		
	protected $table = 'bookings_shows';
	
	protected $schema = array(
		array('name' => 'booking_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'show_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'seat_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'cnt', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjBookingShowModel($attr);
	}
}
?>