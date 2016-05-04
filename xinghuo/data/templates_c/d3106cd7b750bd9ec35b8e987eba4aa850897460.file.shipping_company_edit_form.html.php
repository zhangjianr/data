<?php /* Smarty version Smarty-3.1.13, created on 2016-04-28 16:45:28
         compiled from "C:\xinhuo\xinghuo\member\shop\html\shipping_company_edit_form.html" */ ?>
<?php /*%%SmartyHeaderCode:215705721cda87853a5-67386363%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd3106cd7b750bd9ec35b8e987eba4aa850897460' => 
    array (
      0 => 'C:\\xinhuo\\xinghuo\\member\\shop\\html\\shipping_company_edit_form.html',
      1 => 1436861972,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '215705721cda87853a5-67386363',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'Shipping' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5721cda87c2422_17661462',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5721cda87c2422_17661462')) {function content_5721cda87c2422_17661462($_smarty_tpl) {?><form class="form" action="shipping.php" method="post" id="create_shipping_form" name="mod_create_shipping">
        <p class="rows">
        <label for="Shipping_Name">名称</label>
        <input type="text" value="<?php echo $_smarty_tpl->tpl_vars['Shipping']->value['Shipping_Name'];?>
" class="{required:true}" name="Shipping_Name"  />
        </p> 
        
       
        
        <p class="rows">
        <label>状态</label>
           
           <input name="Shipping_Status" value="1"  type="radio" <?php if ($_smarty_tpl->tpl_vars['Shipping']->value['Shipping_Status']==1){?>checked<?php }?> />&nbsp;&nbsp;可用
           <input name="Shipping_Status" value="0"  type="radio"  <?php if ($_smarty_tpl->tpl_vars['Shipping']->value['Shipping_Status']==0){?>checked<?php }?> />&nbsp;&nbsp; 不可用
        </p> 
     
       <p class="rows">
        <label></label>
          
        <input type="submit" value="确定提交" name="submit_btn">
      
      </div>
      
      <input type="hidden" name="Shipping_ID" value="<?php echo $_smarty_tpl->tpl_vars['Shipping']->value['Shipping_ID'];?>
">  
      <input type="hidden" name="action" value="edit_shipping_company">  
</form><?php }} ?>