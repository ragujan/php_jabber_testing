var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreateBooking = $("#frmCreateBooking"),
			$frmUpdateBooking = $("#frmUpdateBooking"),
			$frmResendConfirm = $('#frmResendConfirm'),
			$dialogSelect = $("#dialogSelect"),
			$dialogTicketConfirmation = $('#dialogTicketConfirmation'),
			validate = ($.fn.validate !== undefined),
			chosen = ($.fn.chosen !== undefined);
			dialog = ($.fn.dialog !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			tabs = ($.fn.tabs !== undefined),
			$tabs = $("#tabs"),
			tOpt = {
				select: function (event, ui) {
					$(":input[name='tab_id']").val(ui.panel.id);
				}
			},
			current_ticket = null,
			has_map = 1;
			
		if ($tabs.length > 0 && tabs) 
		{
			$tabs.tabs(tOpt);
		}
		if (chosen) 
		{
			$("#event_id").chosen();
			$("#filter_event_id").chosen();
			$("#c_country").chosen();
		}
		if($frmResendConfirm.length > 0)
		{
			tinymce.init({
			    selector: "textarea.mceEditor",
			    theme: "modern",
			    relative_urls : false,
				remove_script_host : false,
				convert_urls : true,
			    width: 500,
			    plugins: [
			         "advlist autolink link image lists charmap print preview hr anchor pagebreak",
			         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			         "save table contextmenu directionality emoticons template paste textcolor"
				],
				toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons"
			});
		}
		if ($frmCreateBooking.length > 0 && validate) {
			$frmCreateBooking.validate({
				rules: {
					"uuid": {
						required: true,
						remote: "index.php?controller=pjAdminBookings&action=pjActionCheckUniqueId"
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var index = $(validator.errorList[0].element, this).closest("div[id^='tabs-']").index();
				    	if ($tabs.length > 0 && tabs && index !== -1) {
				    		$tabs.tabs(tOpt).tabs("option", "active", index-1);
				    	}
				    };
				},
				submitHandler: function (form) {
					if(seatValidation() == true)
					{
						form.submit();
					}else{
						$tabs.tabs(tOpt).tabs("option", "active", 0);
					}
					return false;
				}
			});
			
		}
		if ($frmUpdateBooking.length > 0 && validate) {
			$frmUpdateBooking.validate({
				rules: {
					"uuid": {
						required: true,
						remote: "index.php?controller=pjAdminBookings&action=pjActionCheckUniqueId&id=" + $frmUpdateBooking.find("input[name='id']").val()
					}
				},
				messages: {
					"uuid":{
						remote: myLabel.existing_id
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var index = $(validator.errorList[0].element, this).closest("div[id^='tabs-']").index();
				    	if ($tabs.length > 0 && tabs && index !== -1) {
				    		$tabs.tabs(tOpt).tabs("option", "active", index-1);
				    	}
				    };
				},
				submitHandler: function (form) {
					if(seatValidation() == true)
					{
						form.submit();
					}else{
						$tabs.tabs(tOpt).tabs("option", "active", 0);
					}
					return false;
				}
			});
			has_map = parseInt($('#has_map').val(), 10);
			
			$frmUpdateBooking.on("click", ".btnCreateInvoice", function () {
				$("#frmCreateInvoice").trigger("submit");
			});
		}
		
		if($dialogSelect.length > 0)
		{
			var $frm = null;
			if ($frmCreateBooking.length > 0) 
			{
				$frm = $frmCreateBooking;
			}
			if ($frmUpdateBooking.length > 0) 
			{
				$frm = $frmUpdateBooking;
			}	
			$dialogSelect.dialog({
				autoOpen: false,
				resizable: false,
				draggable: false,
				modal: true,
				width: 850,
				open: function (){
					if($('#reload_map').val() == '1')
					{
						$.post("index.php?controller=pjAdminBookings&action=pjActionGetSeats", $frm.serialize()).done(function (data) {
							$dialogSelect.html(data);
							adviseToSelectSeats();
						});
					}else{
						adviseToSelectSeats();
					}
				},
				buttons: (function () {
					var buttons = {};
					buttons[tbApp.locale.button.ok] = function () {
						$('#tbCopiedSeats').html($('#tbSelectedSeats').html());
						if ($frmUpdateBooking.length > 0){
							$('#reload_map').val(0);
						}
						$dialogSelect.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		function seatValidation()
		{
			var total_tickets = 0,
				total_seats = 0
				$seatContainer = $('#tbSelectedSeats');
			if(has_map == 0)
			{
				$seatContainer = $('#tbCopiedSeats');
			}
			$('.tbTicketSelector').each(function(i, el){
				total_tickets += parseInt($(el).val(), 10);
			});
			total_seats	= $seatContainer.find('.tbAssignedSeats').length;
			if(total_seats == 0)
			{
				total_seats	= $seatContainer.find('.tbAssignedNoMap').length;
			}
			if(total_seats < total_tickets)
			{
				$('.tbSeatValidation').show();
				return false;
			}else{
				$('.tbSeatValidation').hide();
				return true;
			}
		}
		function checkHasPriceId(id, arr)
		{
			for (var index in arr) 
			{
				if(arr[index] == id)
				{
					return true
				}	
			}
			return false;
		}
		function checkAssignedSeats()
		{
			var $seatContainer = $('#tbSelectedSeats');
			if($seatContainer.find('.tbAssignedSeats').length > 0)
			{
				$('.tbAskToSelectSeats').hide();
				$('.tbTipToRemoveSeats').show();
			}else{
				$('.tbAskToSelectSeats').show();
				$('.tbTipToRemoveSeats').hide();
			}
		}
		function adviseToSelectSeats()
		{
			var total_ticket = 0,
				ticket_arr = {},
				$seatContainer = $('#tbSelectedSeats');
			
			$(".tbTicketSelector").each(function (i, el) {
				var price_id = $(el).attr('data-id'),
					value = parseInt($(el).val(), 10),
					ticket_name = $(el).attr('data-ticket');
				if(value > 0)
				{
					ticket_arr[price_id] = {'cnt': value, 'name': ticket_name};
				}
				total_ticket += value;
			});
			$.each(ticket_arr, function (price_id, pair) {
				if($seatContainer.find(".tbAssignedSeats_" + price_id).length < pair.cnt)
				{
					current_ticket = price_id;
					var guide_message = '';
					if(pair.cnt > 1)
					{
						guide_message = myLabel.guide_msg.select_seats_for;
					}else{
						guide_message = myLabel.guide_msg.select_seat_for;
					}
					guide_message = guide_message.replace(/\{tickets\}/g, pair.cnt + ' ' + pair.name);
					$('.tbSelectSeatGuide').removeClass('success').addClass('info').html(guide_message).show();
					
					return false;
				}
			});
			if(total_ticket == 0)
			{
				current_ticket = null;
				$('.tbSelectSeatGuide').html('').hide();
				
			}else if(total_ticket == $seatContainer.find(".tbAssignedSeats").length){
				current_ticket = null;
				var msg = myLabel.guide_msg.continue;
				msg = msg.replace(/\{STAG\}/g, '<a href="#" class="alert-link tbContinueLink"><strong>');
				msg = msg.replace(/\{ETAG\}/g, '</strong></a>');
				$('.tbSelectSeatGuide').removeClass('info').addClass('success').html(msg).show();
				$('.tbSeatValidation').hide();
			}
		}
		function clearSelection()
		{
			$('#tbSeatsForm').html('');
			$('#tbCopiedSeats').html('');
			$('#tbAssignedSeats').html('');
		}
		function calculatePrices()
		{
			var sub_total = 0,
				tax = 0,
				total = 0,
				deposit = 0,
				total_tickets = 0,
				arr = [],
				$seatContainer = $('#tbSelectedSeats');
			
			if(has_map == 0)
			{
				$seatContainer = $('#tbCopiedSeats');
			}
			$('.tbTicketSelector').each(function(i, el){
				
				var val = parseInt($(el).val(), 10),
					price = parseFloat($(el).attr('data-price'));
				total_tickets += val;
				
				sub_total += val * price;
			});
			
			if(total_tickets == 0)
			{
				$('#sub_total').val('');
				$('#tax').val('');
				$('#total').val('');
				$('#deposit').val('');
			}else{
				tax = (parseFloat($('#tax').attr('data-tax')) * sub_total) / 100;
				total = sub_total + tax;
				deposit = (parseFloat($('#deposit').attr('data-deposit')) * total) / 100;
				
				$('#sub_total').val(sub_total.toFixed(2));
				$('#tax').val(tax.toFixed(2));
				$('#total').val(total.toFixed(2));
				$('#deposit').val(deposit.toFixed(2));
			}
		}
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminBookings&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBooking&id={:id}"},
						 ],
				columns: [
				          {text: myLabel.name, type: "text", sortable: true, editable: false, width: 110},
				          {text: myLabel.tickets, type: "text", sortable: true, editable: false},
				          {text: myLabel.event, type: "text", sortable: true, editable: false, width: 140},
				          {text: myLabel.date_time, type: "text", sortable: true, width: 120},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 100, options: [
				                                                                                     {label: myLabel.pending, value: "pending"}, 
				                                                                                     {label: myLabel.confirmed, value: "confirmed"},
				                                                                                     {label: myLabel.cancelled, value: "cancelled"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString,
				dataType: "json",
				fields: ['c_name', 'tickets', 'title', 'date_time', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBookingBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminBookings&action=pjActionExportBooking", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminBookings&action=pjActionSaveBooking&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}	
		
		if ($("#grid_invoices").length > 0 && datagrid) 
		{
			function formatTotal(val, obj) {
				return obj.total_formated;
			}
			function formatDefault (str) {
				return myLabel[str] || str;
			}
			function formatId (str) {
				return ['<a href="index.php?controller=pjInvoice&action=pjActionUpdate&id=', str, '">#', str, '</a>'].join("");
			}
			function formatCreated(str) {
				if (str === null || str.length === 0) {
					return myLabel.empty_datetime;
				}
				
				if (str === '0000-00-00 00:00:00') {
					return myLabel.invalid_datetime;
				}
				
				if (str.match(/\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}/) !== null) {
					var x = str.split(" "),
						date = x[0],
						time = x[1],
						dx = date.split("-"),
						tx = time.split(":"),
						y = dx[0],
						m = parseInt(dx[1], 10) - 1,
						d = dx[2],
						hh = tx[0],
						mm = tx[1],
						ss = tx[2];
					return $.datagrid.formatDate(new Date(y, m, d, hh, mm, ss), pjGrid.jsDateFormat + ", hh:mm:ss");
				}
			}
			var $grid_invoices = $("#grid_invoices").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjInvoice&action=pjActionUpdate&id={:id}", title: "Edit"},
				          {type: "delete", url: "index.php?controller=pjInvoice&action=pjActionDelete&id={:id}", title: "Delete"}],
				columns: [
				    {text: myLabel.num, type: "text", sortable: true, editable: false, renderer: formatId},
				    {text: myLabel.order_id, type: "text", sortable: true, editable: false},
				    {text: myLabel.issue_date, type: "date", sortable: true, editable: false, renderer: $.datagrid._formatDate, dateFormat: pjGrid.jsDateFormat},
				    {text: myLabel.due_date, type: "date", sortable: true, editable: false, renderer: $.datagrid._formatDate, dateFormat: pjGrid.jsDateFormat},
				    {text: myLabel.created, type: "text", sortable: true, editable: false, renderer: formatCreated},
				    {text: myLabel.status, type: "text", sortable: true, editable: false, renderer: formatDefault},	
				    {text: myLabel.total, type: "text", sortable: true, editable: false, align: "right", renderer: formatTotal}
				],
				dataUrl: "index.php?controller=pjInvoice&action=pjActionGetInvoices&q=" + $frmUpdateBooking.find("input[name='uuid']").val(),
				dataType: "json",
				fields: ['id', 'order_id', 'issue_date', 'due_date', 'created', 'status', 'total'],
				paginator: {
					actions: [
					   {text: myLabel.delete_title, url: "index.php?controller=pjInvoice&action=pjActionDeleteBulk", render: true, confirmation: myLabel.delete_body}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-hover").siblings(".pj-button").removeClass("pj-button-hover");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: "",
				uuid: "",
				c_name: "",
				c_email: "",
				event_id: "",
				from_ticket: "",
				to_ticket: "",
				from_price: "",
				to_price: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString, "created", "DESC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString, "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val(),
				uuid: "",
				c_name: "",
				c_email: "",
				event_id: "",
				from_ticket: "",
				to_ticket: "",
				from_price: "",
				to_price: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString, "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-button-detailed, .pj-button-detailed-arrow", function (e) {
			e.stopPropagation();
			$(".pj-form-filter-advanced").toggle();
		}).on("submit", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var obj = {},
				$this = $(this),
				arr = $this.serializeArray(),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			for (var i = 0, iCnt = arr.length; i < iCnt; i++) {
				obj[arr[i].name] = arr[i].value;
			}
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString, "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("reset", ".frm-filter-advanced", function (e) {
			$(".pj-button-detailed").trigger("click");
			if (chosen) {
				$("#event_id").val('').trigger("liszt:updated");
			}
		}).on("change", "#event_id", function (e) {
			$('.pj-loader').css('display', 'block');
			$.get("index.php?controller=pjAdminBookings&action=pjActionGetShows&event_id=" + $(this).val()).done(function (data) {
				$('#boxShow').html(data);
				$('#ticketBox').html("");
				$('#reload_map').val(1);
				$('#seatsBox').hide();
				$('.pj-loader').css('display', 'none');
			});
		}).on("change", "#date_time", function (e) {
			var $frm = null,
				id = null;
			if($frmCreateBooking.length > 0)
			{
				$frm = $frmCreateBooking;
			}
			if($frmUpdateBooking.length > 0)
			{
				$frm = $frmUpdateBooking;
				id = $frm.find("input[name='id']").val();
			}
			$('.pj-loader').css('display', 'block');
			$.get("index.php?controller=pjAdminBookings&action=pjActionGetTickets&event_id=" + $frm.find("select[name='event_id']").val() + "&date_time=" + $(this).val() + "&venue_id=" + $('option:selected', this).attr('data-venue_id') + "&id=" + id).done(function (data) {
				$('#ticketBox').html(data.ticket);
				$('#venue_id').val(data.venue_id);
				$('#reload_map').val(1);
				$('#seatsBox').hide();
				clearSelection();
				has_map = parseInt(data.has_map, 10);
				calculatePrices();
				$('.pj-loader').css('display', 'none');
			});
		}).on("change", ".tbTicketSelector", function (e) {
			var total_tickets = 0;
			
			$('.tbTicketSelector').each(function(i, el){
				total_tickets += parseInt($(el).val(), 10);
			});
			if(total_tickets > 0)
			{
				$('#seatsBox').show();
			}else{
				current_ticket = null;
				$('#seatsBox').hide();
			}
			var $frm =$('#tbSeatsForm'),
				$mapHolder =$('#tbMapHolder'),
				$seatContainer =$('#tbSelectedSeats'),
				price_id =$(this).attr('data-id'),
				value = parseInt($(this).val(), 10),
				el_arr = [],
				cnt_seats = 0;
			if(has_map == 0)
			{
				$('.tb-select-seats').hide();
				$seatContainer = $('#tbCopiedSeats');
			}else{
				$('.tb-select-seats').show();
				adviseToSelectSeats();
			}
			
			if(has_map == 0)
			{
				$mapHolder.find(".tbSeatSelected").each(function (i, el) {
					var price_id_arr = ($(el).attr('data-price-id')).split('~:~'),
						seat_id =$(el).attr('data-id');
					if(checkHasPriceId(price_id, price_id_arr))
					{
						var can_removed = false;
						$seatContainer.find(".tbAssignedSeats_" + price_id).each(function (indx, element) {
							if($(element).attr('data_seat_id') == seat_id &&$(element).attr('data_price_id') == price_id)
							{
								can_removed = true;
							}
						});
						if(can_removed == true)
						{
							if(!$(el).hasClass('tbSeatAvailable'))
							{
								$(el).addClass('tbSeatAvailable');
							}
							$(el).removeClass('tbSeatSelected');
						}
					}
				});
				$seatContainer.find(".tbAssignedSeats_" + price_id).remove();
				$frm.find(".tbHiddenSeat_" + price_id).remove();
			}
			$frm.find(".tbHiddenSeat_" + price_id).each(function (i, el) {
				el_arr.push($(el));
				cnt_seats += parseInt($(el).val(), 10);
			});
			if(value == 0)
			{
				$mapHolder.find(".tbSeatSelected").each(function (i, el) {
					var price_id_arr = ($(el).attr('data-price-id')).split('~:~');
					if(checkHasPriceId(price_id, price_id_arr) == true && $seatContainer.find(".tbAssignedSeats_" + price_id).length > 0)
					{
						if(!$(el).hasClass('tbSeatAvailable'))
						{
							$(el).addClass('tbSeatAvailable');
						}
						$(el).removeClass('tbSeatSelected');
					}
				});
				$frm.find(".tbHiddenSeat_" + price_id).remove();
				$seatContainer.find(".tbAssignedSeats_" + price_id).remove();
				
			}else if(cnt_seats > value){
				
				while(cnt_seats > value)
				{
					var $removal = el_arr.pop(),
						seat_id = $removal.attr('data_seat_id'),
						val = $removal.val();
					
					if((cnt_seats - value) >= val)
					{
						cnt_seats -= val;
						$mapHolder.find(".tbSeatSelected").each(function (i, el) {
							var price_id_arr = ($(el).attr('data-price-id')).split('~:~');
							if(checkHasPriceId(price_id, price_id_arr) == true && $seatContainer.find(".tbAssignedSeats_" + price_id).length > 0 &&$(el).attr('data-id') == seat_id)
							{
								if(!$(el).hasClass('tbSeatAvailable'))
								{
									$(el).addClass('tbSeatAvailable');
								}
								$(el).removeClass('tbSeatSelected');
							}
						});
						$seatContainer.find(".tbAssignedSeats").each(function (i, el) {
							if($(el).attr('data_seat_id') == seat_id &&$(el).attr('data_price_id') == price_id)
							{
								$(el).remove();
							}
						});
						$('#tbCopiedSeats').html($('#tbSelectedSeats').html());
						$removal.remove();
					}else{
						var tmp = cnt_seats - value;
						cnt_seats -= tmp;
						$removal.val(val - tmp);
						$mapHolder.find(".tbSeatSelected").each(function (i, el) {
							var price_id_arr = ($(el).attr('data-price-id')).split('~:~');
							if(checkHasPriceId(price_id, price_id_arr) == true && $seatContainer.find(".tbAssignedSeats_" + price_id).length > 0  &&$(el).attr('data-id') == seat_id)
							{
								if(!$(el).hasClass('tbSeatAvailable'))
								{
									$(el).addClass('tbSeatAvailable');
								}
							}
						});
						
						$seatContainer.find(".tbAssignedSeats").each(function (i, el) {
							if(tmp > 0 &&$(el).attr('data_seat_id') == seat_id &&$(el).attr('data_price_id') == price_id)
							{
								$(el).remove();
								tmp--;
							}
						});
						$('#tbCopiedSeats').html($('#tbSelectedSeats').html());
					}
				}
			}else{
				if(has_map == 0)
				{
					current_ticket = price_id;
					$mapHolder.find(".tbSeatAvailable").each(function (i, el) {
						var price_id_arr = ($(el).attr('data-price-id')).split('~:~');
						if(checkHasPriceId(price_id, price_id_arr) == true)
						{
							if(value > 0)
							{
								var cnt = parseInt($(el).attr('data-count'), 10),
									tmp = value - cnt;
								
								if(cnt == 1)
								{
									$(el).trigger('click');
									value--;
								}else if(cnt > 1){
									if(tmp >= 0)
									{
										for(var i = 1; i<= cnt; i++)
										{
											$(el).trigger('click');
											value--;
										}
									}else{
										var number_of_tickers = value;
										for(var i = 1; i<= number_of_tickers; i++)
										{
											$(el).trigger('click');
											value--;
										}
									}
								}
							}
						}
					});
				}
			}
			calculatePrices();
		}).on("click", ".tb-select-seats", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogSelect.dialog('open');
		}).on("click", ".tbSeatAvailable", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $frm = $('#tbSeatsForm'),
				$seatContainer = $('#tbSelectedSeats'),
				price_id_arr = ($(this).attr('data-price-id')).split('~:~'),
				seat_id = $(this).attr('data-id'),
				seat_name = $(this).attr('data-name'),
				cnt = parseInt($(this).attr('data-count'), 10);
			if(has_map == 0)
			{
				$seatContainer = $('#tbCopiedSeats');
			}
			if(current_ticket != null && checkHasPriceId(current_ticket, price_id_arr) == true)
			{
				var price_id = current_ticket,
					$ticket = $('#tbTicket_' + price_id),
					chosen_ticket = parseInt($ticket.val(), 10),
					ticket_name = $ticket.attr('data-ticket');
				
				if(chosen_ticket > 0)
				{
					var cnt_selected = 0;
					
					$frm.find(".tbHiddenSeat_" + price_id).each(function (i, el) {
						cnt_selected += parseInt($(el).val(), 10);
					});
					
					if(cnt_selected < chosen_ticket)
					{
						var $el = $("#tbSeatsForm input[name='seat_id\\["+price_id+"\\]\\["+seat_id+"\\]']"),
							seatClass = 'tbAssignedSeats';
						if(has_map == 0)
						{
							seatClass = 'tbAssignedNoMap';
						}
						if($el.length > 0)
						{
							var cnt_seats = parseInt($el.val(), 10);
							if(cnt_seats < cnt)
							{
								$el.val(cnt_seats + 1);
								$seatContainer.append('<span class="'+seatClass+' tbAssignedSeats_'+price_id+'" data_price_id="'+price_id+'" data_seat_id="'+seat_id+'">'+$ticket.attr('data-ticket') + ' #'+ seat_name +'</span>');
								if(cnt_seats + 1 == cnt)
								{
									$(this).removeClass('tbSeatAvailable');
								}
							}
						}else{
							$('<input>').attr({
							    type: 'hidden',
							    name: 'seat_id['+price_id+'][' + seat_id + ']',
							    class: 'tbHiddenSeat_' + price_id,
							    data_seat_id: seat_id,
							    value: '1'
							}).appendTo($frm);
							$seatContainer.append('<span class="'+seatClass+' tbAssignedSeats_'+price_id+'" data_price_id="'+price_id+'" data_seat_id="'+seat_id+'">'+$ticket.attr('data-ticket') + ' #'+ seat_name +'</span>');
							if(cnt == 1)
							{
								$(this).removeClass('tbSeatAvailable');
							}
						}
						$('#reload_map').val(0);
						if(has_map == 1)
						{
							adviseToSelectSeats();
							checkAssignedSeats();
						}
						$(this).addClass('tbSeatSelected');
					}else{
						
					}
				}else{

				}
			}
		}).on("click", ".tbAssignedSeats", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if($(this).parent().hasClass('copied'))
			{
				e.preventDefault();
				return false;
			}
			var $seat_id_arr = [],
				$mapHolder = $('#tbMapHolder'),
				seat_id = $(this).attr('data_seat_id'),
				price_id = $(this).attr('data_price_id'),
				$hidden = $("#tbSeatsForm input[name='seat_id\\["+price_id+"\\]\\["+seat_id+"\\]']");
			
			if($hidden.length > 0)
			{
				var val = $hidden.val(),
					tmp =parseInt(val, 10) - 1;
				if(tmp == 0)
				{
					$hidden.remove();
					$("#tbSeatsForm :input").each(function(i, el){
						$seat_id_arr.push($(el).attr('data_seat_id'));
					});
					$mapHolder.find(".tbSeatSelected").each(function (i, el) {
						var price_id_arr = ($(el).attr('data-price-id')).split('~:~');
						if(checkHasPriceId(price_id, price_id_arr) == true && $(el).attr('data-id') == seat_id && checkHasPriceId(seat_id, $seat_id_arr) == false)
						{
							if(!$(el).hasClass('tbSeatAvailable'))
							{
								$(el).addClass('tbSeatAvailable');
							}
							$(el).removeClass('tbSeatSelected');
						}
					});
				}else if(tmp > 0){
					$hidden.val(tmp);
				}
				var $parent = $(this).parent(); 
				$(this).remove();
				if($parent.hasClass('copied'))
				{
					$('#tbSelectedSeats').html($('#tbCopiedSeats').html());
				}else{
					$('#tbCopiedSeats').html($('#tbSelectedSeats').html());
				}
				adviseToSelectSeats();
				checkAssignedSeats();
			}
		}).on("change", "#payment_method", function (e) {
			switch ($("option:selected", this).val()) {
				case 'creditcard':
					$(".boxCC").show();
					break;
				default:
					$(".boxCC").hide();
			}
		}).on("click", ".tbContinueLink", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$('#tbCopiedSeats').html($('#tbSelectedSeats').html());
			if ($frmUpdateBooking.length > 0){
				$('#reload_map').val(0);
			}
			$dialogSelect.dialog("close");
		}).on("click", "#use_ticket", function(e){
			var $this = $(this);
			var tid = $(this).attr('lang');
			if ($this.is(':checked')) {
				$dialogTicketConfirmation.dialog({
					autoOpen: false,
					resizable: false,
					draggable: false,
					width: 380,
					height:150,
					modal: true,
					buttons: (function () {
						var buttons = {};
						buttons[tbApp.locale.button.yes] = function () {
							$.ajax({
								type: "POST",
								data: {
									id: tid
								},
								dataType: 'json',
								url: "index.php?controller=pjAdminBookings&action=pjActionSetUseTicket",
								success: function (res) {
									if(res.status == 1){
										$this.attr("disabled", true);
									}
								}
							});
							$dialogTicketConfirmation.dialog('close');
						};
						buttons[tbApp.locale.button.no] = function () {
							$this.removeAttr('checked');
							$dialogTicketConfirmation.dialog('close');
						};
						return buttons;
					})()
				});
				$dialogTicketConfirmation.dialog('open');
		    }
		});
		
	});
})(jQuery_1_8_2);