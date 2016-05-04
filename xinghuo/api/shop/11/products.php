<?php require_once('top.php'); ?>
<body >
<script type='text/javascript' src='/static/js/iscroll.js'></script> 
<script type='text/javascript' src='/static/api/shop/js/product_attr_helper.js'></script>
<link href="/static/css/bootstrap.css" rel="stylesheet" />
<link rel="stylesheet" href="/static/css/font-awesome.css" />
<link href="/static/api/shop/skin/default/css/tao_detail.css?t=<?php echo time();?>" rel="stylesheet" type="text/css" />
<style>


</style>
<?php
	//ad($UsersID, 1, 1);
?>
<script src="/static/js/jquery.idTabs.min.js"></script> 
<!-- 产品图片所需插件 begin -->
<link href="/static/js/plugin/photoswipe/photoswipe.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/static/js/plugin/touchslider/touchslider.min.js"></script> 
<script type='text/javascript' src='/static/js/plugin/photoswipe/klass.min.js'></script> 
<script type='text/javascript' src='/static/js/plugin/photoswipe/photoswipe.jquery-3.0.5.min.js'></script> 
<script type='text/javascript' src='/static/api/shop/js/toast.js'></script> 
<script type='text/javascript' src='/static/js/plugin/cookies/jquery.cookie.js'></script> 
<!-- 产品图片所需插件 end --> 
<script language="javascript">
	var base_url = '<?=$base_url?>';
	var UsersID = '<?=$UsersID?>';
    var Products_ID = '<?=$rsProducts['Products_ID']?>';
	var proimg_count = 1;
	var is_virtual = <?php echo $rsProducts["Products_IsVirtual"]?>;
	$(document).ready(function() {
		shop_obj.tao_detail_init();
		$("#content-filter").idTabs();
	});
</script>
<header class="bar bar-nav"> <a href="javascript:history.go(-1)" class="pull-left"><img src="/static/api/shop/skin/default/images/black_arrow_left.png" /></a>
    <h1 class="title" id="page_title">产品详情</h1>
</header>
<div id="overlay"></div>
<div id="wrap"> 
  <!-- 产品图片开始 -->
  <div id="detail_images">
    <div class="pro_img">
      <div class="touchslider">
        <div class="img">
          <div class="touchslider-viewport">
            <div class="list">
              <?php if(isset($JSON["ImgPath"])){
									foreach($JSON["ImgPath"] as $key=>$value){
										echo '<div class="touchslider-item"><a href="'.$value.'" rel="'.$value.'"><img data-url="'.$value.'" src="/static/js/plugin/lazyload/grey.gif" style="max-height:350px;" /></a></div>';
									}
								}?>
            </div>
          </div>
        </div>
        <div class="touchslider-nav">
          <?php
							if(isset($JSON["ImgPath"])){
							foreach($JSON["ImgPath"] as $key=>$value){
								echo $key==0 ? '<a class="touchslider-nav-item touchslider-nav-item-current"></a>' : '<a class="touchslider-nav-item"></a>';
							}
						}?>
        </div>
      </div>
    </div>
  </div>
  <!-- 产品图片结束 -->
  <div class="conbox">
    <div id="product_brief_info">
      <div id="name_and_share"> <span id="product_name">
        <?=$rsProducts["Products_Name"]?>
        </span> <span id="shu_xian"></span> <a class="detail_share" id="share_product" productid="<?=$ProductsID?>" is_distribute="<?=$Is_Distribute?>">分销此商品</a>
        <div class="clearfix"></div>
        <span id="product_price">&yen;
        <?=$rsProducts["Products_PriceX"]?>
        元</span>&nbsp;&nbsp;&nbsp;&nbsp; <del id="product_origin_price">&yen;
        <?=$rsProducts["Products_PriceY"]?>
        元</del> </div>
      <p id="other_info">
        <?php if($rsProducts["Products_IsShippingFree"] == 1):?>
        <span>快递免运费</span>
        <?php endif;?>
        <span>销量
        <?=$rsProducts["Products_Sales"]?>
        件</span> <span>
        <?=$comment_aggregate['points']?>
        分</span> </p>
    </div>
  </div>
</div>
<div class="conbox">
  <div id="activity_info">
    <?php if($un_accept_coupon_num  >0):?>
    <p><a href="/api/<?=$UsersID?>/user/coupon/1/"><img src="/static/api/shop/skin/default/quan_icon.jpg" />&nbsp;点击领取优惠券</a></p>
    <?php endif; ?>
    <?php if(!empty($man_list)):?>
    <p> <img src="/static/api/shop/skin/default/gift.jpg" />
      <?php foreach($man_list as $key=>$item):?>
      满<span class="juice">
      <?=$item['reach'];?>
      </span>减<span class="juice">
      <?=$item['award'];?>
      </span>
      <?php endforeach;?>
    </p>
    <?php endif; ?>
  </div>
</div>

<!-- 会员专享价begin -->
<?php if(count($discount_list) > 0):?>
<div class="b5"></div>
<div class="detail_panel_title">
    <div class="p_info"><a href="javascript:void(0)">会员专享价</a></div>
</div>
<div class="b5"></div>
<div class="detail_panel_content">
  <ul class="list-group" id="user_level_price">
    <?php foreach($discount_list as $key=>$item):?>
    <?php if($item['cur'] == 1):?>
    <li class="list-group-item red">&nbsp;&nbsp;
      <?=$item['Name']?>
      价&nbsp;&nbsp;<strong>&yen;
      <?=$item['price']?>
      </strong></li>
    <?php else: ?>
    <li class="list-group-item">&nbsp;&nbsp;
      <?=$item['Name']?>
      价&nbsp;&nbsp;<strong class="red">&yen;
      <?=$item['price']?>
      </strong></li>
    <?php endif;?>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>
<!-- 会员专享价end -->

<div id="detail">
  <div class="b5"></div>
  <div class="commit"><a href="<?=$base_url?>api/<?=$UsersID?>/shop/commit/<?=$ProductsID?>/"><span>&nbsp;</span>商品评价
    <label class="juice">(共
      <?=$comment_aggregate['num']?>
      条)</label>
    <label class="pull-right juice">
      <?=$comment_aggregate['points']?>
      分</label>
    </a></div>
  <div class="b5"></div>
  <div class="company">
    <div class="company_info"> <img src="<?php echo $rsConfig["ShopLogo"];?>" /> <span><?php echo $rsConfig["ShopName"];?></span>
      <div class="clear"></div>
    </div>
    <div class="company_btns"> <a href="<?php echo $shop_url; ?>allcategory/"><span><img src="/static/api/shop/skin/default/images/category_btns.png" width="16" /> 全部分类</span></a> <a href="<?php echo $shop_url; ?>"><span><img src="/static/api/shop/skin/default/images/home_btns.png" width="16" /> 店铺逛逛</span></a>
      <div class="clear"></div>
    </div>
  </div>
</div>
<div class="b5"></div>
<!-- 产品详情start -->
<div id="content-filter" class="center-block">
  <div style="width:100%;height:40px;overflow:hidden;border-top:solid 1px #dcdcdc;border-bottom:solid 1px #eeeeee">
	<a href="#description" class="item selected">产品详情</a><span class="shu">|</span><a href="#panel2" class="item">产品参数</a>
  </div>
  <div id="table-panel">
    <div id="description">
      <div class="contents">
        <?=$rsProducts["Products_Description"]?>
      </div>
    </div>
  </div>
  <div id="panel2"> <br/>
    <table class="Ptable" border="1" cellpadding="0" cellspacing="1" width="100%">
      <tbody>
        <?php foreach($properties['pro'] as $attr_group=>$attr_list):?>
        <tr>
          <th class="tdTitle" colspan="2"><?=$attr_group?></th>
        </tr>
        <tr></tr>
        <?php foreach($attr_list as $k=>$attr): ?>
        <tr>
          <td class="tdTitle"><?=$attr['Name']?></td>
          <td><?=$attr['Value']?></td>
        </tr>
        <?php endforeach; ?>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
  <a href="#" class="clearfix"></a> 
</div>
 <!-- 产品详情end -->
<div class="b5"></div>
<div class="visit_more">
	<h1><a href="#"><span>&nbsp;</span>浏览此商品的用户还看了</a></h1>
	<ul>
		<?php foreach($relatedProducts  as $key=>$item):?>
		<li> 
			<a href="<?=$shop_url?>products/<?=$item['Products_ID']?>/">
				<p class="img"><img src="<?=$base_url?><?=$item['ImgPath']?>" /></p>
				<p class="name">
					<?=$item['Products_Name']?>
				</p>
				<p class="price">￥
					<?=$item['Products_PriceX']?>
				</p>
			</a> 
		</li>
		<?php endforeach;?>
		<div class="clear"></div>
	</ul>
</div>
<!-- 属性选择内容begin -->
<div id="option_content">
    <div id="option_selecter">
	    <div id="title">选择商品属性<div id="close-btn"></div></div>
        <form name="addtocart_form" action="/api/<?php echo $UsersID ?>/shop/cart/" method="post" id="addtocart_form">
            <input type="hidden" name="OwnerID" value="<?=$owner['id']?>"/>
		    <?php if( $rsProducts["Products_IsShippingFree"] == 1):?>
		    <input type="hidden" name="IsShippingFree" value="1"/>
		    <?php else:?>
		    <input type="hidden" name="IsShippingFree" value="0"/>
		    <?php endif;?>
            <input type="hidden" name="ProductsWeight" value="<?=$rsProducts["Products_Weight"];?>"/>
			<div id="simple-info">
				<div id="product-thumb"> <img width="80px" height="80px" src="<?=$rsProducts['ImgPath']?>" /> </div>
				<div id="info-txt">
				    <p><span id="stock_val">
						<?=$rsProducts['Products_Name']?>
						</span>
					</p>
				    <p class="orange">&yen;<span id="cur-price-txt">
							<?=$cur_price?>
						</span>
				    </p>
				    
				</div>
				<div class="clearfix"></div>
			</div>
            <div class="option-val-list">
				<!-- choose begin -->
				<ul id="choose" class="list-group">
					<!-- {* 开始循环所有可选属性 *} --> 
					<!-- {foreach from=$specification item=spec key=spec_key} -->
					<?php $spec_list = array(); ?>
					<?php foreach($specification as $spec_key=>$spec):?>
					<li  id="choose-version" class="list-group-item">
						<div class="dt">
						  <?=$spec['Name']?>
						  ：</div>
						<div class="dd catt"> 
						  <!-- {* 判断属性是复选还是单选 *} -->
						  
						  <?php if($spec['Attr_Type'] == 1):?>
						  <?php foreach($spec['Values'] as $key=>$value):?>
						  <a  class="<?=($key == 0)?'cattsel':'';?>"  onclick="changeAtt(this)" href="javascript:;" name="<?=$value['id']?>" title="<?=$value['label']?>">
						  <?php if($key == 0){
										$spec_list[] = $value['id'];
										
									};
									?>
						  <?=$value['label']?>
						  <input style="display:none" id="spec_value_<?=$value['id']?>" type="radio" name="spec_<?=$spec_key?>" value="<?=$value['id']?>" <?=($key == 0)?'checked':'';?> />
						  </a>
						  <?php endforeach; ?>
						  <?php else: ?>
						  <?php foreach($spec['Values'] as $key=>$value):?>
						  <label for="spec_value_<?=$value['id']?>">
							<?=$value['label']?>
							<input type="checkbox" name="spec_<?=$spec_key?>[]" value="<?=$value['id']?>" id="spec_value_<?=$value['id']?>" onClick="changePrice()" />
						  </label>
						  
						  <!-- {/foreach} -->
						  <?php endforeach; ?>
						  <div class="clearfix"></div>
						  <?php endif; ?>
						</div>
						<div class="clearfix"></div>
					</li>
					<?php endforeach; ?>
					<input type="hidden" id="spec_list" name="spec_list" value="<?=implode(',',$spec_list)?>" />
					<!-- {* 结束循环可选属性 *} --> 
				</ul>
				<!--choose end-->
				<div id="qty_selector">
					<input type="hidden" id="cur_price" value="<?=$cur_price?>"/>
					<input type="hidden" id="no_attr_price" value="<?=$no_attr_price?>"/>
					数量: <a href="javascript:void(0)" class="qty_btn" name="minus">-</a>
					<input type="text" name="Qty" maxlength="3" style="height:28px; line-height:28px" value="1" pattern="[0-9]*" />
					<a href="javascript:void(0)" class="qty_btn" name="add">+</a> 
					<span>库存<span id="stock_val">
						<?=$rsProducts['Products_Count']?>
						</span>件
					</span>
				</div>
            </div> 
		    <input type="hidden" name="ProductsID" value="<?php echo $ProductsID ?>" />
		    <input type="hidden" id="needcart" name="needcart" value="1" />
        </form>
        <a href="#" id="selector_confirm_btn">确定</a>
	</div>
</div>
<!-- 属性选择内容end --> 

<!-- 页脚panle begin -->
<div id="footer_panel_content">
	<ul id="footer-panel">
		<li class="icon-container">
			<span>
				<?php
					$id = $rsProducts['Products_IsFavourite']?'favorited':'favorite';
				?>
				<b id="<?=$id?>"  isFavourite="<?=$rsProducts['Products_IsFavourite']?>" productid="<?=$rsProducts['Products_ID']?>">收藏</b> 
			</span>
			<span><b class="cart" onClick="javascript:location.href='/api/<?=$UsersID?>/shop/cart/';">购物车</b>
			
			<?php 
			    $car_num = '';
			    if(!empty($_COOKIE[$UsersID.'car_num']) && !empty($_SESSION[$UsersID.'CartList'])){
			       $car_num = $_COOKIE[$UsersID.'car_num'];
				}
			?>
			<i <?php if(empty($car_num)){?>style="display:none"<?php }?>><?php echo $car_num;?></i>
			</span>
		</li>
		<?php if($rsProducts['Products_IsVirtual']==0){?>
		<li class="button-conteiner"><a href="javascript:void(0)" id="menu-direct-btn">立即购买</a></li>
		<li class="button-conteiner"><a href="javascript:void(0)" id="menu-addtocard-btn">加入购物车</a></li>
		<?php }else{?>
		<li class="button-conteiner-virtual"><a href="javascript:void(0)" id="menu-direct-btn">立即购买</a></li>
		<?php }?>
		<div class="clearfix"></div>
	</ul>
</div>
<!-- 页脚panle end --> 
<footer id="footer"></footer>
<div id="back-to-top"></div>
<script language='javascript'>
    var KfIco = '/static/kf/ico/00.png';
    var OpenId = '';
    var UsersID = '<?=$UsersID?>';
</script>
<?php if(!empty($kfConfig)){?>
<script language='javascript'>var KfIco='<?php echo $KfIco;?>'; var OpenId='<?php echo empty($_SESSION[$UsersID."OpenID"]) ? '' : $_SESSION[$UsersID."OpenID"];?>'; var UsersID='<?php echo $UsersID;?>'; </script> 
<script type='text/javascript' src='/kf/js/webchat.js?t=<?php echo time();?>'></script>
<?php }?>
<?php if($rsConfig["CallEnable"] && $rsConfig["CallPhoneNumber"]){?>
<script language='javascript'>var shop_tel='<?php echo $rsConfig["CallPhoneNumber"];?>';</script> 
<script type='text/javascript' src='/static/api/shop/js/tel.js?t=<?php echo time();?>'></script>
<?php }?>
<?php if($share_flag==1 && $signature<>""){?>
<script language="javascript">
		var share_config = {
		   appId:"<?php echo $share_user["Users_WechatAppId"];?>",		   
		   timestamp:<?php echo $timestamp;?>,
		   nonceStr:"<?php echo $noncestr?>",
		   url:"<?php echo $url?>",
		   signature:"<?php echo $signature;?>",
		   title:"<?php echo empty($share_title) ? $rsConfig["ShopName"] : $share_title;?>",
		   desc:"<?php echo empty($share_desc) ? $rsConfig["ShopName"] : str_replace(array("\r\n", "\r", "\n"), "", $share_desc);?>",
		   img_url:"<?php echo empty($share_img) ? $rsConfig["ShopLogo"] : $share_img;?>",
		   link:"<?php echo empty($share_id) ? (empty($share_link) ? '' : $share_link) : 'http://'.$_SERVER['HTTP_HOST'].'/api/'.$UsersID.'/'.(empty($owner["id"]) ? '' : $owner["id"].'/').'share_recieve/'.$share_id.'/'; ?>"
		};
		
		$(document).ready(global_obj.share_init_config);
	</script>
<?php }?>
	<div class='conver_favourite'><img src="/static/api/images/global/share/favourite.png" /></div>
	<span style="display:none" id="buy-type"></span>
	<?php require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/substribe.php');?>
	<script type='text/javascript' src='/static/js/plugin/lazyload/jquery.scrollLoading.js'></script> 
	<script>
	    $(document).ready(function(){
			$("img").scrollLoading();
		});
	</script>
	<script>  
	    $(function(){  
			//当滚动条的位置处于距顶部100像素以下时，跳转链接出现，否则消失  
			$(function () {  
				$(window).scroll(function(){  
					if ($(window).scrollTop()>100){  
						$("#back-to-top").fadeIn(1500);  
					}  
					else  
					{  
						$("#back-to-top").fadeOut(1500);  
					}  
				});  
	  
				//当点击跳转链接后，回到页面顶部位置  
	  
				$("#back-to-top").click(function(){  
					$('body,html').animate({scrollTop:0},1000);  
					return false;  
				});  
			});  
		});  
	</script>  
</body>
</html>