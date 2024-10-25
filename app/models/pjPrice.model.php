<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPriceModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'prices';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'event_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public $i18n = array('price_name');
	
	public static function factory($attr=array())
	{
		return new pjPriceModel($attr);
	}
}
?>