<?php require_once('header.php'); ?>
<link href="/static/css/bootstrap.min.css" rel="stylesheet">
<script type='text/javascript' src='/static/js/bootstrap.min.js'></script>
<link rel="stylesheet" href="/static/api/shop/skin/default/css/tao_checkout.css" />
<script>
    var ini_num = <?php echo $qty;?>;//最低批发量
	var UsersID = "<?php echo $UsersID;?>";
	var ProductID = <?php echo $rsProducts['Products_ID'];?>;
	var base_url = "<?php echo $base_url;?>";
    $(document).ready(function(){
		pifa_obj.order_init();
	});
</script>
<style>
    body{background:#f7f7f7;}
	h2{font-size:16px;margin:0;}
</style>
    <div class="top">
	    <a class="go_back" href="javascript:;" onclick="history.go(-1);"></a>
		<span>提交订单</span>
	</div>
	<form id="payment_form" action="/api/<?php echo $UsersID ?>/pifa/cart/">
	    <input type="hidden" name="OwnerID" value="<?php echo $owner['id'];?>"/>
		<input type="hidden" name="ProductID" value="<?php echo $ProductsID;?>"/>
		<?php if( $rsProducts["Products_IsShippingFree"] == 1){?>
		<input type="hidden" name="IsShippingFree" value="1"/>
		<?php }else{?>
		<input type="hidden" name="IsShippingFree" value="0"/>
		<?php }?>
		<input type="hidden" name="Order_Shipping[Express]"  id="Order_Shipping_Express" value="<?php echo $Shipping_Name;?>"/>
        <input type="hidden" id="total_shipping_fee" name="Order_Shipping[Price]" value="<?php echo $total_shipping_fee;?>"/>
		<input type="hidden" name="AddressID" value="<?php echo $rsAddress['Address_ID'];?>" />
		<input type="hidden" name="Need_Shipping" value="<?=$rsConfig['NeedShipping']?>" />
		<div class="select_address">
		<input type="hidden" name="City_Code" value="<?=$rsAddress['Address_City']?>"/>
			<h2>配送地址</h2>
			<ul class="info">
				<li><span class="span_left">收件人：<b><?=$rsAddress['Address_Name']?></b></span><span class="span_right"><?=$rsAddress['Address_Mobile']?></span></li>
				<li>地址：<?=$Province.'&nbsp;&nbsp;'.$City.'&nbsp;&nbsp;'.$Area.'&nbsp;'.$rsAddress['Address_Detailed'];?></li>
			</ul>
			<a class="select_more" href="/api/<?=$UsersID?>/user/my/address/<?=$rsAddress['Address_ID'] ?>/">更多地址>></a>
		</div>
		<div style="border-top:solid 1px #dddddd;border-bottom:solid 1px #dddddd; background:#ffffff; margin-top:10px;">
			<div class="order_box">
				<h2><?php echo $rsProducts['Products_Name'];?></h2>
				<div id="qty_selector">
					数量: <a name="minus" class="qty_btn" href="javascript:void(0)">-</a>
					<input type="text" pattern="[0-9]*" value="<?php echo $qty;?>" style="height:28px; line-height:28px" name="Qty">
					<a name="add" class="qty_btn" href="javascript:void(0)">+</a> 
					<span>库存<span id="stock_val">
							<?php echo $rsProducts["Products_Count"];?></span><?php echo $rsProducts["Products_unit"];?>
					</span>
				</div>
				<div class="heji"><div class="heji_left">单价：￥<b style="color:red"><?php echo $cur_price;?></b></div><div class="heji_right"><b>合计：<span id="total_price">￥<?php echo number_format($base_total_price,2,'.','');?></span></b></div></div>
				<?php if($rsConfig['NeedShipping'] == 1){?>
				<div id="shipping_method">
                    &nbsp;&nbsp;配送方式 
                    <a href="javascript:void(0)" class="pull-right"><img width="25px" height="25px" src="/static/api/shop/skin/default/images/arrow_right.png"/></a>
                    <a class="pull-right">&nbsp;&nbsp;
                    <span id="shipping_name"><?php echo $Shipping_Name;?></span>&nbsp;&nbsp;                  
                    <span id="total_shipping_fee_txt">
						<?php if($rsProducts['Products_IsShippingFree'] == 1){?>
							免运费
                        <?php }else{?>
						    <?php if($total_shipping_fee  == 0){?>
							免运费
							<?php }else{?>
                            <?php echo $total_shipping_fee;?>元
							<?php }?>
						<?php } ?>
                    </span>&nbsp;&nbsp;</a>
                </div>
				<?php }else{?>
				    <div style="text-align:center;line-height:35px;border-top:1px solid #dddddd">此商城无需物流</div>
				<?php }?>
			</div>
		</div>
		<ul class="payment">
			<h2>支付方式</h2>
			<?php if(!empty($rsPay["PaymentWxpayEnabled"])){ ?>
			<li data-value="微支付">微信支付</li>
			<?php }?>
			<?php if(!empty($rsPay["Payment_AlipayEnabled"])){ ?>
			<li data-value="支付宝">支付宝支付</li>
			<?php }?>
			<?php if(!empty($rsPay["Payment_OfflineEnabled"])){?>
			<li data-value="线下支付">货到付款</li>
			<?php }?>
			<li data-value="余额支付">余额支付</li>
		</ul>
	    <div style="height:60px;"></div>
		<div class="footer_pay">
			<div class="footer_left">应付金额：￥<span><?php echo number_format($total_price,2,'.','');?></span></div>
			<div class="footer_right"><a class="submit" href="javascript:;">提交订单</a></div>
		</div>
		<input type="hidden" name="PaymentMethod" value="微支付"/>
		<input type="hidden" name="DefautlPaymentMethod" value="微支付"/>
		<!--选择快递-->
		<div class="container">
			<div class="row">
				<div class="modal"  role="dialog" id="shipping-modal">
					<div class="modal-dialog modal-sm">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
								<h5 class="modal-title" id="mySmallModalLabel">配送方式</h5>
							</div>
							<div class="modal-body">
								<dl id="shipping-company-list"> 
								 <?php foreach($shipping_company_dropdown as  $key=>$item):?>
									<dd>
										<div class="pull-left shipping-company-name"><?=$item?></div>
										<div class="pull-right"><input  type="radio" shipping_name="<?=$item?>" <?=($Default_Shipping == $key)?'checked':'';?>  name="Shiping_ID" value="<?=$key?>" /></div>
										<div class="clearfix"></div>
									</dd>
								  <?php endforeach;?>
								</dl>
								<div class="clearfix"></div>
							</div>
							<div class="modal-footer">
								<a class="pull-left modal-btn" id="confirm_shipping_btn">确定</a>
								<a class="pull-right modal-btn" id="cancel_shipping_btn">取消</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
<?php require_once('footer.php'); ?>