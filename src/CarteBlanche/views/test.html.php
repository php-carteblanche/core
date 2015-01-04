<?php
/**
 * This file is part of the CarteBlanche PHP framework.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * License Apache-2.0 <http://github.com/php-carteblanche/carteblanche/blob/master/LICENSE>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

_use('ajax');

$test_url = build_url(array(
//    'controller'=>'dev', 'action'=>'testajax'
    'action'=>'loremIpsum'
), null, '&');
?>
<script language="Javascript" type="text/javascript">

// Settings : global javascript options of pages
var settings; if (settings===undefined) settings = [];
settings.debug=false;

function test_ajax_txt() {
	return new Ajax({
		url:'<?php echo $test_url; ?>', 
		loader: "assets/img/indicator.gif",
		success:function(resp, e) {
			document.getElementById('TextDiv').innerHTML = resp;
		} 
	});
}

function test_ajax_txt_synch() {
	return new Ajax({
		url:'<?php echo $test_url; ?>', 
		loader: "assets/img/indicator.gif",
		asynch: false,
		success:function(resp, e) {
			document.getElementById('TextDiv').innerHTML = resp;
		} 
	});
}

function test_ajax_xml() {
	return new Ajax({
		url:'test/xml_test.xml', 
		loader: "assets/img/indicator.gif",
		format: 'XML',
		load_in: 'TextDiv',
		success:function(resp, e) {
			var element = resp.getElementsByTagName('root').item(0);
			document.getElementById('TextDiv').innerHTML = element.firstChild.data;
		} 
	});
}

function test_load_txt() {
	return new ajaxLoad('TextDiv' ,'<?php echo $test_url; ?>');
}

function test_ajax_txt_timeout() {
	return new Ajax({
		url:'<?php echo $test_url; ?>', 
		load_in: 'TextDiv',
		timeout: 2000,
		loader: "assets/img/indicator.gif"
	});
}

function test_ajax_txt_timeout_disabled() {
	return new Ajax({
		url:'<?php echo $test_url; ?>', 
		load_in: 'TextDiv',
		timeout: 2000,
		dom_disabled: true,
		loader: "assets/img/indicator.gif"
	});
}

function test_ajax_file_error() {
	Ajax({
		url:'test/abcdefgh.htm', 
		loader: "assets/img/indicator.gif",
		success:function(resp, e) {
			document.getElementById('TextDiv').innerHTML = resp;
		},
		error: function(resp, e) {
    			alert('An error occurred : '+resp);
		}
	});
}

function test_ajax_form() {
	Ajax({
		url:'test/test.php', 
		loader: "assets/img/indicator.gif",
		success:function(resp, e) {
			document.getElementById('TextDiv').innerHTML = resp;
		},
		error: function(resp, e) {
    			alert('An error occurred : '+resp);
		}
	});
}

function test_ajax_loader( _loader_ ) {
	Ajax({
		url:'test/test_sleep.php', 
		load_in: 'TextDiv',
		loader: _loader_
	});
}

function test_ajax_form_sleep() {
	Ajax({
		url:'test/test_sleep.php', 
		loader: "assets/img/indicator.gif",
		load_in: 'TextDiv'
	});
}

function test_ajax_form_get() {
	Ajax({
		url:'test/test.php', 
		data: { myfield: 'an info getted via AJAX' },
		loader: "assets/img/indicator.gif",
		success:function(resp, e) {
			document.getElementById('TextDiv').innerHTML = resp;
		},
		error: function(resp, e) {
    			alert('An error occurred : '+resp);
		}
	});
}

function test_ajax_form_post() {
	Ajax({
		url:'test/test.php', 
		method: 'POST',
		data: "myfield="+escape('an info posted via AJAX'),
		loader: "assets/img/indicator.gif",
		success:function(resp, e) {
			document.getElementById('TextDiv').innerHTML = resp;
		},
		error: function(resp, e) {
    			alert('An error occurred : '+resp);
		}
	});
}

function test_ajax_args_error() {
	Ajax({
		loader: "assets/img/indicator.gif",
		success:function(resp, e) {
			document.getElementById('TextDiv').innerHTML = resp;
		},
		error: function(resp, e) {
    			alert('An error occurred : '+resp);
		}
	});
}

function test_load_args_error() {
	ajaxLoad('TextDiv');
}

function test() {
	if(arguments.length) alert('Arguments');
	else alert('no args');
}

function successFormSubmit(resp) {
	document.getElementById('TextDiv').innerHTML = resp;
}

</script>
<style type="text/css">
.disabled {
    display: block;
    opacity:0.4;
    filter:alpha(opacity=40);
}
</style>

	<table cellspacing=30><tr>
	<td>
	<h3>Functionality tests</h3>
   <ul>
	   <li><a href="#" onclick="test_ajax_txt();">Test text Ajax</a></li>
	   <li><a href="#" onclick="test_ajax_xml();">Test XML Ajax</a></li>
	   <li><a href="#" onclick="test_ajax_txt_timeout();">Test text Ajax with timeout</a></li>
	   <li><a href="#" onclick="test_ajax_txt_timeout_disabled();">Test text Ajax with timeout and div disabled</a></li>	   
	   <li><a href="#" onclick="test_ajax_form_sleep();">Test text Ajax with long time response</a></li>
	   <li><a href="#" onclick="test_load_txt();">Test text ajaxLoad</a></li>
	   <li><a href="#" onclick="test_ajax_form();">Test form Ajax</a></li>
	   <li><a href="#" onclick="test_ajax_form_get();">Test form Ajax with get</a></li>
	   <li><a href="#" onclick="test_ajax_form_post();">Test form Ajax with post</a></li>
	   <li><a href="#" onclick="test_ajax_txt_synch();">Test text Ajax synchronous</a></li>
	</ul>
	</td>
	<td>
	<h3>Errors tests</h3>
   <ul>
	   <li><a href="#" onclick="test_ajax_file_error();">Test error Ajax : file not found</a></li>
	   <li><a href="#" onclick="test_ajax_args_error();">Test error Ajax : no URL specified</a></li>
	   <li><a href="#" onclick="test_load_args_error();">Test error ajaxLoad : argument missing (1 argument) </a></li>
   </ul>
	<h3>Loader image tests</h3>
   <ul>
	   <li><a href="#" onclick="test_ajax_loader('indicator.gif');">Test 'indicator.gif' (default)</a></li>
	   <li><a href="#" onclick="test_ajax_loader('indicator_mini.gif');">Test 'indicator_mini.gif'</a></li>
	   <li><a href="#" onclick="test_ajax_loader('loader.gif');">Test 'loader.gif'</a></li>
	   <li><a href="#" onclick="test_ajax_loader('loadingAnimation.gif');">Test 'loadingAnimation.gif'</a></li>
	   <li><a href="#" onclick="test_ajax_loader('reloading.gif');">Test 'reloading.gif'</a></li>
	</ul>
	</td>
	</tr></table>
	<hr />
   <div id="TextDiv">Text</div>
