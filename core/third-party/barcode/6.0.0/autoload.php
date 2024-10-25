<?php
function barcode_autoload($className)
{
	$paths = array(
		dirname(__FILE__) . '/' . $className . '.php',
		dirname(__FILE__) . '/Drawer/' . $className . '.php',
	);
	
	foreach ($paths as $filename)
	{
		if (file_exists($filename))
		{
			require $filename;
			return;
		}
	}
}
spl_autoload_register('barcode_autoload');