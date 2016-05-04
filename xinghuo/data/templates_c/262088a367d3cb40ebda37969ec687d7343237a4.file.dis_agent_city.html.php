<?php /* Smarty version Smarty-3.1.13, created on 2016-03-16 13:16:32
         compiled from "E:\xh\member\shop\html\dis_agent_city.html" */ ?>
<?php /*%%SmartyHeaderCode:2839256e8ec30bc26e0-42482822%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '262088a367d3cb40ebda37969ec687d7343237a4' => 
    array (
      0 => 'E:\\xh\\member\\shop\\html\\dis_agent_city.html',
      1 => 1438950872,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2839256e8ec30bc26e0-42482822',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'region_list' => 0,
    'i' => 0,
    'region_name' => 0,
    'province_id_list' => 0,
    'province_id' => 0,
    'province_data_list' => 0,
    'province' => 0,
    'city_data_list' => 0,
    'city_list' => 0,
    'city_id' => 0,
    'city' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_56e8ec30bff5e9_15855325',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56e8ec30bff5e9_15855325')) {function content_56e8ec30bff5e9_15855325($_smarty_tpl) {?><span>绿色数字为某省下自己代理城市的数目，红色为此省下被其他人占用城市的数目</span>
<br/>
<br/>
<ul id="K_CityList">
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
					<span class="group-label">
					<label for="K_Group_<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
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
                   
                    <label for="K_Province_<?php echo $_smarty_tpl->tpl_vars['province_id']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['province']->value['Province_Name'];?>
</label>
                    <?php if ($_smarty_tpl->tpl_vars['province']->value['selectd_city_num']>0){?>
                    	<span class="check_num">
                    		<?php echo $_smarty_tpl->tpl_vars['province']->value['selectd_city_num'];?>
,
                        </span>
                    <?php }?>
                    
                    <?php if ($_smarty_tpl->tpl_vars['province']->value['disable_city_num']>0){?>
                    	<span class="disable_num">
                    		<?php echo $_smarty_tpl->tpl_vars['province']->value['disable_city_num'];?>

                        </span>
                    <?php }?>
                    <img class="trigger" src="/static/member/images/shop/city_down_icon.gif">
  			  		</span>
                    
                    <div class="citys">
             
           
		     <?php $_smarty_tpl->tpl_vars['city_list'] = new Smarty_variable($_smarty_tpl->tpl_vars['city_data_list']->value[$_smarty_tpl->tpl_vars['province_id']->value], null, 0);?>	
             <?php  $_smarty_tpl->tpl_vars['city'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['city']->_loop = false;
 $_smarty_tpl->tpl_vars['city_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['city_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['city']->key => $_smarty_tpl->tpl_vars['city']->value){
$_smarty_tpl->tpl_vars['city']->_loop = true;
 $_smarty_tpl->tpl_vars['city_id']->value = $_smarty_tpl->tpl_vars['city']->key;
?>	
             
                <span class="areas">
             
                <input value="<?php echo $_smarty_tpl->tpl_vars['city_id']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['city']->value['City_Name'];?>
" id="K_City_<?php echo $_smarty_tpl->tpl_vars['city_id']->value;?>
" name="K_City" class="K_City" type="checkbox"
                <?php if ($_smarty_tpl->tpl_vars['city']->value['checked']==true){?>checked<?php }?>  
                <?php if ($_smarty_tpl->tpl_vars['city']->value['disabled']==true){?>disabled<?php }?> 
                
                >
				<label for="K_City_<?php echo $_smarty_tpl->tpl_vars['city_id']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['city']->value['disabled']){?>class="del-line"<?php }?>><?php echo $_smarty_tpl->tpl_vars['city']->value['City_Name'];?>
</label></span>
       		 
             <?php } ?>	
				<p style="text-align:right;"><input value="关闭" class="close_button" type="button"></p>
		</div>
				</div>
			<?php } ?>
			</div>
    	</div>         	
     </li>	
     <?php } ?>
</ul><?php }} ?>