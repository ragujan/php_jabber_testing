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
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true, false);
		$bodies = __('error_bodies', true, false);
		
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVenues&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblDetails'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVenues&amp;action=pjActionSector&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblSectors'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoSectorTitle', true, false), __('infoSectorDesc', true, false)); 
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVenues&amp;action=pjActionSector" method="post" id="frmUpdateSector" class="pj-form form"">
		<input type="hidden" name="venue_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']?>" />
		
		<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
			<thead>
				<tr>
					<th class="w30">ID</th>
					<th class="w450">Name</th>
					<th class="w200">Seats</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach($tpl['seat_arr'] as $v)
				{
					?>
					<tr>
						<td><?php echo pjSanitize::clean($v['id']);?></td>
						<td><input type="text" name="m_name[<?php echo $v['id']; ?>]" value="<?php echo pjSanitize::clean($v['name']); ?>" class="pj-form-field w400 required" /></td>
						<td><input type="text" name="m_seats[<?php echo $v['id']; ?>]" value="<?php echo (int) $v['seats']; ?>" class="pj-form-field pj-seat-field w60 required" /></td>
					</tr>
					<?php
				} 
				?>
			</tbody>
		</table>
		
		<p>
			<span class="inline_block">
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button float_left r5" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminVenues&action=pjActionIndex';" />
			</span>
		</p>
		
	</form>
	
	<script type="text/javascript">
	var locale_array = new Array(); 
	var myLabel = myLabel || {};
	myLabel.field_required = "<?php __('tb_field_required'); ?>";	
	</script>
	<?php
}
?>