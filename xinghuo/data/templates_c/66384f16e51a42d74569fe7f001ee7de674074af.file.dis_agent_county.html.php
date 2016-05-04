<?php /* Smarty version Smarty-3.1.13, created on 2016-03-16 13:16:32
         compiled from "E:\xh\member\shop\html\dis_agent_county.html" */ ?>
<?php /*%%SmartyHeaderCode:2609556e8ec30bff5e0-78755834%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '66384f16e51a42d74569fe7f001ee7de674074af' => 
    array (
      0 => 'E:\\xh\\member\\shop\\html\\dis_agent_county.html',
      1 => 1447587861,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2609556e8ec30bff5e0-78755834',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'county_list' => 0,
    'county_name' => 0,
    'county_item' => 0,
    'c_name' => 0,
    'c_item' => 0,
    'county_data_list' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_56e8ec30bff5e9_27477905',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56e8ec30bff5e9_27477905')) {function content_56e8ec30bff5e9_27477905($_smarty_tpl) {?><br/>
<span>灰色不可选择表示已被其他用户选择！！！</span>
<br/>
<br/>
<style>
.county_select{width:400px;}
</style>
<select class="county_select" name="county" multiple="multiple">
	<?php  $_smarty_tpl->tpl_vars['county_item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['county_item']->_loop = false;
 $_smarty_tpl->tpl_vars['county_name'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['county_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['county_item']->key => $_smarty_tpl->tpl_vars['county_item']->value){
$_smarty_tpl->tpl_vars['county_item']->_loop = true;
 $_smarty_tpl->tpl_vars['county_name']->value = $_smarty_tpl->tpl_vars['county_item']->key;
?>
	<optgroup label="<?php echo $_smarty_tpl->tpl_vars['county_name']->value;?>
">
		<?php  $_smarty_tpl->tpl_vars['c_item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['c_item']->_loop = false;
 $_smarty_tpl->tpl_vars['c_name'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['county_item']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['c_item']->key => $_smarty_tpl->tpl_vars['c_item']->value){
$_smarty_tpl->tpl_vars['c_item']->_loop = true;
 $_smarty_tpl->tpl_vars['c_name']->value = $_smarty_tpl->tpl_vars['c_item']->key;
?>
		<option value="<?php echo $_smarty_tpl->tpl_vars['c_name']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['c_item']->value;?>
" <?php if (in_array($_smarty_tpl->tpl_vars['c_name']->value,$_smarty_tpl->tpl_vars['county_data_list']->value['disable'])){?>disabled="disabled"<?php }?>><?php echo $_smarty_tpl->tpl_vars['c_item']->value;?>
</option>
		<?php } ?>
	</optgroup>
	<?php } ?>
</select><?php }} ?>