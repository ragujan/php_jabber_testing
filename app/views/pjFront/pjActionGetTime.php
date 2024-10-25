<?php
if(!empty($tpl['time_arr']))
{
	?>
	<label for="" class="control-label"><?php __('front_select_time')?>:</label>
	<div class="input-group col-lg-7 col-md-7 col-sm-12 ">
		<select name="selected_time" class="form-control">
			<?php
			foreach($tpl['time_arr'] as $v)
			{
				?><option value="<?php echo $v?>"><?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['selected_date'] . ' ' . $v)); ?></option><?php
			} 
			?>
		</select>
	</div><!-- /.input-group col-lg-7 col-md-7 col-sm-12  -->
	<?php
}else{
	?>
	<p class="pjCbMovieEmptyDay"><?php __('front_no_shows_on_selected_date');?></p>
	<?php
} 
?>