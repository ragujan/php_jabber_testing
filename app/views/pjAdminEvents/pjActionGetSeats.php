<?php
if(isset($tpl['arr']) && !empty($tpl['arr']))
{
	?>
	<select name="seat_id[<?php echo $_GET['index']?>][]" multiple="multiple" size="5" class="pj-form-field tbSeats required pjCbShowTime" style="width: 100px;">
		<?php
		foreach($tpl['arr'] as $k => $v)
		{
			$disabled = null;
			if(isset($v['cnt_bookings']) && $v['cnt_bookings'] > 0)
			{
				$disabled = ' disabled="disabled"';
			}
			?><option value="<?php echo $v['id']?>"<?php echo $disabled;?>><?php echo pjSanitize::html($v['name'] . '/' . $v['seats']);?></option><?php
		} 
		?>
	</select>
	<?php
}else{
	?>
	<select name="seat_id[<?php echo $_GET['index']?>][]" multiple="multiple" size="5" class="pj-form-field tbSeats required pjCbShowTime" style="width: 100px;">
	</select>
	<?php
}
?>