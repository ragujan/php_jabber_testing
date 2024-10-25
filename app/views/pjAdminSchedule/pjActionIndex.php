<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	
	pjUtil::printNotice(__('infoScheduleTitle', true, false), __('infoScheduleDesc', true, false)); 
	?>
	<div class="b10">
		<a class="pj-button btnFilter float_left inline_block r5" href="javascript:void(0)" rev="<?php echo pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format'])?>" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionPrint&amp;date=[DATE]"><?php __('lblToday');?></a>
		<a class="pj-button btnFilter float_left inline_block r5" href="javascript:void(0)" rev="<?php echo pjUtil::formatDate(date('Y-m-d', time() + (24*60*60)), 'Y-m-d', $tpl['option_arr']['o_date_format']);?>" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionPrint&amp;date=[DATE]"><?php __('lblTomorrow');?></a>
		<span class="pj-form-field-custom pj-form-field-custom-after">
			<input type="text" name="schedule_date" id="schedule_date" class="pj-form-field pointer w80 datepick required" value="<?php echo isset($_GET['date']) && !empty($_GET['date']) ? $_GET['date'] : date($tpl['option_arr']['o_date_format']); ?>" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionPrint&amp;date=[DATE]"/>
			<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
		</span>
		<a class="pj-button btnPrint float_right inline_block" target="_blank" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionPrint&amp;date=<?php echo pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format'])?>"><?php __('lblPrint');?></a>
		<br class="clear_both">
	</div>
	<div class="boxScheduleOuter">
		<div id="pj_schedule_loader"></div>
		<div id="boxSchedule"></div>
	</div>
	<?php
}
?>