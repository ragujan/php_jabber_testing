<?php
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
$jqTimeFormat = pjUtil::jqTimeFormat($tpl['option_arr']['o_time_format']);
$index = 'new_' . rand(1, 999999);
$date_time = !empty($tpl['date_time']) ?  pjUtil::formatDate(date('Y-m-d', strtotime($tpl['date_time'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($tpl['date_time'])), 'H:i:s', $tpl['option_arr']['o_time_format']) : '';
?>
<tr id="trShow_<?php echo $index;?>">
	<td>
		<span class="block overflow">
			<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
				<input type="text" id="date_time_<?php echo $index;?>" name="date_time[<?php echo $index;?>]" value="<?php echo $date_time;?>" data-index="<?php echo $index;?>" class="pj-form-field pointer w130 datetimepick required pjCbShowTime" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
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
					?><option value="<?php echo $v['id']?>"<?php echo !empty($_POST['venue_id']) ? ($v['id'] == $_POST['venue_id'] ? ' selected="selected"' : null) : null;?>><?php echo pjSanitize::html($v['name']);?></option><?php
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
				$price_id = '';
				foreach($tpl['price_arr'] as $k => $v)
				{
					?><option value="<?php echo $v['id']?>"<?php echo $v['id'] == $tpl['price_id'] ? ' selected="selected"' : null;?>><?php echo pjSanitize::html($v['name']);?></option><?php
				} 
				?>
			</select>
		</span>
	</td>
	<td>
		<span id="tbSeatOuter_<?php echo $index;?>" class="inline_block">
			<?php
			if(isset($tpl['seat_arr']) && !empty($tpl['seat_arr']))
			{
				?>
				<select name="seat_id[<?php echo $index?>][]" multiple="multiple" size="5" class="pj-form-field tbSeats pjCbShowTime" style="width: 100px;">
					<?php
					foreach($tpl['seat_arr'] as $k => $v)
					{
						$disabled = null;
						if(isset($v['cnt_bookings']) && $v['cnt_bookings'] > 0)
						{
							$disabled = ' disabled="disabled"';
						}
						?><option value="<?php echo $v['id']?>"<?php echo $disabled;?><?php echo is_array($_POST['seat_id']) ? (in_array($v['id'], $_POST['seat_id']) ? ' selected="selected"' : null): null;?>><?php echo pjSanitize::html($v['name'] . ' / ' . $v['seats']);?></option><?php
					} 
					?>
				</select>
				<?php
			}else{
				?>
				<select name="seat_id[<?php echo $index?>][]" multiple="multiple" size="5" class="pj-form-field tbSeats required" style="width: 100px;">
				</select>
				<?php
			} 
			?>
		</span>
	</td>
	<td>
		<span class="pj-form-field-custom pj-form-field-custom-before">
			<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
			<input type="text" name="price[<?php echo $index?>]" class="pj-form-field pj-positive-number w50 pjCbShowTime" value="<?php echo $_POST['price'];?>"/>
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