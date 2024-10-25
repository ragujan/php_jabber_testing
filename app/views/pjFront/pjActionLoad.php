<?php
mt_srand();
$index = mt_rand(1, 9999);
$validate = str_replace(array('"', "'"), array('\"', "\'"), __('validate', true, true));
$layout = isset($_GET['layout']) ? $_GET['layout'] : $tpl['option_arr']['o_theme'];
?>
<div id="pjWrapperTicketBooking_<?php echo $layout;?>">
	<div id="tbContainer_<?php echo $index; ?>" class="tbContainer pjCbContainer"></div>
	
	<div class="modal fade pjCbModal" id="scTermModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-pj-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	        		<h4 class="modal-title pjCbModalTitle" id="myModalLabel"><?php __('front_terms_title', false, true); ?></h4>
	      		</div>
			    <div class="modal-body">
			    	<?php echo nl2br(stripslashes($tpl['terms_conditions'])); ?>
			    </div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-default pjCbBtn pjCbBtnPrimary" data-pj-dismiss="modal">OK</button>
		      	</div>
	    	</div>
	  	</div>
	</div>
	<div class="modal fade pjCbModal" id="pjCbsCaptchaModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog">
	    	<div class="modal-content">
			    <div id="pjCbsCaptchaMessage" class="modal-body"></div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-default pjCbBtn pjCbBtnPrimary" data-pj-dismiss="modal">OK</button>
		      	</div>
	    	</div>
	  	</div>
	</div>
	
</div><!-- /#pjWrapper -->
<script type="text/javascript">
var pjQ = pjQ || {},
	TicketBooking_<?php echo $index; ?>;
(function () {
	"use strict";
	var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),

	loadCssHack = function(url, callback){
		var link = document.createElement('link');
		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = url;

		document.getElementsByTagName('head')[0].appendChild(link);

		var img = document.createElement('img');
		img.onerror = function(){
			if (callback && typeof callback === "function") {
				callback();
			}
		};
		img.src = url;
	},
	loadRemote = function(url, type, callback) {
		if (type === "css" && isSafari) {
			loadCssHack(url, callback);
			return;
		}
		var _element, _type, _attr, scr, s, element;
		
		switch (type) {
		case 'css':
			_element = "link";
			_type = "text/css";
			_attr = "href";
			break;
		case 'js':
			_element = "script";
			_type = "text/javascript";
			_attr = "src";
			break;
		}
		
		scr = document.getElementsByTagName(_element);
		s = scr[scr.length - 1];
		element = document.createElement(_element);
		element.type = _type;
		if (type == "css") {
			element.rel = "stylesheet";
		}
		if (element.readyState) {
			element.onreadystatechange = function () {
				if (element.readyState == "loaded" || element.readyState == "complete") {
					element.onreadystatechange = null;
					if (callback && typeof callback === "function") {
						callback();
					}
				}
			};
		} else {
			element.onload = function () {
				if (callback && typeof callback === "function") {
					callback();
				}
			};
		}
		element[_attr] = url;
		s.parentNode.insertBefore(element, s.nextSibling);
	},
	loadScript = function (url, callback) {
		loadRemote(url, "js", callback);
	},
	loadCss = function (url, callback) {
		loadRemote(url, "css", callback);
	},
	getSessionId = function () {
		return sessionStorage.getItem("session_id") == null ? "" : sessionStorage.getItem("session_id");
	},
	createSessionId = function () {
		if(getSessionId()=="") {
			sessionStorage.setItem("session_id", "<?php echo session_id(); ?>");
		}
	},
	options = {
		server: "<?php echo PJ_INSTALL_URL; ?>",
		folder: "<?php echo PJ_INSTALL_URL; ?>",
		layout: "<?php echo $layout;?>",
		index: <?php echo $index; ?>,
		hide: <?php echo isset($_GET['hide']) && (int) $_GET['hide'] === 1 ? 1 : 0; ?>,
		locale: <?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : $controller->pjActionGetLocale(); ?>,
		week_start: <?php echo (int) $tpl['option_arr']['o_week_start']; ?>,
		date_format: "<?php echo $tpl['option_arr']['o_date_format']; ?>",
		guide_msg: <?php echo pjAppController::jsonEncode(__('front_guide', true)); ?>,
		error_msg: <?php echo pjAppController::jsonEncode(__('front_err', true)); ?>,
		validate: <?php echo pjAppController::jsonEncode($validate); ?>
	};
	<?php
	$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
	$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	?>
	loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('storage_polyfill'); ?>storagePolyfill.min.js", function () {
		if (isSafari) {
			createSessionId();
			options.session_id = getSessionId();
		}else{
			options.session_id = "";
		}
		loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_jquery'); ?>pjQuery.min.js", function () {
			loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_jquery_ui'); ?>js/pjQuery-ui.custom.min.js", function () {
				loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_validate'); ?>pjQuery.validate.min.js", function () {
					loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_validate'); ?>pjQuery.additional-methods.min.js", function () {
						loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap'); ?>pjQuery.bootstrap.min.js", function () {
							loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH; ?>pjTicketBooking.js", function () {
								TicketBooking_<?php echo $index; ?> = new TicketBooking(options);
							});
						});
					});
				});
			});
		});
	});
})();
</script>