<?php /* Smarty version Smarty-3.1.13, created on 2016-03-14 13:44:01
         compiled from "E:\wwwroot\spark\public_html\member\kanjia\lbi\option_list.html" */ ?>
<?php /*%%SmartyHeaderCode:3042556e64fa153b231-68864957%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fadb1bda967f974361379e62361213165604ac22' => 
    array (
      0 => 'E:\\wwwroot\\spark\\public_html\\member\\kanjia\\lbi\\option_list.html',
      1 => 1425115676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3042556e64fa153b231-68864957',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'list' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_56e64fa17da875_77567228',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56e64fa17da875_77567228')) {function content_56e64fa17da875_77567228($_smarty_tpl) {?><?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
<option value="<?php echo $_smarty_tpl->tpl_vars['item']->value['Products_ID'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['Products_Name'];?>
---[<?php echo $_smarty_tpl->tpl_vars['item']->value['Products_PriceX'];?>
]</option>
<?php } ?><?php }} ?>