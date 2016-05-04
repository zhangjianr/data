<?php /* Smarty version Smarty-3.1.13, created on 2016-03-16 13:16:32
         compiled from "E:\xh\member\shop\html\dis_agent_form.html" */ ?>
<?php /*%%SmartyHeaderCode:2245556e8ec309610d8-17037725%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1d5a700594b7111e555f7e6f011ef8bb658db8a7' => 
    array (
      0 => 'E:\\xh\\member\\shop\\html\\dis_agent_form.html',
      1 => 1450377298,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2245556e8ec309610d8-17037725',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'account_id' => 0,
    'county_data_list' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_56e8ec30a54cd7_32349802',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56e8ec30a54cd7_32349802')) {function content_56e8ec30a54cd7_32349802($_smarty_tpl) {?><div>

	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#province" aria-controls="province" role="tab" data-toggle="tab">省级代理</a></li>
		<li role="presentation"><a href="#city" aria-controls="city" role="tab" data-toggle="tab">市级代理</a></li>
		<li role="presentation"><a href="#county" aria-controls="county" role="tab" data-toggle="tab">县级代理</a></li>
	</ul>

	<!-- Tab panes -->
	<form class="form-horizontal" id="dis_agent_form">
		<input type="hidden" name="account_id" id="account_id" value="<?php echo $_smarty_tpl->tpl_vars['account_id']->value;?>
"/>
		<div class="tab-content">
			<div role="tabpane2" class="tab-pane active" id="province">

				<div id="dis_privince_area">	
					<?php echo $_smarty_tpl->getSubTemplate ("dis_agent_province.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
	  
				</div>

			</div>
			<div role="tabpane3" class="tab-pane" id="city">

				<div id="dis_city_area">	        
					<?php echo $_smarty_tpl->getSubTemplate ("dis_agent_city.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
	  
				</div>

			</div>
			<div role="tabpane4" class="tab-pane" id="county">

				<div id="dis_county_area">	        
					<?php echo $_smarty_tpl->getSubTemplate ("dis_agent_county.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
	  
				</div>

			</div>
		</div>
	</form>
	<script type="text/javascript">
		var checkedCounty = <?php echo $_smarty_tpl->tpl_vars['county_data_list']->value['checked'];?>
;
	</script>
</div><?php }} ?>