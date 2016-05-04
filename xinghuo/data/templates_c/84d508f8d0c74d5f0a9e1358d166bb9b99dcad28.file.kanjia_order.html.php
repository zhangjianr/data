<?php /* Smarty version Smarty-3.1.13, created on 2016-04-04 00:01:34
         compiled from "C:\xinghuo2\api\user\html\kanjia_order.html" */ ?>
<?php /*%%SmartyHeaderCode:1733957013e5e69bed0-47885055%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '84d508f8d0c74d5f0a9e1358d166bb9b99dcad28' => 
    array (
      0 => 'C:\\xinghuo2\\api\\user\\html\\kanjia_order.html',
      1 => 1445501431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1733957013e5e69bed0-47885055',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
    'base_url' => 0,
    'Status' => 0,
    'UsersID' => 0,
    'order_list' => 0,
    'item' => 0,
    'Product_List' => 0,
    'Product' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_57013e5e725421_93767930',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57013e5e725421_93767930')) {function content_57013e5e725421_93767930($_smarty_tpl) {?><!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta content="telephone=no" name="format-detection" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
<link href='<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
static/css/global.css' rel='stylesheet' type='text/css' />
<link href='<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
static/api/css/user.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
static/api/js/global.js'></script>
<script type='text/javascript' src='<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
static/api/js/user.js'></script>
</head>

<body>
<script type="text/javascript">$(document).ready(user_obj.message_init);</script>
<div id="message">
  <div class="t"><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</div>
  <!-- 订单状态选择器 begin -->
  <ul id="member_nav">
    <li class="<?php if ($_smarty_tpl->tpl_vars['Status']->value==0){?>cur<?php }?>"><a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/kanjia_order/status/0/">待确认</a></li>
	<li class="<?php if ($_smarty_tpl->tpl_vars['Status']->value==1){?>cur<?php }?>"><a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/kanjia_order/status/1/">待付款</a></li>
    <li class="<?php if ($_smarty_tpl->tpl_vars['Status']->value==2){?>cur<?php }?>"><a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/kanjia_order/status/2/">已付款</a></li>
    <li class="<?php if ($_smarty_tpl->tpl_vars['Status']->value==3){?>cur<?php }?>"><a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/kanjia_order/status/3/">已完成</a></li>
  </ul>
  <!-- 订单状态选择器 end -->
  
  <!-- 订单列表 begin -->
	<div id="order_list">
    <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
    <div class="item">
      <h1>
      订单号：<a href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/kanjia_order/detail/<?php echo $_smarty_tpl->tpl_vars['item']->value['Order_ID'];?>
/?wxref=mp.weixin.qq.com"><?php echo $_smarty_tpl->tpl_vars['item']->value['Order_Sn'];?>
</a>（<strong class="fc_red">￥<?php echo $_smarty_tpl->tpl_vars['item']->value['Order_TotalAmount'];?>
</strong>）  
      </h1>
           
    
     	<?php  $_smarty_tpl->tpl_vars['Product_List'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['Product_List']->_loop = false;
 $_smarty_tpl->tpl_vars['Product_ID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['item']->value['Order_CartList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['Product_List']->key => $_smarty_tpl->tpl_vars['Product_List']->value){
$_smarty_tpl->tpl_vars['Product_List']->_loop = true;
 $_smarty_tpl->tpl_vars['Product_ID']->value = $_smarty_tpl->tpl_vars['Product_List']->key;
?>
 			<?php  $_smarty_tpl->tpl_vars['Product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['Product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['Product_List']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['Product']->key => $_smarty_tpl->tpl_vars['Product']->value){
$_smarty_tpl->tpl_vars['Product']->_loop = true;
?>
            <div class="pro">
			<div class="img"><a href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/kanjia_order/detail/<?php echo $_smarty_tpl->tpl_vars['item']->value['Order_ID'];?>
/?wxref=mp.weixin.qq.com"><img src="<?php echo $_smarty_tpl->tpl_vars['Product']->value['ImgPath'];?>
" width="100" height="100"></a></div>
			<dl class="info">
				<dd class="name"><?php echo $_smarty_tpl->tpl_vars['Product']->value['ProductsName'];?>
</dd>
				<dd>价格:￥<?php echo $_smarty_tpl->tpl_vars['Product']->value['Cur_Price'];?>
×<?php echo $_smarty_tpl->tpl_vars['Product']->value['Qty'];?>
=￥<?php echo $_smarty_tpl->tpl_vars['Product']->value['Cur_Price']*$_smarty_tpl->tpl_vars['Product']->value['Qty'];?>
</dd></dl>
			<div class="clear"></div>
			</div>
          	<?php } ?>
        <?php } ?>
   </div>
    <?php } ?>
  </div>
  
  <!-- 订单列表 begin -->
  
  
  </div>
<div id="footer_user_points"></div>
<?php echo $_smarty_tpl->getSubTemplate ("lbi/footer_user.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


</body>
</html><?php }} ?>