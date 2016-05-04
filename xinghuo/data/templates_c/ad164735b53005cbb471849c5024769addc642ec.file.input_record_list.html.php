<?php /* Smarty version Smarty-3.1.13, created on 2016-03-27 09:39:16
         compiled from "C:\xh\member\shop\html\input_record_list.html" */ ?>
<?php /*%%SmartyHeaderCode:391456f739c4b60422-45377949%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ad164735b53005cbb471849c5024769addc642ec' => 
    array (
      0 => 'C:\\xh\\member\\shop\\html\\input_record_list.html',
      1 => 1439866386,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '391456f739c4b60422-45377949',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'input_record_list' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_56f739c4c138f0_33627907',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56f739c4c138f0_33627907')) {function content_56f739c4c138f0_33627907($_smarty_tpl) {?><!-- 记录列表  begin -->
<table class="table table-striped">
  <thead>
    <tr>
      <th>#</th>
      <th>订单号</th>
      <th>总价</th>
      <th>状态</th>
      <th>时间</th>
      <th>详情</th>
    </tr>
  </thead>
  <tbody>
  
  <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['input_record_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>
  <tr>
    <td><?php echo $_smarty_tpl->tpl_vars['item']->value['Order_ID'];?>
</td>
    <td><?php echo $_smarty_tpl->tpl_vars['item']->value['Order_Sn'];?>
 </td>
    <td class="red">&yen;<?php echo $_smarty_tpl->tpl_vars['item']->value['Order_TotalAmount'];?>
</td>
    <td><?php echo $_smarty_tpl->tpl_vars['item']->value['Order_Status'];?>
</td>
     <td><?php echo $_smarty_tpl->tpl_vars['item']->value['Order_CreateTime'];?>
</td>
    <td><a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['Order_Link'];?>
"><img src="/static/member/images/ico/view.gif"/></a></td>
  </tr>
  <?php } ?>
    </tbody>
  
</table>

<!-- 记录列表 end-->
<?php }} ?>