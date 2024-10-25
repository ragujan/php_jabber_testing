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
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	$jqTimeFormat = pjUtil::jqTimeFormat($tpl['option_arr']['o_time_format']);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionIndex"><?php __('menuEvents'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionExport"><?php __('lblExport'); ?></a></li>
		</ul>
	</div>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblDetails'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionShow&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblShows'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionBooking&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblBookings'); ?></a></li>
		</ul>
	</div>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEvents&amp;action=pjActionShow" method="post" id="frmUpdateShow" class="pj-form form">
		<input type="hidden" name="show_update" value="1" class="pjCbShowTime"/>
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']?>" class="pjCbShowTime"/>
		<?php
		pjUtil::printNotice(__('infoUpdateShowTitle', true), __('infoUpdateShowDesc', true)); 
		?>
		
		<p>
			<label class="title"><?php __('lblEvent'); ?></label>
			<span class="inline_block">
				<label class="content"><?php echo pjSanitize::html($tpl['arr']['title']);?></label>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblDuration'); ?></label>
			<span class="inline_block">
				<label class="content"><?php echo pjSanitize::html($tpl['arr']['duration']);?> <?php __('lblMinutes')?></label>
			</span>
		</p>
		<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
			<thead>
				<tr>
					<th style="width: 170px;"><?php __('lblDateTime'); ?></th>
					<th style="width: 120px;"><?php __('lblVenue'); ?></th>
					<th style="width: 100px;"><?php __('lblTicket'); ?></th>
					<th style="width: 100px;"><?php __('lblSeats'); ?></th>
					<th style="width: 90px;"><?php __('lblPrice'); ?></th>
					<th style="width: 50px;">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(count($tpl['show_arr']) > 0)
				{
					foreach($tpl['show_arr'] as $show)
					{
						$date_time = !empty($show['date_time']) ?  pjUtil::formatDate(date('Y-m-d', strtotime($show['date_time'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($show['date_time'])), 'H:i:s', $tpl['option_arr']['o_time_format']) : '';
						$changed = null;
						if($show['cnt_confirmed'] > 0)
						{
							$changed = ' disabled="disabled"';
						}
						?>
						<tr id="trShow_<?php echo $show['id'];?>">
							<td>
								<span class="block overflow">
									<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
										<input type="text" id="date_time_<?php echo $show['id'];?>"<?php echo $changed;?> name="date_time[<?php echo $show['id'];?>]" data-index="<?php echo $show['id'];?>" value="<?php echo $date_time;?>" class="pj-form-field pointer w130 datetimepick required pjCbShowTime" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
										<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
									</span>
								</span>
							</td>
							<td>
								<span class="inline-block">
									<select id="venue_id_<?php echo $show['id'];?>"<?php echo $changed;?> name="venue_id[<?php echo $show['id'];?>]" class="pj-form-field w120 tbVenueSelector required pjCbShowTime" data-index="<?php echo $show['id'];?>">
										<option value="">-- <?php __('lblChoose'); ?>--</option>
										<?php
										foreach($tpl['venue_arr'] as $v)
										{
											?><option value="<?php echo $v['id']?>"<?php echo $v['id'] == $show['venue_id'] ? ' selected="selected"' : null;?>><?php echo pjSanitize::html($v['name']);?></option><?php
										} 
										?>
									</select>
								</span>
							</td>
							<td>
								<span class="inline-block">
									<select name="price_id[<?php echo $show['id'];?>]"<?php echo $changed;?> class="pj-form-field w100 required pjCbShowTime">
										<option value="">-- <?php __('lblChoose'); ?>--</option>
										<?php
										foreach($tpl['price_arr'] as $v)
										{
											?><option value="<?php echo $v['id']?>"<?php echo $v['id'] == $show['price_id'] ? ' selected="selected"' : null;?>><?php echo pjSanitize::html($v['name']);?></option><?php
										} 
										?>
									</select>
								</span>
							</td>
							<td>
								<span id="tbSeatOuter_<?php echo $show['id'];?>" class="inline_block">
									<select name="seat_id[<?php echo $show['id'];?>][]"<?php echo $changed;?> multiple="multiple" size="5" class="pj-form-field tbSeats pjCbShowTime" style="width: 100px;">
										<?php
										if(is_array($tpl['seat_arr'][$show['venue_id']]) && count($tpl['seat_arr'][$show['venue_id']]) > 0)
										{
											foreach($tpl['seat_arr'][$show['venue_id']] as $sk => $_id)
											{
												$disabled = null;
												if(isset($tpl['booked_id_arr'][$show['id']]) && in_array($_id, $tpl['booked_id_arr'][$show['id']]) && isset($tpl['seat_id_arr'][$show['id']]) && in_array($_id, $tpl['seat_id_arr'][$show['id']]))
												{
													$disabled = ' disabled="disabled"';
												}
												$_title = $tpl['seat_name_arr'][$show['venue_id']][$sk] . ' / ' . $tpl['seat_count_arr'][$show['venue_id']][$sk];
												?><option value="<?php echo $_id;?>"<?php echo in_array($_id, $tpl['seat_id_arr'][$show['id']]) ? ' selected="selected"' : null;?><?php echo $disabled;?>><?php echo pjSanitize::html($_title);?></option><?php
											}
										} 
										?>
									</select>
								</span>
							</td>
							<td>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" name="price[<?php echo $show['id'];?>]"<?php echo $changed;?> class="pj-form-field pj-positive-number w50 pjCbShowTime" value="<?php echo $show['price'];?>"/>
								</span>
							</td>
							<td>
								<?php
								if($show['cnt_confirmed'] == 0)
								{ 
									?><a href="#" class="lnkDeleteShow" data-id="<?php echo $show['id'];?>"></a><?php
								}else{
									?>&nbsp;<?php
								} 
								?>
								<a class="pj-table-icon-menu pj-table-button" href="#" data-id="<?php echo $show['id'];?>"><span class="pj-button-arrow-down"></span></a>
								<span id="pj_menu_<?php echo $show['id'];?>" class="pj-menu-list-wrap" style="display: none;">
									<span class="pj-menu-list-arrow"></span>
									<ul class="pj-menu-list">
										<li><a href="#" data-index="<?php echo $show['id'];?>" data-period="ticket" class="lnkNext"><?php __('lblNextTicketType'); ?></a></li>
										<li><a href="#" data-index="<?php echo $show['id'];?>" data-period="hour" class="lnkNext"><?php __('btnNextHour'); ?></a></li>
										<li><a href="#" data-index="<?php echo $show['id'];?>" data-period="day" class="lnkNext"><?php __('btnNextDay'); ?></a></li>
										<li><a href="#" data-index="<?php echo $show['id'];?>" data-period="week" class="lnkNext"><?php __('btnNextWeek'); ?></a></li>
									</ul>
								</span>
							</td>
						</tr>
						<?php
					}
				}else{
					$index = 'new_' . rand(1, 999999);?>
					<tr id="trShow_<?php echo $index;?>">
						<td>
							<span class="block overflow">
								<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
									<input type="text" id="date_time_<?php echo $index;?>" name="date_time[<?php echo $index;?>]" data-index="<?php echo $index;?>" class="pj-form-field pointer w130 datetimepick required pjCbShowTime" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
									<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
								</span>
							</span>
						</td>
						<td>
							<span class="inline-block">
								<select id="venue_id_<?php echo $index;?>" name="venue_id[<?php echo $index;?>]" class="pj-form-field w120 tbVenueSelector required pjCbShowTime" data-index="<?php echo $index;?>">
									<option value="">-- <?php __('lblChoose'); ?>--</option>
									<?php
									foreach($tpl['venue_arr'] as $v)
									{
										?><option value="<?php echo $v['id']?>"><?php echo pjSanitize::html($v['name']);?></option><?php
									} 
									?>
								</select>
							</span>
						</td>
						<td>
							<span class="inline-block">
								<select name="price_id[<?php echo $index;?>]" class="pj-form-field w100 required pjCbShowTime">
									<option value="">-- <?php __('lblChoose'); ?>--</option>
									<?php
									foreach($tpl['price_arr'] as $v)
									{
										?><option value="<?php echo $v['id']?>"><?php echo pjSanitize::html($v['name']);?></option><?php
									} 
									?>
								</select>
							</span>
						</td>
						<td>
							<span id="tbSeatOuter_<?php echo $index;?>" class="inline_block">
								<select name="seat_id[<?php echo $index;?>][]" multiple="multiple" size="5" class="pj-form-field tbSeats pjCbShowTime" style="width: 100px;">
								</select>
							</span>
						</td>
						<td>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" name="price[<?php echo $index?>]" class="pj-form-field pj-positive-number w50 pjCbShowTime"/>
							</span>
						</td>
						<td>
							<a href="#" class="lnkRemoveShow" data-index="<?php echo $index;?>"></a>
							<a class="pj-table-icon-menu pj-table-button" href="#" data-id="<?php echo $index;?>"><span class="pj-button-arrow-down"></span></a>
							<span id="pj_menu_<?php echo $index;?>" class="pj-menu-list-wrap" style="display: none;">
								<span class="pj-menu-list-arrow"></span>
								<ul class="pj-menu-list">
									<li><a href="#" data-index="<?php echo $index;?>" data-period="ticket" class="lnkNext"><?php __('lblNextTicketType'); ?></a></li>
									<li><a href="#" data-index="<?php echo $index;?>" data-period="hour" class="lnkNext"><?php __('btnNextHour'); ?></a></li>
									<li><a href="#" data-index="<?php echo $index;?>" data-period="day" class="lnkNext"><?php __('btnNextDay'); ?></a></li>
									<li><a href="#" data-index="<?php echo $index;?>" data-period="week" class="lnkNext"><?php __('btnNextWeek'); ?></a></li>
								</ul>
							</span>
						</td>
					</tr>
					<?php
				} 
				?>
			</tbody>
		</table>
		<br/>
		<p>
			<label class="title">&nbsp;</label>
			<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button btnAddShow"/>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminEvents&action=pjActionIndex';" />
		</p>
	</form>
	
	<div id="dialogDeleteShow" style="display: none" title="<?php __('lblDeleteConfirmation');?>"><?php __('lblDeleteShowConfirmation');?></div>
	<div id="dialogDuplicated" style="display: none" title="<?php __('lblDuplicatedShowtimesTitle');?>"><?php __('lblDuplicatedShowtimesDesc');?></div>
	
	<div id="dialogShowStatus" title="<?php echo pjSanitize::html(__('lblStatusTitle', true)); ?>" style="display: none">
		<span class="bxShowStatus bxShowStatusStart" style="display: none"><?php __('lblStatusStart'); ?></span>
		<span class="bxShowStatus bxShowStatusEnd" style="display: none"><?php __('lblStatusEnd'); ?></span>
		<span class="bxShowStatus bxShowStatusDuplicate" style="display: none"><?php __('lblStatusDuplicated'); ?></span>
		<span class="bxShowStatus bxShowStatusFail" style="display: none"><?php __('lblStatusFail'); ?></span>
	</div>
	
	<table id="tblShowClone" style="display: none">
		<tbody>
			<tr id="trShow_{INDEX}">
				<td>
					<span class="block overflow">
						<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
							<input type="text" id="date_time_{INDEX}" name="date_time[{INDEX}]" data-index="{INDEX}" class="pj-form-field pointer w130 datetimepick required pjCbShowTime" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</span>
				</td>
				<td>
					<span class="inline-block">
						<select id="venue_id_{INDEX}" name="venue_id[{INDEX}]" class="pj-form-field w120 tbVenueSelector required pjCbShowTime" data-index="{INDEX}">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach($tpl['venue_arr'] as $v)
							{
								?><option value="<?php echo $v['id']?>"><?php echo pjSanitize::html($v['name']);?></option><?php
							} 
							?>
						</select>
					</span>
				</td>
				<td>
					<span class="inline-block">
						<select name="price_id[{INDEX}]" class="pj-form-field w100 required pjCbShowTime">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach($tpl['price_arr'] as $v)
							{
								?><option value="<?php echo $v['id']?>"><?php echo pjSanitize::html($v['name']);?></option><?php
							} 
							?>
						</select>
					</span>
				</td>
				<td>
					<span id="tbSeatOuter_{INDEX}" class="inline_block">
						<select name="seat_id[{INDEX}][]" multiple="multiple" size="5" class="pj-form-field tbSeats pjCbShowTime" style="width: 100px;">
						</select>
					</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" name="price[{INDEX}]" class="pj-form-field pj-positive-number w50 pjCbShowTime"/>
					</span>
				</td>
				<td>
					<a href="#" class="lnkRemoveShow" data-index="{INDEX}"></a>
					<a class="pj-table-icon-menu pj-table-button" href="#" data-id="{INDEX}"><span class="pj-button-arrow-down"></span></a>
					<span id="pj_menu_{INDEX}" class="pj-menu-list-wrap" style="display: none;">
						<span class="pj-menu-list-arrow"></span>
						<ul class="pj-menu-list">
							<li><a href="#" data-index="{INDEX}" data-period="ticket" class="lnkNext"><?php __('lblNextTicketType'); ?></a></li>
							<li><a href="#" data-index="{INDEX}" data-period="hour" class="lnkNext"><?php __('btnNextHour'); ?></a></li>
							<li><a href="#" data-index="{INDEX}" data-period="day" class="lnkNext"><?php __('btnNextDay'); ?></a></li>
							<li><a href="#" data-index="{INDEX}" data-period="week" class="lnkNext"><?php __('btnNextWeek'); ?></a></li>
						</ul>
					</span>
				</td>
		</tbody>
	</table>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.choose = "-- <?php __('lblChoose'); ?> --";
	</script>
	<?php
}
?>