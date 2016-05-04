<?php /* Smarty version Smarty-3.1.13, created on 2016-03-14 13:47:19
         compiled from "E:\wwwroot\spark\public_html\api\kanjia\skin\1\activity.html" */ ?>
<?php /*%%SmartyHeaderCode:2004956e65067d98548-32783413%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '592cbf2597ff7cf6b47b5a3bea39a86e8b11129a' => 
    array (
      0 => 'E:\\wwwroot\\spark\\public_html\\api\\kanjia\\skin\\1\\activity.html',
      1 => 1438833314,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2004956e65067d98548-32783413',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'base_url' => 0,
    'product' => 0,
    'deadline_array' => 0,
    'UsersID' => 0,
    'cur_price' => 0,
    'activity' => 0,
    'Product_Property' => 0,
    'PName' => 0,
    'Property' => 0,
    'value_name' => 0,
    'kanjia_url' => 0,
    'self_kaned' => 0,
    'expired' => 0,
    'KanjiaID' => 0,
    'member_activity' => 0,
    'helper_list' => 0,
    'item' => 0,
    'Helper_ID' => 0,
    'user_list' => 0,
    'public' => 0,
    'time_interval' => 0,
    'share_flag' => 0,
    'appId' => 0,
    'timestamp' => 0,
    'noncestr' => 0,
    'url' => 0,
    'signature' => 0,
    'desc' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_56e6506844bf89_36165733',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56e6506844bf89_36165733')) {function content_56e6506844bf89_36165733($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("lbi/header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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
  <div class="content container">
    <div class="row">
      <div class="activity-image">
      <img  width="100%" src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
<?php echo $_smarty_tpl->tpl_vars['product']->value['thumb'];?>
"/>
      <a class="deadline" href="javascript:void()"><i class="icon-time"></i>活动剩余时间
    <span ><strong id="day_show"><?php echo $_smarty_tpl->tpl_vars['deadline_array']->value['day'];?>
</strong>天</span>
    <span ><strong id="hour_show"><?php echo $_smarty_tpl->tpl_vars['deadline_array']->value['hour'];?>
</strong>小时</span>
    <span ><strong id="minute_show"><?php echo $_smarty_tpl->tpl_vars['deadline_array']->value['minute'];?>
</strong>分</span>
    <span ><strong id="second_show"><?php echo $_smarty_tpl->tpl_vars['deadline_array']->value['second'];?>
</strong>秒</span>
    </a>
      </div>
    </div>
    <div class="row">

      <div class="desc">
       <form action="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/kanjia/" method="post" id="addto_cart_form">
        <input type="hidden" name="Product_ID" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['Products_ID'];?>
"/>
        <input type="hidden" name="Cur_Price" value="<?php echo $_smarty_tpl->tpl_vars['cur_price']->value;?>
" />
        <h4><?php echo $_smarty_tpl->tpl_vars['product']->value['Products_Name'];?>
</h4>
        
        <span class="cur_price">当前价格:<strong class="red">&yen;<?php echo $_smarty_tpl->tpl_vars['cur_price']->value;?>
</strong></span> 底价: &yen;<?php echo $_smarty_tpl->tpl_vars['activity']->value['Bottom_Price'];?>
 <span class="grey">|</span> 原价: &yen;<?php echo $_smarty_tpl->tpl_vars['product']->value['Products_PriceX'];?>

      <?php if (count($_smarty_tpl->tpl_vars['Product_Property']->value)>0){?>  
       <ul class="info">
        <?php  $_smarty_tpl->tpl_vars['Property'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['Property']->_loop = false;
 $_smarty_tpl->tpl_vars['PName'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['Product_Property']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['Property']->key => $_smarty_tpl->tpl_vars['Property']->value){
$_smarty_tpl->tpl_vars['Property']->_loop = true;
 $_smarty_tpl->tpl_vars['PName']->value = $_smarty_tpl->tpl_vars['Property']->key;
?>
          <li class="property">
            <table>
              <tr>
                <td nowrap="nowrap"><?php echo $_smarty_tpl->tpl_vars['PName']->value;?>
:&nbsp;</td>
                
                <td>
                <?php  $_smarty_tpl->tpl_vars['value_value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value_value']->_loop = false;
 $_smarty_tpl->tpl_vars['value_name'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['Property']->value['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['value_value']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['value_value']->key => $_smarty_tpl->tpl_vars['value_value']->value){
$_smarty_tpl->tpl_vars['value_value']->_loop = true;
 $_smarty_tpl->tpl_vars['value_name']->value = $_smarty_tpl->tpl_vars['value_value']->key;
 $_smarty_tpl->tpl_vars['value_value']->iteration++;
?>
               		 <?php if ($_smarty_tpl->tpl_vars['value_value']->iteration==1){?>	 
                     	<span PName="<?php echo $_smarty_tpl->tpl_vars['PName']->value;?>
" class="cur"><?php echo $_smarty_tpl->tpl_vars['value_name']->value;?>
</span> 
                	 <?php }else{ ?>
                     	<span PName="<?php echo $_smarty_tpl->tpl_vars['PName']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['value_name']->value;?>
</span> 
                     <?php }?>
                <?php } ?>
                </td>
              </tr>
            </table>
            <input type="hidden" name="Property[<?php echo $_smarty_tpl->tpl_vars['PName']->value;?>
]" value="<?php echo $_smarty_tpl->tpl_vars['Property']->value['default'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['PName']->value;?>
" />
          </li>
          <?php } ?>
        </ul>
       <?php }?>
        <input type="hidden" name="action" value="addto_cart" />
       </form>
       
           <div class="go-detail">
	 			 <a href="<?php echo $_smarty_tpl->tpl_vars['kanjia_url']->value;?>
product/<?php echo $_smarty_tpl->tpl_vars['product']->value['Products_ID'];?>
/"><span>&nbsp;</span>商品详细描述</a>
          </div>
       
      </div>
      
   
     
    </div>
    
    <div class="row">
     
      
      <input type="hidden" id="self_kaned" value="<?php echo $_smarty_tpl->tpl_vars['self_kaned']->value;?>
"/>
      <?php if ($_smarty_tpl->tpl_vars['expired']->value==0){?>
		
		<?php if ($_smarty_tpl->tpl_vars['cur_price']->value>$_smarty_tpl->tpl_vars['activity']->value['Bottom_Price']){?>	
			<div class="button-panel container"> 
			<?php if ($_smarty_tpl->tpl_vars['self_kaned']->value==0){?>
				<p>自砍一刀参加此活动</p>
				<button id="self_kan" class="btn btn-danger col-xs-10" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
api/kanjia/help.php?UsersID=<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
&KanjiaID=<?php echo $_smarty_tpl->tpl_vars['KanjiaID']->value;?>
&action=self_kan">自砍一刀</button> <br/>
				<br/>
			<?php }else{ ?>
				<p>您自己砍掉了<span class="red"><?php echo $_smarty_tpl->tpl_vars['member_activity']->value['Self_Kan'];?>
</span>元</p>
			<?php }?>
			<button id="invite_kan" class="btn btn-warning col-xs-10" href="#" role="button">邀请好友帮我砍价</button>
			<div class="clear"></div>
			</div>
		<?php }else{ ?>
			<p >
      			<h4 style="text-align:center;margin-top:10px;">此产品已经到底价,不能再邀请好友砍价</h4>
      		</p>	
   	    <?php }?>
	  <?php }else{ ?>
      		 <p >
      			<h4 style="text-align:center;margin-top:10px;">此活动已经过期</h4>
      		</p>
      <?php }?>
    </div>
    <div class="row">
      <div class="kanjia_list">
        <div class="title">
          <h5>砍友榜</h5>
          <hr/>
        </div>
        <div class="container">
          <div class="row"> <?php if (count($_smarty_tpl->tpl_vars['helper_list']->value)>0){?>
            <table class="table">
              <tbody>
                <tr>
                  <td>头像</td>
                  <td>昵称</td>
                  <td>砍掉金额</td>
                </tr>
              <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['helper_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
       			<?php $_smarty_tpl->tpl_vars["Helper_ID"] = new Smarty_variable($_smarty_tpl->tpl_vars['item']->value['Helper_ID'], null, 0);?>
              <tr>
                <td><img width="50px" height="50px" src="<?php echo $_smarty_tpl->tpl_vars['user_list']->value[$_smarty_tpl->tpl_vars['Helper_ID']->value]['User_HeadImg'];?>
"/></td>
                <td><?php echo $_smarty_tpl->tpl_vars['user_list']->value[$_smarty_tpl->tpl_vars['Helper_ID']->value]['User_NickName'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['item']->value['Record_Reduce'];?>
</td>
              </tr>
              <?php } ?>
                </tbody>
              
            </table>
            <?php }else{ ?>
            &nbsp;&nbsp;&nbsp;&nbsp;目前没有人帮你砍价！！！
            <?php }?> </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- 主题内容end --> 
</div>

<!-- 分享提示遮罩begin -->
<div class='share_layer'><img src='/static/api/kanjia/image/share.png' /></div>
<!-- 分享提示遮罩end --> 

<!-- 固定footer begin -->
<footer class="footer">
  <div class="container">
    <ul class="buy-panel row">
      <li class="col-xs-5" style="text-align:left;"> 现价: <span class="red">&yen;<?php echo $_smarty_tpl->tpl_vars['cur_price']->value;?>
</span> </li>
      <li class="col-xs-5" style="text-align:right"> 
      <?php if ($_smarty_tpl->tpl_vars['expired']->value==0){?>
     	 <a  id="buy_btn" class="btn btn-warning input-sm" href="<?php echo $_smarty_tpl->tpl_vars['kanjia_url']->value;?>
buy/<?php echo $_smarty_tpl->tpl_vars['KanjiaID']->value;?>
/" role="button">立即购买</a> 
      <?php }else{ ?>
      	 <?php echo ad($_smarty_tpl->tpl_vars['UsersID']->value,2,2);?>

      <?php }?>
      </li>
      </li>
    </ul>
  </div>
</footer>
<!-- 固定footer end --> 


<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="<?php echo $_smarty_tpl->tpl_vars['public']->value;?>
js/kanjia.js"></script> 
<script type="text/javascript">
	 	  var UsersID = "<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
";
          var base_url = "<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
";
		  var Kanjia_ID = "<?php echo $_smarty_tpl->tpl_vars['KanjiaID']->value;?>
";
		  var time_interval = "<?php echo $_smarty_tpl->tpl_vars['time_interval']->value;?>
";
	 	  kanjia_obj.activity_init();
	 </script>
	 <?php if ($_smarty_tpl->tpl_vars['share_flag']->value){?>
	<script language="javascript">
		var share_config = {
		   appId:"<?php echo $_smarty_tpl->tpl_vars['appId']->value;?>
",		   
		   timestamp:<?php echo $_smarty_tpl->tpl_vars['timestamp']->value;?>
,
		   nonceStr:"<?php echo $_smarty_tpl->tpl_vars['noncestr']->value;?>
",
		   url:"<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
",
		   signature:"<?php echo $_smarty_tpl->tpl_vars['signature']->value;?>
",
		   title:"<?php echo $_smarty_tpl->tpl_vars['product']->value['Products_Name'];?>
",
		   desc:"<?php echo $_smarty_tpl->tpl_vars['desc']->value;?>
",
		   img_url:"<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
<?php echo $_smarty_tpl->tpl_vars['product']->value['thumb'];?>
",
		   link:""
		};
		$(document).ready(global_obj.share_init_config);
	</script>
	<?php }?>
</body>
</html><?php }} ?>