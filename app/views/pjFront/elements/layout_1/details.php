<?php
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
$months = __('months', true);
$short_months = __('short_months', true);
ksort($months);
ksort($short_months);
$days = __('days', true);
$short_days = __('short_days', true);
$STORE = @$_SESSION[$controller->defaultStore];
?>
<br/>
<div class="container-fluid">
	<div class="panel panel-default pjCbMain">
		<div class="panel-heading clearfix pjCbHeading">
			<?php include_once PJ_VIEWS_PATH . 'pjFront/elements/layout_1/locale.php';?>

			<a href="#" class="btn btn-link text-muted tbBackToEvents pjCbBtnBack" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['hash_date'])); ?>">
				<span class="text-muted">
					<i class="fa fa-arrow-left"></i>
					<?php __('front_back');?>
				</span>
			</a>
		</div><!-- /.panel-heading clearfix pjCbHeading -->
		<div class="panel-body pjCbBody pjCbMovieInfo">
			<div class="row">
				<div class="col-md-3 col-sm-3 col-xs-12">
					<?php
					$src = 'https://placehold.it/220x320';
					if(!empty($tpl['arr']['event_img']) && is_file(PJ_INSTALL_PATH . $tpl['arr']['event_img']))
					{
						$src = PJ_INSTALL_URL . $tpl['arr']['event_img'];
					} 
					?>
					<img src="<?php echo $src;?>" class="img-responsive" alt="Responsive image">
				</div><!-- /.col-md-3 col-sm-3 col-xs-12 -->
				<div class="col-md-9 col-sm-9 col-xs-12">
					<h1 class="pjCbMovieTitle"><?php echo pjSanitize::html($tpl['arr']['title']);?></h1>
					<ul class="list-inline pjCbMovieMeta">
						<li><strong><?php __('front_running_time')?>:</strong></li>
						<li><?php echo $tpl['arr']['duration']?> <?php __('front_minutes')?></li>
					</ul><!-- /.list-inline pjCbMovieMeta -->
					<p><?php echo nl2br(stripslashes($tpl['arr']['description']));?></p>
				</div><!-- /.col-md-9 col-sm-9 col-xs-12 -->
			</div><!-- /.row -->
		</div><!-- /.panel-body pjCbBody pjCbMovieInfo -->
		
		<div class="panel-footer pjCbFoot">
			<div class="row">
				<div class="col-lg-offset-2 col-lg-7 col-md-offset-1 col-md-8 col-sm-7 col-xs-12">
					<form id="tbDetailsForm_<?php echo $_GET['index'];?>" action="#" method="post" class="form-inline pjCbFormDate">
						<input type="hidden" name="back_to" value="details"/>
						<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>"/>
						<div class="form-group col-sm-6">
							<label for="" class="control-label"><?php __('front_select_date')?>:</label>
							<div class="input-group date col-lg-7 col-md-8 col-sm-12">
								<input type="text" name="selected_date" readonly="readonly" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['selected_date'])); ?>" class="form-control tbSelectorDatepick" data-id="<?php echo $tpl['arr']['id'];?>" data-list="0" data-dformat="<?php echo $jqDateFormat; ?>" data-fday="<?php echo $week_start; ?>" data-months="<?php echo join(',', $months);?>" data-shortmonths="<?php echo join(',', $short_months);?>" data-day="<?php echo join(',', $days);?>" data-daymin="<?php echo join(',', $short_days);?>">
								<span class="input-group-addon tbSelectorDatepickIcon">
									<i class="fa fa-calendar"></i>
								</span>
							</div><!-- /.input-group date col-lg-7 col-md-8 col-sm-12 -->
						</div><!-- /.form-group col-sm-6 -->
							<div id="tbTimeContainer_<?php echo $_GET['index'];?>" class="form-group col-sm-6">
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
												?><option value="<?php echo $v?>"<?php echo $v == @$STORE['selected_time'] ? ' selected="selected"' : null;?>><?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['selected_date'] . ' ' . $v)); ?></option><?php
											} 
											?>
										</select>
									</div><!-- /.input-group col-lg-7 col-md-7 col-sm-12  -->
									<?php
								}else{
									__('front_no_shows_on_selected_date');
								} 
								?>
							</div><!-- /.form-group col-sm-6 -->
					</form><!-- /.form-inline -->
				</div><!-- /.col-lg-offset-2 col-lg-7 col-md-offset-2 col-md-8 col-sm-7 col-xs-12 -->

				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 text-center">
					<button class="btn btn-default tbSelectorButton tbSelectorButtonPurchase pjCbBtn pjCbBtnPrimary" style="display:<?php echo !empty($tpl['time_arr']) ? ' block': 'none';?>;" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['hash_date'])); ?>"><?php __('front_button_purchase_tickets');?></button>
				</div><!-- /.col-lg-3 col-md-3 col-sm-3 col-xs-12 text-center -->
			</div><!-- /.row -->
		</div><!-- /.panel-footer pjCbFoot -->
	</div><!-- /.panel panel-default pjCbMain -->
</div><!-- /.container-fluid -->