<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjSeatModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'seats';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'venue_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'width', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'height', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'top', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'left', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'seats', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjSeatModel($attr);
	}
}
?>