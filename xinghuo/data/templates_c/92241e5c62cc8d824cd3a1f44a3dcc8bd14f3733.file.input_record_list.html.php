<?php /* Smarty version Smarty-3.1.13, created on 2016-03-14 11:20:44
         compiled from "E:\wwwroot\spark\public_html\member\shop\html\input_record_list.html" */ ?>
<?php /*%%SmartyHeaderCode:963056e62e0c27bed7-42866133%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '92241e5c62cc8d824cd3a1f44a3dcc8bd14f3733' => 
    array (
      0 => 'E:\\wwwroot\\spark\\public_html\\member\\shop\\html\\input_record_list.html',
      1 => 1439866386,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '963056e62e0c27bed7-42866133',
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
  'unifunc' => 'content_56e62e0c595635_30694991',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56e62e0c595635_30694991')) {function content_56e62e0c595635_30694991($_smarty_tpl) {?><!-- 记录列表  begin -->
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