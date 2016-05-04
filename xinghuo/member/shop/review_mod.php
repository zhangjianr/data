<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>微易宝</title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/member/js/global.js'></script>
</head>

<body>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->

<div id="iframe_page">
  <div class="iframe_content">
    <link href='/static/member/css/shop.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/member/js/shop.js'></script>
    <div class="r_nav">
      <ul>
        <li class=""><a href="config.php">基本设置</a></li>
        <li class=""><a href="skin.php">风格设置</a></li>
        <li class=""><a href="home.php">首页设置</a></li>
        <li class=""><a href="products.php">产品管理</a></li>
        <li class=""><a href="orders.php">订单管理</a></li>
        <li class="cur"><a href="review.php">评论管理</a></li>
      </ul>
    </div>
    <div id="reviews" class="r_con_wrap">
      <form id="review_mod_form" method="post" action="./?m=shop&a=review" class="r_con_form">
        <div class="rows">
          <label>评论产品</label>
          <span class="input"><span class="tips">雪纺连衣裙</span></span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label>用户名称</label>
          <span class="input"><span class="tips">测试</span></span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label>评论内容</label>
          <span class="input"><span class="tips">森森岁</span></span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label>评论时间</label>
          <span class="input"><span class="tips">2014-03-20 21:29:41</span></span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label>回复内容</label>
          <span class="input">
          <textarea name="ReviewReply" class="briefdesc">dsfsd</textarea>
          </span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label></label>
          <span class="input">
          <input type="submit" class="btn_green" name="submit_button" value="提交保存" />
          <a href="" class="btn_gray">返 回</a></span>
          <div class="clear"></div>
        </div>
        <input type="hidden" name="RId" value="124" />
        <input type="hidden" name="page" value="1" />
        <input type="hidden" name="do_action" value="shop.review_mod">
      </form>
    </div>
  </div>
</div>
</body>
</html>