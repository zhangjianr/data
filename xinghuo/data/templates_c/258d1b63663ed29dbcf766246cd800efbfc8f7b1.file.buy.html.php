<?php /* Smarty version Smarty-3.1.13, created on 2016-03-14 13:47:34
         compiled from "E:\wwwroot\spark\public_html\api\kanjia\skin\1\buy.html" */ ?>
<?php /*%%SmartyHeaderCode:80456e65076c67870-50912167%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '258d1b63663ed29dbcf766246cd800efbfc8f7b1' => 
    array (
      0 => 'E:\\wwwroot\\spark\\public_html\\api\\kanjia\\skin\\1\\buy.html',
      1 => 1441964125,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '80456e65076c67870-50912167',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'base_url' => 0,
    'address_list' => 0,
    'key' => 0,
    'item' => 0,
    'activity' => 0,
    'member_activity' => 0,
    'shipping_price' => 0,
    'shipping_list' => 0,
    'order_sum' => 0,
    'KanjiaID' => 0,
    'public' => 0,
    'UsersID' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_56e65076f06eb8_54142829',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56e65076f06eb8_54142829')) {function content_56e65076f06eb8_54142829($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("lbi/header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

     <script type='text/javascript' src='<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
static/js/plugin/pcas/pcas.js'></script> 
  </head>
  <body>
<div id="wrap">
    <!-- 导航栏begin -->
	<?php echo $_smarty_tpl->getSubTemplate ("lbi/top_nav.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    <!-- 导航栏end -->
	
	<!-- 标题栏begin -->
 	<?php echo $_smarty_tpl->getSubTemplate ("lbi/title_bar.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<!-- 标题栏end -->

	<!-- 主题内容begin -->
    	<div class="content">
    
            	<div class="order_info container">
            		<form id="order_form" name="order_form" class="row" method="post">
                    
                    <div id="address_info" class="well">
                    	<h5>联系人信息</h5>
                        <ul >
                        	<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['address_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>
                            
                            <li>
                             <?php if ($_smarty_tpl->tpl_vars['key']->value==0){?>
                            	<input type="radio"  value="<?php echo $_smarty_tpl->tpl_vars['item']->value['Address_ID'];?>
" checked="true" name="AddressID" />
                             <?php }else{ ?>
                                <input type="radio"  value="<?php echo $_smarty_tpl->tpl_vars['item']->value['Address_ID'];?>
"  name="AddressID"/>
                             <?php }?>
                            <?php echo $_smarty_tpl->tpl_vars['item']->value['Address_Province'];?>
<?php echo $_smarty_tpl->tpl_vars['item']->value['Address_City'];?>
<?php echo $_smarty_tpl->tpl_vars['item']->value['Address_Area'];?>
【<?php echo $_smarty_tpl->tpl_vars['item']->value['Address_Detailed'];?>
,<?php echo $_smarty_tpl->tpl_vars['item']->value['Address_Mobile'];?>
】
                            </li>
                            
                            <?php } ?>
                            <li>
                            <input type="radio" name="AddressID" value="0" id="user_new"/>
                            使用新的联系人地址
                            </li>
                        	 
                        </ul>
                     <div id="new_address_info">   
                     <div class="form-group">
                     	<label>姓名</label>
                        <input type="text" class="form-control input-sm" name="Name"  placeholder="姓名" notnull/>
                     </div>
                     <div class="form-group">
                    	<label>手机</label>
                        <input type="text" class="form-control input-sm"  name="Mobile" value=""  notnull/>
                     </div>
                     
                     <div id="diqu" class="form-group">
                     	<label>所在地区</label>
                        <br/>
                        <select class="input-sm form-control col-xs-3" name="Province"  notnull ></select>
                        <select class="input-sm form-control col-xs-2" name="City" notnull ></select>
                        <select class="input-sm form-control col-xs-2 " name="Area" notnull ></select>
						<script type="text/javascript">new PCAS("Province","City","Area");</script>
                        <div class="clearfix"></div>
                     </div>
                     
                     <div class="form-group">
                     	<label>详细地址</label>
                     	<input class="form-control input-sm" type="text" name="Detailed" value="" notnull />
                     </div>
                     
                     </div>
                  
                    </div>
                	<div id="product_info" class="well">
                    <input type="hidden" name="Product_ID" value="<?php echo $_smarty_tpl->tpl_vars['activity']->value['Product_ID'];?>
"/> 
                    <input type="hidden" name="total_price" id="total_price" value="<?php echo $_smarty_tpl->tpl_vars['member_activity']->value['Cur_Price'];?>
"/>
                         <h5>订单商品信息</h5>
                        
                    	<table class="table">
                        <tr>
                        	<td>名称</td><td> 数量</td><td> 价格 </td>
                        </tr>	
                        <tr>
                        	<td><?php echo $_smarty_tpl->tpl_vars['activity']->value['Product_Name'];?>
</td> <td>1 </td><td>&yen;&nbsp;<span class="red" ><?php echo $_smarty_tpl->tpl_vars['member_activity']->value['Cur_Price'];?>
</span></td>  
                        	
                        </tr>
                        </table>
                    </div> 
               		
                    <div id="shipping_info" class="well">
                		<h5>选择配送方式</h5>
                        <input type="hidden" name="shipping_price" id="shipping_price" value="<?php echo $_smarty_tpl->tpl_vars['shipping_price']->value;?>
" />
                        <table class="table">
                        	<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['shipping_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>
                            
                            <tr><td>
                            <?php if ($_smarty_tpl->tpl_vars['key']->value==0){?>
                            	<input type="radio" price="<?php echo $_smarty_tpl->tpl_vars['item']->value['Base_Price'];?>
" checked="true" name="Shipping[Express]" value="<?php echo $_smarty_tpl->tpl_vars['item']->value['Express'];?>
"/>
                            <?php }else{ ?>
                            	<input type="radio" price="<?php echo $_smarty_tpl->tpl_vars['item']->value['Base_Price'];?>
" name="Shipping[Express]" value="<?php echo $_smarty_tpl->tpl_vars['item']->value['Express'];?>
"/>
                            <?php }?>
                            </td>
                            
                            <td><?php echo $_smarty_tpl->tpl_vars['item']->value['Express'];?>
</td>
                            
                            <td>&yen;&nbsp;<span class="red"><?php echo $_smarty_tpl->tpl_vars['item']->value['Price'];?>
</span></td>
                            </tr>
                            <?php } ?>
                            
                            
                        </table>
                     
               	    </div>
                    
                    <div id="order_remark" class="well">
                    	<h5>订单备注</h5>
                        <textarea  name="Remark" class="form-control" rows="3"></textarea>
                    </div>
                  
                    <div class="order_sum">
                       <div class="container">
                         <p style="text-align:right;">订单总价:&yen;<span class="red" id="order_sum"><?php echo $_smarty_tpl->tpl_vars['order_sum']->value;?>
</span>&nbsp;&nbsp;&nbsp;&nbsp;</p>
                         <p style="text-align:center;"><button type="button"  id="submit" class="btn btn-danger">提交订单</button></p>
                       </div>
                   
                       </div>
                
                    <input type="hidden" name="action" value="confirm_order"/>
					<input type="hidden" name="KanjiaID" value="<?php echo $_smarty_tpl->tpl_vars['KanjiaID']->value;?>
" />
                    </form>  
                    </div>
                </div>
                
              	
    	</div>
    
     <!-- 主题内容end -->
</div>
    
    
    
    <!-- 固定footer begin -->
    	<footer class="footer">
        	   <div class="container">
                
               </div>
  		</footer>
    <!-- 固定footer end -->
	
   
	
 
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo $_smarty_tpl->tpl_vars['public']->value;?>
js/kanjia.js"></script>
 
     <script type="text/javascript">
	 	  var base_url = "<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
";
		  var Kanjia_ID = "<?php echo $_smarty_tpl->tpl_vars['KanjiaID']->value;?>
";
		  var UsersID = "<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
";
	 	  kanjia_obj.buy_init();
	 </script>
  </body>
</html><?php }} ?>