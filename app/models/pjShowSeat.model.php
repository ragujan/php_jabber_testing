<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjShowSeatModel extends pjAppModel
{
	protected $primaryKey = NULL;
	
	protected $table = 'shows_seats';
	
	protected $schema = array(
		array('name' => 'show_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'seat_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjShowSeatModel($attr);
	}
}
?>