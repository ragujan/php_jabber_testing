<?php
if (isset($tpl['locale_arr']) && is_array($tpl['locale_arr']) && !empty($tpl['locale_arr']) && count($tpl['locale_arr']) > 1)
{ 
	
	$locale_id = $controller->pjActionGetLocale();
	$selected_title = null;
	$selected_src = NULL;
	foreach ($tpl['locale_arr'] as $locale)
	{
		if($locale_id == $locale['id'])
		{
			$selected_title = $locale['language_iso'];
			$lang_iso = explode("-", $selected_title);
			if(isset($lang_iso[1]))
			{
				$selected_title = $lang_iso[1];
			}
			if (!empty($locale['flag']) && is_file(PJ_INSTALL_PATH . $locale['flag']))
			{
				$selected_src = PJ_INSTALL_URL . $locale['flag'];
			} elseif (!empty($locale['file']) && is_file(PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $locale['file'])) {
				$selected_src = PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $locale['file'];
			}
			break;
		}
	}
	?>
	<div class="pull-right">
		<div class="btn-group pjCbLocal">
			<a href="#" class="btn btn-default dropdown-toggle pjCbLocalTrigger" data-pj-toggle="dropdown" role="button" aria-expanded="false">
				<img src="<?php echo $selected_src; ?>" alt="">
				<span class="title"><?php echo $selected_title; ?></span>
				<span class="caret"></span>
			</a>
	                
	        <ul class="dropdown-menu pjCbLocalMenu" role="menu">
	        	<?php
	            foreach ($tpl['locale_arr'] as $locale)
	            {
	            	$selected_src = NULL;
	            	if (!empty($locale['flag']) && is_file(PJ_INSTALL_PATH . $locale['flag']))
	            	{
	            		$selected_src = PJ_INSTALL_URL . $locale['flag'];
	            	} elseif (!empty($locale['file']) && is_file(PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $locale['file'])) {
	            		$selected_src = PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $locale['file'];
	            	}
	            	?>
	            		<li>
	            			<a href="#" class="tbSelectorLocale<?php echo $locale_id == $locale['id'] ? ' tbLocaleFocus' : NULL; ?>" data-id="<?php echo $locale['id']; ?>">
	            				<img src="<?php echo $selected_src; ?>" alt="">
								<?php echo pjSanitize::html($locale['name']); ?>
	            			</a>
	            		</li>
	            	<?php
	            }
	            ?>
			</ul>
       	</div>
	</div><!-- /.pull-right -->
	<?php
}
?>