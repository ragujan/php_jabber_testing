<select name="date_time" id="date_time" class="pj-form-field w300 required">
	<option value="">-- <?php __('lblChoose'); ?>--</option>
	<?php
	foreach($tpl['show_arr'] as $v)
	{
		?><option value="<?php echo $v['date_time']?>" data-venue_id="<?php echo $v['venue_id']?>"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['date_time'])); ?>, <?php echo date($tpl['option_arr']['o_time_format'], strtotime($v['date_time'])); ?>, <?php echo pjSanitize::html($v['venue_name']);?></option><?php
	} 
	?>
</select>