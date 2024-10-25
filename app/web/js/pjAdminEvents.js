var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateEvent = $("#frmCreateEvent"),
			$frmUpdateEvent = $("#frmUpdateEvent"),
			$frmUpdateShow = $("#frmUpdateShow"),
			$frmExportMovies = $("#frmExportMovies"),
			$dialogDelete = $("#dialogDeleteImage"),
			$dialogDeleteShow = $("#dialogDeleteShow"),
			$dialogDuplicated = $("#dialogDuplicated"),
			$dialogShowStatus = $('#dialogShowStatus'),
			multiselect = ($.fn.multiselect !== undefined),
			dialog = ($.fn.dialog !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			remove_arr = new Array();
		
		$(".field-int").spinner({
			min: 0
		});
		if (multiselect) {
			$("#frmUpdateShow .tbSeats").multiselect({
				noneSelectedText: myLabel.choose,
				minWidth: 90,
				close: function(){
					$(this).valid();
					$(this).siblings().removeClass('tbError');
				}
			});
		}
		function setPrices()
		{
			var index_arr = new Array();
				
			$('#fd_size_list').find(".fd-size-row").each(function (index, row) {
				index_arr.push($(row).attr('data-index'));
			});
			$('#index_arr').val(index_arr.join("|"));
		}
		
		if ($frmUpdateShow.length > 0 && validate) {
			$frmUpdateShow.validate({
				errorPlacement: function (error, element) {
					if(element.hasClass('datetimepick'))
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				submitHandler: function(form)
				{
					var post = null;
					var post_arr = new Array(); 
					var fields = 0;
					var max_fields_post = 100;
					var loop = 0;
					var valid = true;
					
					function setBeforeSave(i)
					{
						$.post("index.php?controller=pjAdminEvents&action=pjActionBeforeSave", post_arr[i], callback);
					}
					function callback(data){
						loop++;
						if(loop < post_arr.length)
						{
							setBeforeSave.call(null, [loop]);
						}else{
							$.post("index.php?controller=pjAdminEvents&action=pjActionSave").done(function (data) {
					    	
								if(data.code == '101')
								{
									$dialogShowStatus
										.find(".bxShowStatusStart, .bxShowStatusFail, .bxShowStatusEnd").hide().end()
										.find(".bxShowStatusDuplicate").show();
									
									$dialogShowStatus.dialog("option", "buttons", {
							    		'Close': function () {
							    			$(this).dialog("close");
							    		}
							    	});
								}else if(data.code == '200'){
									$dialogShowStatus
										.find(".bxShowStatusStart, .bxShowStatusFail, .bxShowStatusDuplicate").hide().end()
										.find(".bxShowStatusEnd").show();
							    	
									$dialogShowStatus.dialog("option", "close", function () {
							    		$(this).dialog("option", "buttons", {});
							    		if(data.code == '200')
							    		{
							    			window.location.href="index.php?controller=pjAdminEvents&action=pjActionShow&id="+data.id+"&err=" + data.text;
							    		}
							    	});
									$dialogShowStatus.dialog("option", "buttons", {
							    		'Close': function () {
							    			$(this).dialog("close");
							    		}
							    	});
								}
					    	});
						}
					}
					$("#frmUpdateShow .pj-positive-number").each(function() {
						if($(this).val() == '')
						{
							valid = false;
							$(this).addClass('pj-error-field');
						}else{
							if(Number($(this).val()) < 0 || $.isNumeric($(this).val()) == false)
						    {
						    	valid = false;
						    	$(this).addClass('pj-error-field');
						    }else{
						    	valid = true;
						    	$(this).removeClass('pj-error-field');
						    }
						}
					});
					$("#frmUpdateShow .tbSeats ").each(function() {
						if($(this).val() == null)
						{
							valid = false;
							$(this).siblings().addClass('tbError');
						}
					});
					if(valid == true)
					{
						$dialogShowStatus.dialog("open");
						$frmUpdateShow.find(".pjCbShowTime").each(function(index){
							var $this = $(this);
							
							if($this.hasClass('tbSeats'))
							{
								post_arr.push($this.serialize());
							}else{
								if(post == null)
								{
									post = $this.serialize();
								}else{
									post += '&' + $this.serialize();
								}
								
								fields++;
								if(fields == max_fields_post)
								{
									post_arr.push(post);
									fields = 0;
									post = null;
								}
							}
						});
						
						if(post != null)
						{
							post_arr.push(post);
						}
						if(post_arr.length > 0)
						{
							setBeforeSave.call(null, [loop]);
						}
					}
				}
			});
		}
		if ($dialogShowStatus.length > 0 && dialog) {
			$dialogShowStatus.dialog({
				autoOpen: false,
				modal: true,
				resizable: false,
				draggable: false,
				open: function () {
					$dialogShowStatus
						.find(".bxShowStatusFail, .bxShowStatusEnd, .bxShowStatusDuplicate").hide().end()
						.find(".bxShowStatusStart").show();
				},
				close: function () {
					$(this).dialog("option", "buttons", {});
					//window.location.reload();
				},
				buttons: {}
			});
		}
		if ($frmCreateEvent.length > 0 || $frmUpdateEvent.length > 0) {
			$.validator.addMethod('positiveNumber',
				function (value) { 
		        	return Number(value) > 0;
		    	}, 
		    myLabel.duration_greater_zero);
		}
		if ($frmCreateEvent.length > 0 && validate) {
			$frmCreateEvent.validate({
				rules:{
					"duration": {
						positiveNumber: true
					}
				},
				messages: {
					"duration":{
						positiveNumber: myLabel.duration_greater_zero
					}
				},
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'duration')
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
					if(localeId != undefined)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
				},
				submitHandler: function(form){
					var valid = true,
						localeId = null;
					
					setPrices();
					
					$("#frmCreateEvent .fdRequired").each(function() {
						if($(this).val() == '')
						{
							valid = false;
					    	$(this).addClass('pj-error-field');
					    	if(localeId == null)
					    	{
					    		localeId = $(this).attr('lang');
					    	}
					    	
						}else{
							$(this).removeClass('pj-error-field');
						}
					});
					if(localeId != null)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
					
					
					if(valid == true)
					{
						form.submit();
					}
				}
			});
			
			if(myLabel.locale_array.length > 0)
			{
				var locale_array = myLabel.locale_array;
				for(var i = 0; i < locale_array.length; i++)
				{
					var title = $("#i18n_title_" + locale_array[i]),
						description = $("#i18n_description_" + locale_array[i]);
					title.rules('add', {
						messages: {
					    	required: myLabel.field_required
					    }
					});
					description.rules('add', {
						messages: {
					    	required: myLabel.field_required
					    }
					});
				}
			}
		}
		if ($frmUpdateEvent.length > 0 && validate) {
			$frmUpdateEvent.validate({
				rules:{
					"duration": {
						positiveNumber: true
					}
				},
				messages: {
					"duration":{
						positiveNumber: myLabel.duration_greater_zero
					}
				},
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'duration')
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
					if(localeId != undefined)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
				},
				submitHandler: function(form){
					var valid = true,
						localeId = null;
					
					setPrices();
					$("#frmUpdateProduct .fdRequired").each(function() {
						if($(this).val() == '')
						{
							valid = false;
					    	$(this).addClass('pj-error-field');
					    	if(localeId == null)
					    	{
					    		localeId = $(this).attr('lang');
					    	}
					    	
						}else{
							$(this).removeClass('pj-error-field');
						}
						
					});
					if(localeId != null)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
					
					if(valid == true)
					{
						form.submit();
					}
				}
			});
			if(myLabel.locale_array.length > 0)
			{
				var locale_array = myLabel.locale_array;
				for(var i = 0; i < locale_array.length; i++)
				{
					var title = $("#i18n_title_" + locale_array[i]),
						description = $("#i18n_description_" + locale_array[i]);
					title.rules('add', {
						messages: {
					    	required: myLabel.field_required
					    }
					});
					description.rules('add', {
						messages: {
					    	required: myLabel.field_required
					    }
					});
				}
			}
		}
		
		if ($dialogDelete.length > 0 && dialog) 
		{
			$dialogDelete.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 400,
				buttons: (function () {
					var buttons = {};
					buttons[tbApp.locale.button.delete] = function () {
						$.ajax({
							type: "GET",
							dataType: "json",
							url: $dialogDelete.data('href'),
							success: function (res) {
								if(res.code == 200){
									$('#image_container').remove();
									$dialogDelete.dialog('close');
								}
							}
						});
					};
					buttons[tbApp.locale.button.cancel] = function () {
						$dialogDelete.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		
		if ($dialogDeleteShow.length > 0 && dialog) 
		{
			$dialogDeleteShow.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 400,
				buttons: (function () {
					var buttons = {};
					buttons[tbApp.locale.button.delete] = function () {
						var $this = $(this),
							$link = $this.data("link"),
							$tr = $link.closest("tr"),
							id = $link.data("id");
						
						$.post("index.php?controller=pjAdminEvents&action=pjActionDeleteShow", {
							"id": id
						}).done(function (data) {
							if (data.code === undefined) {
								return;
							}
							switch (data.code) {
								case 200:
									$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
										$tr.remove();
										$this.dialog("close");
									});
									break;
							}
						});
					};
					buttons[tbApp.locale.button.cancel] = function () {
						$dialogDeleteShow.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		if ($dialogDuplicated.length > 0 && dialog) 
		{
			$dialogDuplicated.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 400,
				buttons: (function () {
					var buttons = {};
					buttons[tbApp.locale.button.ok] = function () {
						$dialogDuplicated.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		function onBeforeShow (obj) {
			if (parseInt(obj.cnt_confirmed, 10) > 0) {
				return false;
			}
			return true;
		}
		function formatImage (str, obj) {
			var src = str ? str : 'app/web/img/backend/80x116.png';
			return ['<a href="index.php?controller=pjAdminEvents&action=pjActionUpdate&id=', obj.id ,'"><img src="', src, '" style="width: 80px; display:block;" /></a>'].join("");
		}
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminEvents&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminEvents&action=pjActionDeleteEvent&id={:id}", beforeShow: onBeforeShow},
				          {type: "menu", url: "#", text: '', items:[  
      			                  {text: myLabel.showtimes, url: "index.php?controller=pjAdminEvents&action=pjActionShow&id={:id}"},
    				              {text: myLabel.bookings, url: "index.php?controller=pjAdminEvents&action=pjActionBooking&id={:id}"}
    				           ]}
				          ],
				columns: [{text: myLabel.image, type: "text", sortable: false, editable: false, width: 85, renderer: formatImage},
				          {text: myLabel.title, type: "text", sortable: true, editable: false, width: 300},
				          {text: myLabel.duration, type: "text", sortable: false, editable: false, width: 85},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 90, options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminEvents&action=pjActionGetEvent",
				dataType: "json",
				fields: ['event_img', 'title', 'duration', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminEvents&action=pjActionDeleteEventBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.revert_status, url: "index.php?controller=pjAdminEvents&action=pjActionStatusEvent", render: true},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminEvents&action=pjActionExportEvent", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminEvents&action=pjActionSaveEvent&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		function getSeats(index)
		{
			var venue_id = $('#venue_id_' + index).val(),
				date_time = $('#date_time_' + index).val(),
				event_id = $frmUpdateShow.find("input[name='id']").val();
			$.get("index.php?controller=pjAdminEvents&action=pjActionGetSeats&event_id="+event_id+"&venue_id=" + venue_id + "&date_time=" + date_time + "&index="+index).done(function (data) {
				$('#tbSeatOuter_' + index).html(data);
				$("#frmUpdateShow .tbSeats").multiselect({
					noneSelectedText: myLabel.choose,
					minWidth: 90,
					close: function(){
						$(this).valid();
					}
				});
			});
		}
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminEvents&action=pjActionGetEvent", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminEvents&action=pjActionGetEvent", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-status-1", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			return false;
		}).on("click", ".pj-status-0", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$.post("index.php?controller=pjAdminEvents&action=pjActionSetActive", {
				id: $(this).closest("tr").data("object")['id']
			}).done(function (data) {
				$grid.datagrid("load", "index.php?controller=pjAdminEvents&action=pjActionGetEvent");
			});
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminEvents&action=pjActionGetEvent", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", '.pj-add-size', function(e){
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var clone_text = $('#fd_size_clone').html(),
				index = Math.ceil(Math.random() * 999999),
				number_of_sizes = $('#fd_size_list').find(".fd-size-row").length;
			clone_text = clone_text.replace(/\{INDEX\}/g, 'fd_' + index);
			$('#fd_size_list').append(clone_text);
		}).on("click", '.pj-remove-size', function(e){
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $size = $(this).parent().parent(),
				id = $size.attr('data-index');
			if(id.indexOf("fd") == -1)
			{
				remove_arr.push(id);
			}
			$('#remove_arr').val(remove_arr.join("|"));
			$size.remove();
			
		}).on("click", ".pj-delete-image", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogDelete.data('href', $(this).data('href')).dialog("open");
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				if(!$dp.is('[disabled=disabled]'))
				{
					$dp.trigger("focusin").datepicker("show");
				}
			}
		}).on("focusin", ".datetimepick", function (e) {
			var $this = $(this),
				custom = {},
				o = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev"),
					timeFormat: $this.attr("lang"),
					stepMinute: 5,
					onClose: function(){
						getSeats($this.attr('data-index'));
					}
			};
			$(this).datetimepicker($.extend(o, custom));
		}).on("click", ".btnAddShow", function () {
			var $c = $("#tblShowClone tbody").clone(),
			r = $c.html().replace(/\{INDEX\}/g, 'new_' + Math.ceil(Math.random() * 99999));
			$(this).closest("form").find("table").find("tbody").append(r);
			
			$("#frmUpdateShow .tbSeats").multiselect({
				noneSelectedText: myLabel.choose,
				minWidth: 90,
				close: function(){
					$(this).valid();
				}
			});
		}).on("click", ".lnkRemoveShow", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tr = $(this).closest("tr");
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
			});			
			return false;
		}).on("change", ".tbVenueSelector", function (e) {
			getSeats($(this).attr('data-index'));
			return false;
		}).on("click", ".lnkDeleteShow", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($dialogDeleteShow.length > 0 && dialog) {
				$dialogDeleteShow.data("link", $(this)).dialog("open");
			}
			return false;
		}).on("click", ".lnkNext", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var event_id = $('#frmUpdateShow input[name=id]').val(),
				index = $(this).attr('data-index'),
				$this = $(this),
				date_time = $("#frmUpdateShow input[name='date_time\\["+index+"\\]']").val();
			
			if(date_time != '')
			{
				var post_data = {
				             date_time: $("#frmUpdateShow input[name='date_time\\["+index+"\\]']").val(),
				             venue_id: $("#frmUpdateShow select[name='venue_id\\["+index+"\\]']").val(),
				             price_id: $("#frmUpdateShow select[name='price_id\\["+index+"\\]']").val(),
				             price: $("#frmUpdateShow input[name='price\\["+index+"\\]']").val(),
				             seat_id: $("#frmUpdateShow select[name='seat_id\\["+index+"\\]\\[\\]']").val()
				};
				$.post("index.php?controller=pjAdminEvents&action=pjActionAddShow&event_id="+event_id+"&period=" + $(this).attr('data-period'), post_data).done(function (data) {
					$this.closest("form").find("table").find("tbody").append(data);
					$("#frmUpdateShow .tbSeats").multiselect({
						noneSelectedText: myLabel.choose,
						minWidth: 90,
						close: function(){
							$(this).valid();
						}
					});
				});
			}
			return false;
		}).on("click", ".pj-table-icon-menu", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var diff, lf,
				$this = $(this),
				$list = $this.siblings(".pj-menu-list-wrap");
			diff = Math.ceil( ($list.outerWidth() - $this.outerWidth()) / 2 );
			if (diff > 0) {
				lf = $this.offset().left - diff;
				if (lf < 0) {
					lf = 0;
				}
			} else {
				lf  = $this.offset().left + diff;
			}
			$list.css({
				"top": $this.offset().top + $this.outerHeight() + 2,
				"left": lf
			});
		
			$list.toggle();
			$(".pj-menu-list-wrap").not($list).hide();
			return false;
		}).on("change", "#date", function (e) {
			$('#frmEventBooking').submit();
		}).on("change", "#time", function (e) {
			$('#frmEventBooking').submit();
		});
		
		$(document).on("click", "*", function (e) {
			if(!$(e.target).hasClass('pj-table-icon-menu'))
			{
				$('.pj-menu-list-wrap').hide();
			}
		}).on("change", "#export_period", function (e) {
			var period = $(this).val();
			if(period == 'last')
			{
				$('#last_label').show();
				$('#next_label').hide();
			}else{
				$('#last_label').hide();
				$('#next_label').show();
			}
		}).on("click", "#file", function (e) {
			$('#abSubmitButton').val(myLabel.btn_export);
			$('.abFeedContainer').hide();
			$('.abPassowrdContainer').hide();
		}).on("click", "#feed", function (e) {
			$('.abPassowrdContainer').show();
			$('#abSubmitButton').val(myLabel.btn_get_url);
		}).on("focus", "#movies_feed", function (e) {
			$(this).select();
		});
		
		if ($frmExportMovies.length > 0 && validate) {
			$frmExportMovies.validate({
				rules: {
					"password": {
						required: function(){
							if($('#feed').is(':checked'))
							{
								return true;
							}else{
								return false;
							}
						}
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ".ignore"
			});
		}
	});
})(jQuery_1_8_2);