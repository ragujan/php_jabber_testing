<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjVenueModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'venues';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'map_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'mime_type', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'map_name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'seats_count', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
		return new pjVenueModel($attr);
	}
}
?>