<?php /* Smarty version Smarty-3.1.13, created on 2016-03-14 13:47:14
         compiled from "E:\wwwroot\spark\public_html\api\kanjia\skin\1\index.html" */ ?>
<?php /*%%SmartyHeaderCode:970656e65062263898-64332832%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a49dedb636cfd5753ae333ead1121fc5f0cc2a15' => 
    array (
      0 => 'E:\\wwwroot\\spark\\public_html\\api\\kanjia\\skin\\1\\index.html',
      1 => 1430472302,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '970656e65062263898-64332832',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'filter' => 0,
    'base_url' => 0,
    'UsersID' => 0,
    'kanjia_list' => 0,
    'item' => 0,
    'kanjia_id' => 0,
    'member_kanjia_list' => 0,
    'public' => 0,
    'share_flag' => 0,
    'appId' => 0,
    'timestamp' => 0,
    'noncestr' => 0,
    'url' => 0,
    'signature' => 0,
    'title' => 0,
    'desc' => 0,
    'img_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_56e650626eb357_72892843',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56e650626eb357_72892843')) {function content_56e650626eb357_72892843($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("lbi/header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

  </head>
  
  <body id="home_body">
  <div id="wrap">
    <!-- 导航栏begin -->
	<?php echo $_smarty_tpl->getSubTemplate ("lbi/top_nav.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    <!-- 导航栏end -->
	
	<!-- 标题栏begin -->
  	<div class="header-bar  container">
    <div class="row">
  	
   	  <ul class="filter container">
       	  <li class="col-xs-3 <?php if ($_smarty_tpl->tpl_vars['filter']->value=='is_new'){?>cur<?php }?>"><a href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
api/kanjia/index.php?UsersID=<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
&is_new=1">最新</a></li>
            <li class="col-xs-1 grey">|</li>
          <li class="col-xs-3 <?php if ($_smarty_tpl->tpl_vars['filter']->value=='is_hot'){?>cur<?php }?>"><a href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
api/kanjia/index.php?UsersID=<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
&is_hot=1">最热</a></li>
            <li class="col-xs-1 grey">|</li>
        <li class="col-xs-3 <?php if ($_smarty_tpl->tpl_vars['filter']->value=='is_recommend'){?>cur<?php }?>"><a href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
api/kanjia/index.php?UsersID=<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
&is_recommend=1">推荐</a></li>
 		<li class="clearfix"></li>         
        </ul>
    </div>
  </div>
  	<!-- 标题栏end -->

	<!-- 主体内容begin -->
    <div class="product_list">
    	<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['kanjia_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
        
        <?php $_smarty_tpl->tpl_vars["kanjia_id"] = new Smarty_variable($_smarty_tpl->tpl_vars['item']->value['Kanjia_ID'], null, 0);?>
        	
      <div class="item well">
        	<div class="row">
            <div class="image col-xs-3">
            	<a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['activity_url'];?>
"><img width="102px" height="102px" src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
<?php echo $_smarty_tpl->tpl_vars['item']->value['product']['thumb'];?>
"/></a>
            </div>
            <div class="item_desc col-xs-4">
           	  <h5><a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['activity_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['product']['Products_Name'];?>
</a></h5>
               
            	<p>原价:&nbsp;&yen;<?php echo $_smarty_tpl->tpl_vars['item']->value['product']['Products_PriceX'];?>
</p>
            	<p>底价:&nbsp;&yen;<?php echo $_smarty_tpl->tpl_vars['item']->value['Bottom_Price'];?>
</p>
                <?php if (isset($_smarty_tpl->tpl_vars['member_kanjia_list']->value[$_smarty_tpl->tpl_vars['kanjia_id']->value])){?>
 					<p>当前价:<span  class="red cur-price">&yen;<?php echo $_smarty_tpl->tpl_vars['member_kanjia_list']->value[$_smarty_tpl->tpl_vars['kanjia_id']->value]['Cur_Price'];?>
</span></p>
            	<?php }else{ ?>
           	  <p>当前价:<span  class="red cur-price">&yen;<?php echo $_smarty_tpl->tpl_vars['item']->value['product']['Products_PriceX'];?>
</span></p>
                <?php }?>
            </div>
           
            <div class="col-xs-2 kan-button">
               	<?php if ($_smarty_tpl->tpl_vars['item']->value['expired']==0){?> 
            	<a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['activity_url'];?>
"><img width="80px" height="38px" src="<?php echo $_smarty_tpl->tpl_vars['public']->value;?>
image/kanjia_btn.png"/></a>
        
            	<?php }else{ ?>
                <a href="javascript:void(0)"><img src="<?php echo $_smarty_tpl->tpl_vars['public']->value;?>
image/jin.png"/></a>
               
                <?php }?>
            </div>	
            
         
            </div>
            
            <div class="row">
             <hr/>
           	 <?php if ($_smarty_tpl->tpl_vars['item']->value['expired']==0){?> 
              <ul class="brief_info">
                <?php if (isset($_smarty_tpl->tpl_vars['member_kanjia_list']->value[$_smarty_tpl->tpl_vars['kanjia_id']->value])){?>
            	<li class="col-xs-6"><i class="icon-user shallow_grey"></i>&nbsp;已有<?php echo $_smarty_tpl->tpl_vars['member_kanjia_list']->value[$_smarty_tpl->tpl_vars['kanjia_id']->value]['Helper_Count'];?>
人为你砍价</li>
                <?php }else{ ?>
                <li class="col-xs-6"><i class="icon-user shallow_grey"></i>&nbsp;已有0人为你砍价</li>
                <?php }?>
                <li class="col-xs-1 grey">|</li>
            	<li class="col-xs-4"><i class="icon-home shallow_grey"></i>&nbsp;库存:<?php echo $_smarty_tpl->tpl_vars['item']->value['product']['Products_Count'];?>
件</li>
                <li class="clearfix"></li>
           	  </ul>
             <?php }else{ ?>
           	  <ul class="brief_info"><li class="col-xs-6 red">此活动已过期</li><li class="clearfix"></li></ul>
             <?php }?>
        </div>
         
      </div>
     	<?php } ?>
    </div>	
    
	<!-- 主体内容end -->
  </div>
    <!-- 固定footer begin -->
      <div style="margin-bottom:-40px;">
      </div>
    <!-- 固定footer end -->
	
    <!-- Include all compiled plugins (below), or include individual files as needed -->
   
    <script src="<?php echo $_smarty_tpl->tpl_vars['public']->value;?>
js/kanjia.js"></script> 
    <script type="text/javascript">
	 	  var UsersID = "<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
";
          var base_url = "<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
";
	 	  kanjia_obj.general_init();
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
		   title:"<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
",
		   desc:"<?php echo $_smarty_tpl->tpl_vars['desc']->value;?>
",
		   img_url:"<?php echo $_smarty_tpl->tpl_vars['img_url']->value;?>
",
		   link:""
		};
		$(document).ready(global_obj.share_init_config);
	</script>
	<?php }?>
    <?php echo ad($_smarty_tpl->tpl_vars['UsersID']->value,2,2);?>

  </body>
</html><?php }} ?>