var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateVenue = $("#frmCreateVenue"),
			$frmUpdateVenue = $("#frmUpdateVenue"),
			$frmUpdateSector = $('#frmUpdateSector'),
			$dialogUpdate = $("#dialogUpdate"),
			$dialogDel = $("#dialogDelete"),
			$dialogHotspot = $("#dialogHotspot"),
			$boxMap = $("#boxMap"),
			datagrid = ($.fn.datagrid !== undefined),
			validate = ($.fn.validate !== undefined),
			hotspot_width = 25,
			hotspot_height = 25,
			vOpts = {
				rules:{
					seat_number: {
						required: function(){
							if($('#seats_count').val() != '')
							{
								var result = false;
								$('.number-field').each(function(i, ele) {
								    if($(ele).val() == '')
								    {
								    	result = true;
								    }
								});
								return result;
							}else{
								return false;
							}
						}
					}
				},
				messages: {
					number_of_seats:{
						required: myLabel.seats_required
					},
					seat_number:{
						required: myLabel.seat_numbers_required
					},
					seats_count: {
						positiveNumber: myLabel.seat_count_greater_zero
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: '',
				invalidHandler: function (event, validator) {
				    $(".pj-multilang-wrap").each(function( index ) {
						if($(this).attr('data-index') == myLabel.localeId)
						{
							$(this).css('display','block');
						}else{
							$(this).css('display','none');
						}
					});
					$(".pj-form-langbar-item").each(function( index ) {
						if($(this).attr('data-index') == myLabel.localeId)
						{
							$(this).addClass('pj-form-langbar-item-active');
						}else{
							$(this).removeClass('pj-form-langbar-item-active');
						}
					});
				}, 
				submitHandler: function(form){
					var post_arr = new Array();
					var chunk_arr = new Array();
					var loop = 0;
					function setBeforeSave(i)
					{
						var post_data = chunk_arr[i].join("&");
						$.post("index.php?controller=pjAdminVenues&action=pjActionBeforeSave", post_data, callback);
					}
					function callback()
					{
						loop++;
						if(loop < chunk_arr.length)
						{
							setBeforeSave.call(null, [loop]);
						}else{
							form.submit();
						}
					}
					if($("input[type='radio']:checked").val() == 'T')
					{
						form.submit();
					}else{
						var max_post_fields = 100;
						if(parseInt($('#seats_count').val(), 10) > max_post_fields)
						{
							$('.number-field').each(function(index){
								post_arr.push($(this).serialize());
								$(this).attr('name', "");
							});
							while (post_arr.length > 0) 
							{
								chunk_arr.push(post_arr.splice(0, max_post_fields));
							}
							if(chunk_arr.length > 0)
							{
								setBeforeSave.call(null, [loop]);
							}
						}else{
							form.submit();
						}
					}
					return false;
				}
			};
		if ($frmCreateVenue.length > 0 || $frmUpdateVenue.length > 0) {
			$.validator.addMethod('positiveNumber',
				function (value) { 
		        	return Number(value) > 0;
		    	}, 
		    myLabel.seat_count_greater_zero);
		}
		
		
		function collisionDetect(o) {
			var i, pos, horizontalMatch, verticalMatch, collision = false;
			$("#mapHolder").children("span").each(function (i) {
				pos = getPositions(this);
				horizontalMatch = comparePositions([o.left, o.left + o.width], pos[0]);
				verticalMatch = comparePositions([o.top, o.top + o.height], pos[1]);			
				if (horizontalMatch && verticalMatch) {
					collision = true;
					return false;
				}
			});
			if (collision) {
				return true;
			}
			return false;
		}
		function getPositions(box) {
			var $box = $(box);
			var pos = $box.position();
			var width = $box.width();
			var height = $box.height();
			return [[pos.left, pos.left + width], [pos.top, pos.top + height]];
		}
		
		function comparePositions(p1, p2) {
			var x1 = p1[0] < p2[0] ? p1 : p2;
			var x2 = p1[0] < p2[0] ? p2 : p1;
			return x1[1] > x2[0] || x1[0] === x2[0] ? true : false;
		}
		
		function updateElem(event, ui) {
			var $this = $(this),
				rel = $this.attr("rel"),
				$hidden = $("#" + rel),
				val = $hidden.val().split("|");				
			$hidden.val([val[0], parseInt($this.width(), 10), parseInt($this.height(), 10), ui.position.left, ui.position.top, $this.text(), val[6], val[7]].join("|"));
		}
		function getMax() {
			var tmp, index = 0;
			$("span.empty").each(function (i) {
				if (!isNaN(Number(this.title))) {
					tmp = Number(this.title);
				} else {
					tmp = parseInt($(this).attr("rel").split("_")[1], 10);
				}
				if (tmp > index) {
					index = tmp;
				}
			});
			return index;
		}
		
		function GetZoomFactor () {
            var factor = 1;
            if (document.body.getBoundingClientRect) {
                    // rect is only in physical pixel size in IE before version 8 
                var rect = document.body.getBoundingClientRect ();
                var physicalW = rect.right - rect.left;
                var logicalW = document.body.offsetWidth;

                    // the zoom level is always an integer percent value
                factor = Math.round ((physicalW / logicalW) * 100) / 100;
            }
            return factor;
        }
		
		if ($frmCreateVenue.length > 0 && validate) {
			var validator = $frmCreateVenue.submit(function() {
				if($('#hiddenHolder').length > 0)
				{
					if($("#hiddenHolder :input").length > 0)
					{
						$('#number_of_seats').val('1');
					}else{
						$('#number_of_seats').val('');
					}
				}
				if($("input[type='radio']:checked").val() == 'T')
				{
					$('#number_of_seats').addClass('required');
					$('#seats_count').removeClass('required positiveNumber');
				}
				if($("input[type='radio']:checked").val() == 'F')
				{
					$('#number_of_seats').removeClass('required');
					$('#seats_count').addClass('required positiveNumber');
				}
			}).validate(vOpts);
		}
		if ($frmUpdateVenue.length > 0) {
			var validator = $frmUpdateVenue.submit(function() {
				if($('#hiddenHolder').length > 0)
				{
					if($("#hiddenHolder :input").length > 0)
					{
						$('#number_of_seats').val('1');
					}else{
						$('#number_of_seats').val('');
					}
				}
				if($("input[type='radio']:checked").val() == 'T')
				{
					$('#number_of_seats').addClass('required');
					$('#seats_count').removeClass('required positiveNumber');
				}
				if($("input[type='radio']:checked").val() == 'F')
				{
					$('#number_of_seats').removeClass('required');
					$('#seats_count').addClass('required positiveNumber');
				}
			}).validate(vOpts);
			
			var offset = $("#map").offset(),
				dragOpts = {
					containment: "parent",
					stop: function (event, ui) {
						updateElem.apply(this, [event, ui]);
					}
				};
			$("span.empty").draggable(dragOpts).resizable({
				resize: function(e, ui) {
					var height = $(this).height();
					$(this).css("line-height", height + "px"); 
		        },
				stop: function(e, ui) {
					var height = $(this).height();
					$(this).css("line-height", height + "px");
					updateElem.apply(this, [e, ui]);
		        }
			}).bind("click", function (e) {
				$dialogUpdate.data('rel', $(this).attr("rel")).dialog("open");
				$(this).siblings(".rect").removeClass("rect-selected").end().addClass("rect-selected");
			});
			
			$("#mapHolder").click(function (e) {
				var px = $('.bsMapHolder').scrollLeft();
				var $this = $(this),
				index = getMax(),
				w = hotspot_width,
				h = hotspot_height;
				
				var t = Math.ceil(e.pageY - offset.top - (parseInt(hotspot_height / 2, 10)));
				var l = Math.ceil(e.pageX - offset.left - (parseInt(hotspot_width / 2, 10)) + px);
				var o = {top: t, left: l, width: w, height: h};
				
				if (!collisionDetect(o)) {
					index++;
					$("<span>", {
						css: {
							"top": t + "px",
							"left": l + "px",
							"width": w + "px",
							"height": h + "px",
							"line-height": h + "px",
							"position": "absolute"
						},
						html: '<span class="bsInnerRect" data-name="hidden_'+index+'">'+index+'</span>',
						rel: "hidden_" + index,
						title: index
					}).addClass("rect empty new").draggable(dragOpts).resizable({
						resize: function(e, ui) {
							var height = $(this).height();
							$(this).css("line-height", height + "px"); 
				        },
						stop: function(e, ui) {
							var height = $(this).height();
							$(this).css("line-height", height + "px"); 
							updateElem.apply(this, [e, ui]);
				        }
					}).bind("click", function (e) {
						$dialogUpdate.data('rel', $(this).attr("rel")).dialog("open");
						$(this).siblings(".rect").removeClass("rect-selected").end().addClass("rect-selected");
					}).appendTo($this);
					
					$("<input>", {
						type: "hidden",
						name: "seats_new[]",
						id: "hidden_" + index
					}).val(['x', w, h, l, t, index, '1', '1'].join("|")).appendTo($("#hiddenHolder"));
					
				} else {
					if (window.console && window.console.log) {
					}
				}
			});
			
			if ($dialogHotspot.length > 0) {
				$dialogHotspot.dialog({
					autoOpen: false,
					resizable: false,
					draggable: false,
					modal: true,
					buttons: (function () {
						var buttons = {};
						buttons[tbApp.locale.button.set] = function () {
							hotspot_width = parseInt($('#hotspot_width').val(), 10);
							hotspot_height = parseInt($('#hotspot_height').val(), 10);
							$dialogHotspot.dialog('close');
						};
						return buttons;
					})()
				});
			}
			
			if ($dialogDel.length > 0) {
				$dialogDel.dialog({
					autoOpen: false,
					resizable: false,
					draggable: false,
					modal: true,
					buttons: (function () {
						var buttons = {};
						buttons[tbApp.locale.button.delete] = function () {
							$.ajax({
								type: "POST",
								data: {
									id: $(this).data('lang')
								},
								url: "index.php?controller=pjAdminVenues&action=pjActionDeleteMap",
								success: function (data) {
									if(data != '100')
									{
										$boxMap.html(data);
										$('#seats_count').parent().parent().css('display', 'block');
										$('#number_of_seats').remove();
									}
								}
							});
							$dialogDel.dialog('close');
						};
						buttons[tbApp.locale.button.cancel] = function () {
							$dialogDel.dialog('close');
						};
						
						return buttons;
					})()
				});
			}
			
			if ($dialogUpdate.length > 0) {
				var seat_id = null;
				$dialogUpdate.dialog({
					autoOpen: false,
					resizable: false,
					draggable: false,
					modal: true,
					width: 440,
					open: function () {
						var rel = $(this).data("rel"),
							arr = $("#" + rel).val().split("|");
						$("#seat_name").val(arr[5]);
						$("#seat_seats").val(arr[6]);
						seat_id = arr[0];
					},
					close: function () {
						$("#seat_name, #seat_seats").val("");
										
					},
					buttons: (function () {
						var buttons = {};
						buttons[tbApp.locale.button.save] = function () {
							var rel = $(this).data("rel"),
								pName = $("#seat_name").val(),
								pSeats = parseInt($("#seat_seats").val(), 10),
								pHidden = $("#" + rel, $frmUpdateVenue).val();
							if(pSeats > 0)
							{
								var a = pHidden.split("|");
								var $rect_inner = $(".bsInnerRect[data-name='" + rel + "']", $frmUpdateVenue);
								$rect_inner.text(pName);
								$("#rbInner_" + rel).text(pName);
								$("#" + rel).val([seat_id, a[1], a[2], a[3], a[4], pName, pSeats].join("|"));
								
								$("#seat_seats").removeClass('error');
								$(this).dialog('close');
							}else{
								$("#seat_seats").addClass('error');
							}
						};
						buttons[tbApp.locale.button.delete] = function () {
							var rel = $(this).data('rel');
							$("#" + rel, $("#hiddenHolder")).remove();				
							$(".rect-selected[rel='"+ rel +"']", $("#mapHolder")).remove();
							
							$(this).dialog('close');
						};
						buttons[tbApp.locale.button.cancel] = function () {
							$dialogUpdate.dialog('close');
						};
						
						return buttons;
					})()
				});
			}
		}
		
		function formatMap(val, obj) {
			return val != null ? myLabel.yes : myLabel.no ;
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminVenues&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminVenues&action=pjActionDeleteVenue&id={:id}"}
				          ],
				columns: [{text: myLabel.name, type: "text", sortable: true, editable: false, width: 280},
				          {text: myLabel.map, type: "text", sortable: false, editable: false, renderer: formatMap, width: 100},
				          {text: myLabel.seats, type: "text", sortable: true, editable: false, width: 120},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 90, options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminVenues&action=pjActionGetVenue",
				dataType: "json",
				fields: ['name', 'map_path', 'seats_count', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminVenues&action=pjActionDeleteVenueBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.revert_status, url: "index.php?controller=pjAdminVenues&action=pjActionStatusVenue", render: true},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminVenues&action=pjActionExportVenue", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminVenues&action=pjActionSaveVenue&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		function loadSeatNumber()
		{
			var number_of_seats = parseInt($('#seats_count').val(), 10),
				i = 1,
				existing_number = $('#tbSeatNumber > input').length,
				tmp = 1;
			if(number_of_seats == 0)
			{
				$('#tbSeatNumber').siblings().eq(0).html(myLabel.seat_numbers_1);
				$('#tbSeatNumber').parent().siblings().html('');
			}else{
				$('#tbSeatNumber').siblings().eq(0).html(myLabel.seat_numbers_2);
				$('#tbSeatNumber').parent().siblings().eq(0).html(myLabel.seat_numbers);
			}
			
			if(existing_number == 0)
			{
				$('#tbSeatNumber').html("");
			}
			if(existing_number < number_of_seats && existing_number > 0){
				tmp = existing_number + 1;
			}
			if(existing_number > number_of_seats)
			{
				$('.number-field').each(function(i, ele) {
					var index = parseInt($(ele).attr('data-index'),10)
				    if(index > number_of_seats)
				    {
				    	$(this).remove();
				    }
				});
			}else{
				if(existing_number != number_of_seats)
				{
					for(i = tmp; i <= number_of_seats; i++)
					{
						$('#tbSeatNumber').append('<input type="text" name="number[new_'+i+']" value="'+i+'" class="pj-form-field w80 number-field" data-index="'+i+'" />');
					}
				}
			}
			$('.pj-loader').hide();
		}
		
		if ($frmUpdateSector.length > 0 && validate) {
			$frmUpdateSector.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element);
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				submitHandler: function(form){
					var valid = true;
					$('.pj-seat-field').each(function(i, el){
						var value = parseInt($(this).val(), 10);
						if(value > 0)
						{
							$(this).removeClass('error');
						}else{
							valid = false;
							$(this).addClass('error');
						}
					});
					if(valid == true)
					{
						form.submit();
					}
				}
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
			$grid.datagrid("load", "index.php?controller=pjAdminVenues&action=pjActionGetVenue", "name", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminVenues&action=pjActionGetVenue", "name", "ASC", content.page, content.rowCount);
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
			$.post("index.php?controller=pjAdminVenues&action=pjActionSetActive", {
				id: $(this).closest("tr").data("object")['id']
			}).done(function (data) {
				$grid.datagrid("load", "index.php?controller=pjAdminVenues&action=pjActionGetVenue");
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
			$grid.datagrid("load", "index.php?controller=pjAdminVenues&action=pjActionGetVenue", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-delete-map", function (e) {
			$dialogDel.data('lang', $(this).attr('lang')).dialog('open');
		}).on("click", "#pj_delete_seat", function (e) {
			var rel = $(this).attr('data-rel');
			$("#" + rel, $("#hiddenHolder")).remove();				
			$(".rect-selected[rel='"+ rel +"']", $("#mapHolder")).remove();
			$(this).css('display', 'none');
		}).on("click", "input:radio[name=use_seats_map]", function (e) {
			if($(this).val() == 'T')
			{
				$('.tbUseMapYes').css('display', 'block');
				$('.tbUseMapNo').css('display', 'none');
				$('#seats_map').addClass('required');
				$('#seats_count').removeClass('required');
				$('.tbHotpotSize').css('display', 'block');
			}else{
				$('.tbUseMapYes').css('display', 'none');
				$('.tbUseMapNo').css('display', 'block');
				$('#seats_count').addClass('required');
				$('#seats_map').removeClass('required');
				$('.tbHotpotSize').css('display', 'none');
			}
		}).on("keyup", "#seats_count", function (e) {
			var key = e.charCode || e.keyCode || 0;
			if (key == 8 || 
                key == 13 ||
                key == 46 ||
                key == 110 ||
                key == 190 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105))
			{
				$('.pj-loader').show();
				delay(function(){
					loadSeatNumber();
			    }, 2000 );
			}
		}).on("click", ".tbHotpotSize", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogHotspot.dialog('open');
		});
		
		var delay = (function(){
			var timer = 0;
			return function(callback, ms){
		    clearTimeout (timer);
		    timer = setTimeout(callback, ms);
		  };
		})();
	});
})(jQuery_1_8_2);