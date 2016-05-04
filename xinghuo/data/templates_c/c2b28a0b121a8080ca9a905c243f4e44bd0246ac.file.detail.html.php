<?php /* Smarty version Smarty-3.1.13, created on 2016-03-15 09:24:55
         compiled from "E:\wwwroot\spark\public_html\api\kanjia\skin\1\detail.html" */ ?>
<?php /*%%SmartyHeaderCode:343956e76467414307-06621395%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c2b28a0b121a8080ca9a905c243f4e44bd0246ac' => 
    array (
      0 => 'E:\\wwwroot\\spark\\public_html\\api\\kanjia\\skin\\1\\detail.html',
      1 => 1427100806,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '343956e76467414307-06621395',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'commit_list' => 0,
    'commit' => 0,
    'public' => 0,
    'base_url' => 0,
    'share_flag' => 0,
    'appId' => 0,
    'timestamp' => 0,
    'noncestr' => 0,
    'url' => 0,
    'signature' => 0,
    'desc' => 0,
    'img_url' => 0,
    'UsersID' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_56e76467821cb5_03034776',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56e76467821cb5_03034776')) {function content_56e76467821cb5_03034776($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("lbi/header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</head>
<body>

<!-- 导航栏begin --> 
<?php echo $_smarty_tpl->getSubTemplate ("lbi/top_nav.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
 
<!-- 导航栏end --> 

<!-- 标题栏begin --> 
<?php echo $_smarty_tpl->getSubTemplate ("lbi/title_bar.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
 
<!-- 标题栏end --> 

<!-- 主题内容begin -->
<div class="content container">
  <div class="row">
    <div class="product-detail container">
      
      
      <div id="detail">
    <div class="productshow_title"><?php echo $_smarty_tpl->tpl_vars['product']->value['Products_Name'];?>
</div>
	
     <!--商品详情和评论tab 开始-->
 	 <div class="desc">
	<h5>
	<span id="tag0" class="cur">商品详情</span><span class="" id="tag1" style="border-left: 1px solid #e7e7e7;">评论(<?php echo count($_smarty_tpl->tpl_vars['commit_list']->value);?>
)</span>
	</h5>
    
	<div style="display: block;" id="description">
		<div class="contents">
			<?php echo $_smarty_tpl->tpl_vars['product']->value['Products_Description'];?>

        </div>
	</div>
	<div id="commit" style="display: none;">
		<div class="commit_list">
			<div id="commit" style="">
					<div class="commit_list">
                      <?php if (count($_smarty_tpl->tpl_vars['commit_list']->value)>0){?>                 
                       		<table width="100%" cellpadding="0" cellspacing="0">
							<tbody>
                            <?php  $_smarty_tpl->tpl_vars['commit'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['commit']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['commit_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['commit']->key => $_smarty_tpl->tpl_vars['commit']->value){
$_smarty_tpl->tpl_vars['commit']->_loop = true;
?>
                            <tr>
								<td class="commit_time"><?php echo date('Y-m-d H:i:s',$_smarty_tpl->tpl_vars['commit']->value['CreateTime']);?>
</td>
							</tr>
							<tr>
								<td class="commit_note"><?php echo $_smarty_tpl->tpl_vars['commit']->value['Note'];?>
</td>
							</tr>
							<tr>
								<td class="commit_score"><?php echo $_smarty_tpl->tpl_vars['commit']->value['Score'];?>
分</td>
							</tr>
                            <?php } ?>
						</tbody></table>                
                      
                      <?php }else{ ?>
                      	<p>暂无评论</p>
                      <?php }?>								
					</div>
				</div>
        </div>
	</div>
	<div class="clearfix">
	</div>
</div>
     <!--商品详情和评论tab 结束-->
	</div>
    
    </div>
  </div>
</div>

<!-- 主题内容end --> 
<!-- 固定footer begin --> 

<!-- 固定footer end --> 

<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="<?php echo $_smarty_tpl->tpl_vars['public']->value;?>
js/kanjia.js"></script> 
<script type="text/javascript">
	var base_url = "<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
";
    kanjia_obj.product_detail_init();
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