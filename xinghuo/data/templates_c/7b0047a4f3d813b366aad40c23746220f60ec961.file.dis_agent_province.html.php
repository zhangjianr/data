<?php /* Smarty version Smarty-3.1.13, created on 2016-03-16 13:16:32
         compiled from "E:\xh\member\shop\html\dis_agent_province.html" */ ?>
<?php /*%%SmartyHeaderCode:2633956e8ec30a91bd8-87361778%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7b0047a4f3d813b366aad40c23746220f60ec961' => 
    array (
      0 => 'E:\\xh\\member\\shop\\html\\dis_agent_province.html',
      1 => 1438950958,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2633956e8ec30a91bd8-87361778',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'region_list' => 0,
    'province_id_list' => 0,
    'i' => 0,
    'region_name' => 0,
    'province_id' => 0,
    'province_data_list' => 0,
    'province' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_56e8ec30bc26e9_03196547',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56e8ec30bc26e9_03196547')) {function content_56e8ec30bc26e9_03196547($_smarty_tpl) {?>画红色删除线表示此省代理资格已被他人获得
<br/>
<br/>
<ul id="J_ProvinceList">
	 <?php  $_smarty_tpl->tpl_vars['province_id_list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['province_id_list']->_loop = false;
 $_smarty_tpl->tpl_vars['region_name'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['region_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["region_list"]['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['province_id_list']->key => $_smarty_tpl->tpl_vars['province_id_list']->value){
$_smarty_tpl->tpl_vars['province_id_list']->_loop = true;
 $_smarty_tpl->tpl_vars['region_name']->value = $_smarty_tpl->tpl_vars['province_id_list']->key;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["region_list"]['index']++;
?>
     <li>
         <div class="dcity clearfix">
    		<div class="ecity gcity">
    			<?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable($_smarty_tpl->getVariable('smarty')->value['foreach']['region_list']['index'], null, 0);?>										
					<span class="group-label"><input value="<?php echo implode($_smarty_tpl->tpl_vars['province_id_list']->value,',');?>
" class="J_Group" id="J_Group_<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" type="checkbox">
					<label for="J_Group_<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['region_name']->value;?>
</label></span>
			</div>
    		<div class="province-list">	
       			<?php  $_smarty_tpl->tpl_vars['province_id'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['province_id']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['province_id_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['province_id']->key => $_smarty_tpl->tpl_vars['province_id']->value){
$_smarty_tpl->tpl_vars['province_id']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['province_id']->key;
?> 
        		<div class="ecity">
					<span class="gareas">
                 	<?php $_smarty_tpl->tpl_vars['province'] = new Smarty_variable($_smarty_tpl->tpl_vars['province_data_list']->value[$_smarty_tpl->tpl_vars['province_id']->value], null, 0);?>
                    <input value="<?php echo $_smarty_tpl->tpl_vars['province_id']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['province']->value['Province_Name'];?>
" id="J_Province_<?php echo $_smarty_tpl->tpl_vars['province_id']->value;?>
" name="J_Province" class="J_Province" type="checkbox" 
                    <?php if ($_smarty_tpl->tpl_vars['province']->value['checked']==true){?>checked<?php }?>  
                    <?php if ($_smarty_tpl->tpl_vars['province']->value['disabled']==true){?>disabled<?php }?> 
                    >
        			
                    <label for="J_Province_<?php echo $_smarty_tpl->tpl_vars['province_id']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['province']->value['disabled']){?>class="del-line"<?php }?>><?php echo $_smarty_tpl->tpl_vars['province']->value['Province_Name'];?>
</label>
  			  		</span>
				</div>
			<?php } ?>
			</div>
    	</div>         	
     </li>	
     <?php } ?>
</ul><?php }} ?>