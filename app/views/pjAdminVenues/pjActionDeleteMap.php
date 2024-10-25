<?php
if($tpl['code'] == 200)
{ 
	?>
	<div class="tbUseMapYes">
		<p>
			<label class="title"><?php __('lblSeatsMap'); ?></label>
			<span class="inline_block">
				<input type="file" name="seats_map" id="seats_map" class="pj-form-field w250 required" />
			</span>
		</p>
	</div>
	<?php
}else{
	echo $tpl['code'];
} 
?>