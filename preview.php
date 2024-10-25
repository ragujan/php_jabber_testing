<!DOCTYPE html>
<html>
	<head>
		<title>Cinema Booking System - Preview</title>
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <link href="core/framework/libs/pj/css/pj.bootstrap.min.css" type="text/css" rel="stylesheet" />
	    <link href="index.php?controller=pjFront&action=pjActionLoadCss<?php echo isset($_GET['theme']) ? '&layout=' . $_GET['theme'] : null; ?>" type="text/css" rel="stylesheet" />
	</head>
	<body>
		<div style="max-width:1000px">
			<script type="text/javascript" src="index.php?controller=pjFront&action=pjActionLoad<?php echo isset($_GET['locale']) ? '&locale=' . $_GET['locale'] : NULL;?><?php echo isset($_GET['hide']) ? '&hide=' . $_GET['hide'] : NULL;?>&layout=<?php echo $_GET['theme'];?>"></script>
		</div>        
	</body>
</html>