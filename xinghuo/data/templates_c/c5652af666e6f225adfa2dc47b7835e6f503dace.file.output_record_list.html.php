<?php /* Smarty version Smarty-3.1.13, created on 2016-04-03 16:55:01
         compiled from "C:\xinghuo\member\shop\html\output_record_list.html" */ ?>
<?php /*%%SmartyHeaderCode:22075700da65d64b15-89988594%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c5652af666e6f225adfa2dc47b7835e6f503dace' => 
    array (
      0 => 'C:\\xinghuo\\member\\shop\\html\\output_record_list.html',
      1 => 1439886530,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '22075700da65d64b15-89988594',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'output_record_list' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5700da65d64b16_03678934',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5700da65d64b16_03678934')) {function content_5700da65d64b16_03678934($_smarty_tpl) {?><!-- 记录列表  begin -->
<table class="table table-striped">
  <thead>
    <tr>
      <th>#</th>
      <th>分销商</th>
      <th>流水号</th>
      <th>金额</th>
      <th>状态</th>
      <th>时间</th>
    </tr>
  </thead>
  <tbody>
  
  <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['output_record_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>
  <tr>
    <td><?php echo $_smarty_tpl->tpl_vars['item']->value['Record_ID'];?>
</td>
    <td><?php echo $_smarty_tpl->tpl_vars['item']->value['User_NickName'];?>
</td>
    <td><?php echo $_smarty_tpl->tpl_vars['item']->value['Record_Sn'];?>
 </td>
    <td class="red">&yen;<?php echo $_smarty_tpl->tpl_vars['item']->value['Record_Money'];?>
</td>
    <td><?php echo $_smarty_tpl->tpl_vars['item']->value['Record_Status'];?>
</td>
     <td><?php echo $_smarty_tpl->tpl_vars['item']->value['Record_CreateTime'];?>
</td>
 
  </tr>
  <?php } ?>
    </tbody>
  
</table>

<!-- 记录列表 end-->
<?php }} ?>