<?php
if(empty($_SESSION["Users_Account"])){
	header("location:/member/login.php");
}

$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Orders_Status<4";
if(isset($_GET["search"])){
	if($_GET["search"]==1){
		if(!empty($_GET["Keyword"])){
			$condition .= " and `".$_GET["Fields"]."` like '%".$_GET["Keyword"]."%'";
		}
		if(isset($_GET["Status"])){
			if($_GET["Status"]<>''){
				$condition .= " and Orders_Status=".$_GET["Status"];
			}
		}
		if(!empty($_GET["AccTime_S"])){
			$condition .= " and Orders_CreateTime>=".strtotime($_GET["AccTime_S"]);
		}
		if(!empty($_GET["AccTime_E"])){
			$condition .= " and Orders_CreateTime<=".strtotime($_GET["AccTime_E"]);
		}
	}
}

$condition .= " order by Orders_CreateTime desc";

$_STATUS_SHIPPING = array('<font style="color:#FF0000">待付款</font>','<font style="color:#03A84E">待发货</font>','<font style="color:#F60">待收货</font>','<font style="color:blue">已领取</font>','<font style="color:#999; text-decoration:line-through">&nbsp;已取消&nbsp;</font>');
$_STATUS = array('','<font style="color:#FF0000">未领取</font>','','<font style="color:blue">已领取</font>');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/member/js/global.js'></script>
<link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script> 
<link href='/static/js/plugin/lean-modal/style.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/plugin/lean-modal/lean-modal.min.js'></script>
</head>

<body>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->
<div id="iframe_page">
  <div class="iframe_content">
    <link href='/static/member/css/user.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/member/js/user.js'></script>
    <div class="r_nav">
      <ul>
        <li class=""><a href="config.php">基本设置</a>
          <dl>
            <dd class="first"><a href="lbs.php">一键导航设置</a></dd>
          </dl>
        </li>
        <li class=""> <a href="user_list.php">会员管理</a>
          <dl>
            <dd class="first"><a href="user_level.php">会员等级设置</a></dd>
            <dd class=""><a href="user_profile.php">会员注册资料</a></dd>
            <dd class=""><a href="card_benefits.php">会员权利说明</a></dd>
            <dd class=""><a href="user_list.php">会员管理</a></dd>
          </dl>
        </li>
        <li class=""> <a href="card_config.php">会员卡设置</a></li>
        <li class=""> <a href="coupon_config.php">优惠券</a>
          <dl>
            <dd class="first"><a href="coupon_config.php">优惠券设置</a></dd>
            <dd class=""><a href="coupon_list.php">优惠券管理</a></dd>
            <dd class=""><a href="coupon_list_logs.php">优惠券使用记录</a></dd>
          </dl>
        </li>
        <li class="cur"> <a href="gift_orders.php">礼品兑换</a>
          <dl>
            <dd class="first"><a href="gift.php">礼品管理</a></dd>
            <dd class=""><a href="gift_orders.php">兑换订单管理</a></dd>
          </dl>
        </li>
        <li class=""><a href="business_password.php">商家密码设置</a></li>
        <li class=""><a href="message.php">消息发布管理</a></li>
      </ul>
    </div>
    <div id="gift_orders" class="r_con_wrap"> 
      <script language="javascript">$(document).ready(user_obj.gift_orders_init);</script>
      <form class="search" id="search_form" method="get" action="">
      	<select name="Fields">
			<option value='Address_Name'>购买人</option>
			<option value='Address_Mobile'>购买手机</option>
			<option value='Address_Detailed'>收货地址</option>
		</select>
        <input type="text" name="Keyword" value="" class="form_input" size="15" />
        订单状态：
        <select name="Status">
          <option value="">--请选择--</option>
          <option value='0'>已兑换</option>
          <option value='1'>待发货</option>
          <option value='2'>待收货</option>
          <option value='3'>已领取</option>
        </select>
        兑换时间：
        <input type="text" class="input" name="AccTime_S" value="" maxlength="20" />
        -
        <input type="text" class="input" name="AccTime_E" value="" maxlength="20" />
        <input type="hidden" value="1" name="search" />
        <input type="submit" class="search_btn" value="搜索" />
        <input type="button" class="virtual_btn" value="电子券验证" style="background:#1584D5; color:white; border:none; height:22px; cursor:pointer; line-height:22px; border-radius:5px; width:120px;" />
		<input type="button" class="recieve_btn" value="批量收货" style="background:#1584D5; color:white; border:none; height:22px; cursor:pointer; line-height:22px; border-radius:5px; width:120px;" />
      </form>
      <table width="100%" align="center" border="0" cellpadding="5" cellspacing="0" class="r_con_table">
        <thead>
          <tr>
            <td width="8%" nowrap="nowrap">序号</td>
            <td width="20%" nowrap="nowrap">名称</td>
            <td width="20%" nowrap="nowrap">图片</td>
            <td width="10%" nowrap="nowrap">姓名</td>
            <td width="10%" nowrap="nowrap">手机</td>
            <td width="10%" nowrap="nowrap">生成时间</td>
            <td width="10%" nowrap="nowrap">状态</td>
            <td width="10%" nowrap="nowrap" class="last">操作</td>
          </tr>
        </thead>
        <tbody>
        <?php $DB->getPage("user_gift_orders","*",$condition,$pageSize=10);
		$i=1;
		$lists = array();
		while($rsGift=$DB->fetch_assoc()){
			$lists[] = $rsGift;
		}
		foreach($lists as $k=>$v){
			$gift = $DB->GetRs("user_gift","*","where Gift_ID=".$v['Gift_ID']."");
			$v['Gift_Name'] = $gift['Gift_Name'];
			$v['Gift_ImgPath'] = $gift['Gift_ImgPath'];
			?>
          <tr>
            <td nowrap="nowrap"><?php echo $pageSize*($DB->pageNo-1)+$i; ?></td>
            <td><?php echo $v['Gift_Name'];?></td>
            <td nowrap="nowrap"><img src="<?php echo $v['Gift_ImgPath']?>" class="img" /></a></td>
            <td nowrap="nowrap"><?php echo $v['Address_Name'];?></td>
            <td nowrap="nowrap"><?php echo $v['Address_Mobile'];?></td>
            <td nowrap="nowrap"><?php echo date("Y-m-d H:i:s",$v['Orders_CreateTime']);?></td>
            <td nowrap="nowrap">
            	<?php
                if($v["Orders_IsShipping"]==0){
					 echo $_STATUS[$v["Orders_Status"]];
				}else{
					echo $_STATUS_SHIPPING[$v["Orders_Status"]];
				}
				?>
            </td>
            <td class="last" nowrap="nowrap">
            	<a href="gift_orders_view.php?OrderId=<?php echo $v['Orders_ID']?>&page=<?php echo $DB->pageNo;?>">[详情]</a>
                <?php
                if($v["Orders_IsShipping"]==1){
					if($v["Orders_Status"]==1){
						echo '<a href="gift_orders_send.php?OrderId='.$v['Orders_ID'].'">[发货]</a>';
					}else{
						if($v["Orders_Status"]==0 && $v["Orders_PaymentMethod"] == '线下支付'){
							echo '<a href="gift_orders_send.php?OrderId='.$v['Orders_ID'].'">[发货]</a><br />线下支付';
						}
					}
				}
				?>
            </td>
          </tr>
          
          <?php $i++;
		  }?>
        </tbody>
      </table>
      <div class="blank20"></div>
      <?php $DB->showPage(); ?>
    </div>
    
    <div id="virtual_div" class="lean-modal lean-modal-form">
      <div class="h">电子券验证<a class="modal_close" href="#"></a></div>
      <form class="form" id="virtual_form">
        <div class="rows">
          <label>电子券码：</label>
          <span class="input">
          <input name="Code" value="" id="Code" type="text" class="form_input" size="26" maxlength="100" notnull>
          <font class="fc_red">*</font> </span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label></label>
          <span class="submit">
          <input type="submit" value="确定提交" name="submit_btn">
          </span>
          <div class="clear"></div>
        </div>
      </form>
    </div>
	
	<div id="recieve_div" class="lean-modal lean-modal-form">
      <div class="h">批量收货<a class="modal_close" href="#"></a></div>
      <form class="form" id="recieve_form">
        <div class="rows">
          <label></label>
          <span class="input">
			<span class="tips">发货七天后的礼品将自动收货</span>
          </span>
          <div class="clear"></div>
        </div>
		<div class="rows">
          <label></label>
          <span class="submit">
          <input type="submit" value="确定" name="submit_btn">
          </span>
          <div class="clear"></div>
        </div>
      </form>
    </div>
    
  </div>
</div>
</body>
</html>