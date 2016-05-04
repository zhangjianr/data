<?php /* Smarty version Smarty-3.1.13, created on 2016-03-14 12:05:47
         compiled from "E:\wwwroot\spark\public_html\member\shop\html\withdraw_method_edit_form.html" */ ?>
<?php /*%%SmartyHeaderCode:3124356e6389b3769b9-08495783%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7b843f6fb33ab773b518040aff1b3bc961b85667' => 
    array (
      0 => 'E:\\wwwroot\\spark\\public_html\\member\\shop\\html\\withdraw_method_edit_form.html',
      1 => 1454170428,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3124356e6389b3769b9-08495783',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'Method' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_56e6389b521db4_95847508',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56e6389b521db4_95847508')) {function content_56e6389b521db4_95847508($_smarty_tpl) {?><form class="form" action="withdraw_method.php" method="post" id="create_method_form" name="mod_create_method">
    		
          <input type="hidden" name="Method_ID" value="<?php echo $_smarty_tpl->tpl_vars['Method']->value['Method_ID'];?>
" />  
         <div class="rows">
          <label>类型</label>
          <span class="input">  
          <input value="bank_card" name="Method_Type" type="radio" <?php if ($_smarty_tpl->tpl_vars['Method']->value['Method_Type']=='bank_card'){?>checked<?php }?> >&nbsp;&nbsp;银行卡
          <input value="red" name="Method_Type" type="radio" <?php if ($_smarty_tpl->tpl_vars['Method']->value['Method_Type']=='red'){?>checked<?php }?> >&nbsp;&nbsp;微信企业付款
          <input value="alipay" name="Method_Type" type="radio" <?php if ($_smarty_tpl->tpl_vars['Method']->value['Method_Type']=='alipay'){?>checked<?php }?> >&nbsp;&nbsp; 支付宝
</span>
          <div class="clear"></div>
        </div>
          
        <div class="rows method_name_rows" >
        	<label>名称</label>
            <span class="input"><input type="text" value="<?php echo $_smarty_tpl->tpl_vars['Method']->value['Method_Name'];?>
" class="form_input" name="Method_Name"  notnull /></span>
            <div class="clear"></div>
        </div>
		
		<div class="rows method_type_rows" <?php if ($_smarty_tpl->tpl_vars['Method']->value['Method_Type']!='red'){?>style="display:none"<?php }?>>
			<label>方式</label>
			<span class="input">
				<input value="0" name="isAuto" type="radio" <?php if ($_smarty_tpl->tpl_vars['Method']->value['isAuto']=='0'){?>checked<?php }?> checked>&nbsp;&nbsp;审核
				<input value="1" name="isAuto" type="radio" <?php if ($_smarty_tpl->tpl_vars['Method']->value['isAuto']=='1'){?>checked<?php }?>>&nbsp;&nbsp; 自动
			</span>
			<div class="clear"></div>
		</div>
		
         <div class="rows">
          <label>状态</label>
          <span class="input">  <input value="1" name="Status" type="radio" <?php if ($_smarty_tpl->tpl_vars['Method']->value['Status']=='1'){?>checked<?php }?> checked>&nbsp;&nbsp;可用
           <input value="0" name="Status" type="radio" <?php if ($_smarty_tpl->tpl_vars['Method']->value['Status']=='0'){?>checked<?php }?>>&nbsp;&nbsp; 不可用
</span>
          <div class="clear"></div>
        </div>
           
       <div class="rows">
        <label></label>
        <span class="submit">
        <input type="submit" value="确定提交" name="submit_btn">
        </span>
        <div class="clear"></div>
      </div>
      <input type="hidden" name="action" value="edit_method">  
         </form><?php }} ?>