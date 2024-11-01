jQuery(document).ready(function($) {
	$('.xdu-trigger').on('change', function() {
		xdu_ajax_call([{
			type: $(this).closest( "div" ).attr('class').split(' ')[0], 
			slug: event.target.id, 
			checked: document.getElementById(event.target.id).checked
		}]);
	});
	
	function xdu_ajax_call(options) {
		var data = {
			action: 'xdu_trigger',
			xdu_nonce: xdu_vars.xdu_nonce,
			options: options
		}
		$.post(ajaxurl, data, function(response) {
		});
	}
	$('#xdu_theme_enable').click(function() {
		var options = [];
		$('.xdu-theme-check').each(function() {
			$(this).attr("checked",true);
			options.push({type: 'theme', slug: $(this).attr('id'), checked: 'true'});
		});
		xdu_ajax_call(options);
  	});
	$('#xdu_theme_disable').click(function() {
		var options = [];
		$('.xdu-theme-check').each(function() {
			$(this).attr("checked",false);
			options.push({type: 'theme', slug: $(this).attr('id'), checked: 'false'});
		});
		xdu_ajax_call(options);
	});
	$('#xdu_plugin_enable').click(function() {
		var options = [];
		$('.xdu-plugin-check').each(function() {
			$(this).attr("checked",true);
			options.push({type: 'plugin', slug: $(this).attr('id'), checked: 'true'});
		});
		xdu_ajax_call(options);
	});
	$('#xdu_plugin_disable').click(function() {
		var options = [];
		$('.xdu-plugin-check').each(function() {
			$(this).attr("checked",false);
			options.push({type: 'plugin', slug: $(this).attr('id'), checked: 'false'});
		});
		xdu_ajax_call(options);
	});
});