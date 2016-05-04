<?php /* Smarty version Smarty-3.1.13, created on 2016-04-28 15:56:08
         compiled from "C:\xinhuo\xinghuo\member\shop\html\shipping_tpl_item.html" */ ?>
<?php /*%%SmartyHeaderCode:232085721c2183092a4-33883208%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1461fd5300a3aacf7e29df0c8659f55a5671d9d9' => 
    array (
      0 => 'C:\\xinhuo\\xinghuo\\member\\shop\\html\\shipping_tpl_item.html',
      1 => 1436861346,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '232085721c2183092a4-33883208',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'business_alias' => 0,
    'template_content' => 0,
    'unit' => 0,
    'method_name' => 0,
    'v' => 0,
    'k' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5721c2183fd4e8_62778167',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5721c2183fd4e8_62778167')) {function content_5721c2183fd4e8_62778167($_smarty_tpl) {?><p>除指定地区外，其他地区运费采用默认运费(此部分内容只能填写数字)</p>

<div class="section">
   
    <div class="poster_detail">
        <!--  默认运费begin -->
        <div class="default J_DefaultSet">
            <?php if (!empty($_smarty_tpl->tpl_vars['template_content']->value[$_smarty_tpl->tpl_vars['business_alias']->value])){?> 默认运费：
            <input name="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_start" value="<?php echo $_smarty_tpl->tpl_vars['template_content']->value[$_smarty_tpl->tpl_vars['business_alias']->value]['default']['start'];?>
" data-field="start" class="input-text" autocomplete="off" maxlength="6" aria-label="默认运费<?php echo $_smarty_tpl->tpl_vars['unit']->value;?>
数" type="text" notnull><?php echo $_smarty_tpl->tpl_vars['unit']->value;?>
内，
            <input data-field="postage" name="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_postage" value="<?php echo $_smarty_tpl->tpl_vars['template_content']->value[$_smarty_tpl->tpl_vars['business_alias']->value]['default']['postage'];?>
" class="input-text" autocomplete="off" maxlength="6" aria-label="默认运费价格" type="text" notnull> 元， 每增加
            <input name="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_plus" data-field="plus" value="<?php echo $_smarty_tpl->tpl_vars['template_content']->value[$_smarty_tpl->tpl_vars['business_alias']->value]['default']['plus'];?>
" class="input-text" autocomplete="off" maxlength="6" aria-label="每加<?php echo $_smarty_tpl->tpl_vars['unit']->value;?>
" type="text" notnull> <?php echo $_smarty_tpl->tpl_vars['unit']->value;?>
， 增加运费
            <input name="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_postageplus" data-field="postageplus" value="<?php echo $_smarty_tpl->tpl_vars['template_content']->value[$_smarty_tpl->tpl_vars['business_alias']->value]['default']['postageplus'];?>
" class="input-text" autocomplete="off" maxlength="6" aria-label="加<?php echo $_smarty_tpl->tpl_vars['unit']->value;?>
运费" type="text" notnull>元 
            <?php }else{ ?> 
            默认运费：
            <input name="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_start" value="1" data-field="start" class="input-text" autocomplete="off" maxlength="6" aria-label="默认运费<?php echo $_smarty_tpl->tpl_vars['unit']->value;?>
数" type="text" notnull><?php echo $_smarty_tpl->tpl_vars['unit']->value;?>
内，
            <input data-field="postage" name="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_postage" value="" class="input-text" autocomplete="off" maxlength="6" aria-label="默认运费价格" type="text" notnull> 元， 每增加
            <input name="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_plus" data-field="plus" value="1" class="input-text" autocomplete="off" maxlength="6" aria-label="每加<?php echo $_smarty_tpl->tpl_vars['unit']->value;?>
" type="text" notnull> <?php echo $_smarty_tpl->tpl_vars['unit']->value;?>
， 增加运费
            <input style="display: none;" class="j_sellerBearFrePrice" value="0.00" disabled="disabled" type="text">
            <input name="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_postageplus" data-field="postageplus" value="" class="input-text" autocomplete="off" maxlength="6" aria-label="加<?php echo $_smarty_tpl->tpl_vars['unit']->value;?>
运费" type="text" notnull>元
            <?php }?>
        </div>
        <!-- 默认运费end -->
        <!-- 特殊地区设置begin -->
        <?php if (!empty($_smarty_tpl->tpl_vars['template_content']->value[$_smarty_tpl->tpl_vars['business_alias']->value])){?>	
        	<?php if ($_smarty_tpl->tpl_vars['template_content']->value[$_smarty_tpl->tpl_vars['business_alias']->value]['except']==true){?>
        		<div class="tpl_except">
            <table cellspacing="0">
                <tbody>
                    <tr>
                        <th>运送到</th>
                        <th>按<?php echo $_smarty_tpl->tpl_vars['method_name']->value;?>
(<?php echo $_smarty_tpl->tpl_vars['unit']->value;?>
)</th>
                        <th>首费</th>
                        <th>续<?php echo $_smarty_tpl->tpl_vars['method_name']->value;?>
(<?php echo $_smarty_tpl->tpl_vars['unit']->value;?>
)</th>
                        <th>续费</th>
                        <th>操作</th>
                    </tr>
                    
                    
                        <?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['template_content']->value[$_smarty_tpl->tpl_vars['business_alias']->value]['specify']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
                   			<tr>
                        <td><span><?php if (!empty($_smarty_tpl->tpl_vars['v']->value['desc'])==true){?> <?php echo $_smarty_tpl->tpl_vars['v']->value['desc'];?>
 <?php }else{ ?>未指定区域<?php }?></span> <a class="Edit_Area" href="javascript:void(0)" area_type="deliver_business" area_value_container="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_areas_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
">编辑</a>
                            <input name="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_areas_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_areas_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" class="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_areas" value="<?php echo $_smarty_tpl->tpl_vars['v']->value['areas'];?>
" type="hidden">
                            <input name="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_desc_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" class="area_desc" value="" type="hidden"> </td>
                        <td>
                            <input name="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_start_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" data-field="start" value="<?php echo $_smarty_tpl->tpl_vars['v']->value['start'];?>
" class="input-text " autocomplete="off" maxlength="6" aria-label="首kg" notnull="" type="text"> </td>
                        <td>
                            <input name="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_postage_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" data-field="postage" value="<?php echo $_smarty_tpl->tpl_vars['v']->value['postage'];?>
" class="input-text" autocomplete="off" maxlength="6" aria-label="首费" notnull="" type="text"> </td>
                        <td>
                            <input name="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_plus_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" data-field="plus" value="<?php echo $_smarty_tpl->tpl_vars['v']->value['plus'];?>
" class="input-text " autocomplete="off" maxlength="6" aria-label="续kg" notnull="" type="text"> </td>
                        <td>
                            <input name="<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
_postageplus_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" data-field="postageplus" value="<?php echo $_smarty_tpl->tpl_vars['v']->value['postageplus'];?>
" class="input-text " autocomplete="off" maxlength="6" aria-label="续费" notnull="" type="text"> </td>
                        <td><a href="javascript:void(0)" class="delete_rule_link">删除</a></td>
                    </tr>
                    	<?php } ?>
               		
                </tbody>
            </table>
        </div>
        	<?php }?>
        <?php }?>
        <!-- 特殊地区设置end -->
        <div class="tbl-attach">
            <div class="J_SpecialMessage"></div>
            <a href="javascript:void(0)" class="AddRule_Link" business_alias='<?php echo $_smarty_tpl->tpl_vars['business_alias']->value;?>
'>为指定地区城市设置运费</a>
        </div>
    </div>
</div>

<?php }} ?>