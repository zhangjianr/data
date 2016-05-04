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
      <form class="search" id="search_form" method="get" action="">
        关键词：
        <input type="text" name="Keyword" value="" class="form_input" size="15" />
        <input type="submit" class="search_btn" value="搜索" />
        <input type="hidden" name="m" value="shop" />
        <input type="hidden" name="a" value="review" />
      </form>
      <table border="0" cellpadding="5" cellspacing="0" class="r_con_table" id="review_list">
        <thead>
          <tr>
            <td width="8%" nowrap="nowrap">序号</td>
            <td width="20%" nowrap="nowrap">评论产品</td>
            <td width="10%" nowrap="nowrap">用户名称</td>
            <td width="25%" nowrap="nowrap">评论内容</td>
            <td width="12%" nowrap="nowrap">时间</td>
            <td width="8%" nowrap="nowrap" class="last">操作</td>
          </tr>
        </thead>
        <tbody>
          <tr RId="124" class="is_not_read">
            <td nowrap="nowrap">1</td>
            <td>雪纺连衣裙</td>
            <td nowrap="nowrap">测试</td>
            <td>森森岁</td>
            <td nowrap="nowrap">2014-03-20 21:29:41</td>
            <td nowrap="nowrap" class="last"><a href="./?m=shop&a=review&d=view&RId=124&page=1"><img src="/static/member/images/ico/view.gif" align="absmiddle" /></a> <a href="./?m=shop&a=review&do_action=shop.review_del&RId=124&page=1" title="删除" onClick="if(!confirm('删除后不可恢复，继续吗？')){return false};"><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a></td>
          </tr>
        </tbody>
      </table>
      <div class="blank20"></div>
      <div id="turn_page"><font class='page_noclick'><<上一页</font>&nbsp;<font class='page_item_current'>1</font>&nbsp;<font class='page_noclick'>下一页>></font></div>
    </div>
  </div>
</div>
</body>
</html>