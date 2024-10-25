<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjShowModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'shows';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'event_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'venue_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'date_time', 'type' => 'datetime', 'default' => 'T')
	);
	
	public static function factory($attr=array())
	{
		return new pjShowModel($attr);
	}
}
?>