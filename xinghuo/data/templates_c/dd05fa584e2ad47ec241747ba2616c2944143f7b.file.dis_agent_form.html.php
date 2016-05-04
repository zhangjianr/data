<?php /* Smarty version Smarty-3.1.13, created on 2016-03-14 12:22:15
         compiled from "E:\wwwroot\spark\public_html\member\shop\html\dis_agent_form.html" */ ?>
<?php /*%%SmartyHeaderCode:299956e63c77f3e0d2-30331707%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dd05fa584e2ad47ec241747ba2616c2944143f7b' => 
    array (
      0 => 'E:\\wwwroot\\spark\\public_html\\member\\shop\\html\\dis_agent_form.html',
      1 => 1450368298,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '299956e63c77f3e0d2-30331707',
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
  'unifunc' => 'content_56e63c7816a038_63573984',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56e63c7816a038_63573984')) {function content_56e63c7816a038_63573984($_smarty_tpl) {?><div>

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