<?php
$Dwidth = array('100','640');
$DHeight = array('40','320');
$Home_Json=json_decode($rsSkin['Home_Json'],true);
for($no=1;$no<=2;$no++){
	$json[$no-1]=array(
		"ContentsType"=>$no==2?"1":"0",
		"Title"=>$no==2?json_encode($Home_Json[$no-1]['Title']):$Home_Json[$no-1]['Title'],
		"ImgPath"=>$no==2?json_encode($Home_Json[$no-1]['ImgPath']):$Home_Json[$no-1]['ImgPath'],
		"Url"=>$no==2?json_encode($Home_Json[$no-1]['Url']):$Home_Json[$no-1]['Url'],
		"Postion"=>"t0".$no,
		"Width"=>$Dwidth[$no-1],
		"Height"=>$DHeight[$no-1],
		"NeedLink"=>"1"
	);
}

if($_POST){
	$no=intval($_POST["no"])+1;
	if(empty($_POST["ImgPath"])){
		$_POST["TitleList"]=array();
		foreach($_POST["ImgPathList"] as $key=>$value){
			$_POST["TitleList"][$key]="";
			if(empty($value)){
				unset($_POST["TitleList"][$key]);
				unset($_POST["ImgPathList"][$key]);
				unset($_POST["UrlList"][$key]);
			}
		}
	}
	$Home_Json[$no-1]=array(
		"ContentsType"=>$no==2?"1":"0",
		"Title"=>$no==2?array_merge($_POST["TitleList"]):$_POST['Title'],
		"ImgPath"=>$no==2?array_merge($_POST["ImgPathList"]):$_POST["ImgPath"],
		"Url"=>$no==2?array_merge($_POST["UrlList"]):$_POST['Url'],
		"Postion"=>"t0".$no,
		"Width"=>$Dwidth[$no-1],
		"Height"=>$DHeight[$no-1],
		"NeedLink"=>"1"
	);
	$Data=array(
		"Home_Json"=>json_encode($Home_Json,JSON_UNESCAPED_UNICODE),
	);
	$Flag=$DB->Set("shop_home",$Data,"where Users_ID='".$_SESSION["Users_ID"]."' and Skin_ID=".$rsConfig['Skin_ID']);
	if($Flag){
		$json=array(
			"Title"=>$no==2?json_encode(array_merge($_POST["TitleList"])):$_POST['Title'],
			"ImgPath"=>$no==2?json_encode(array_merge($_POST["ImgPathList"])):$_POST["ImgPath"],
			"Url"=>$no==2?json_encode(array_merge($_POST["UrlList"])):$_POST['Url'],
			"status"=>1
		);
		echo json_encode($json);
	}else{
		$json=array(
			"status"=>"0"
		);
		echo json_encode($json);
	}
	exit;
}
require_once('top.php');
?>
    <link href='/static/js/plugin/lean-modal/style.css' rel='stylesheet' type='text/css' />
    <link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/js/plugin/lean-modal/lean-modal.min.js'></script> 
    <script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script>
    <link href='/static/api/shop/skin/<?php echo $rsConfig['Skin_ID'];?>/page.css?t=<?php echo time() ?>' rel='stylesheet' type='text/css' />
    <script language="javascript">var shop_skin_data=<?php echo json_encode($json) ?>;</script>
    <script language="javascript">$(document).ready(shop_obj.home_init);</script>
    <div id="home" class="r_con_wrap">
      <div class="m_lefter">
      <script type="text/javascript">
		var skin_index_init=function(){
			$('#index-h a.category').click(function(){
				if($('#category').height()>$(window).height()){
					$('html, body, #cover_layer').css({
						height:$('#category').height(),
						width:$(window).width(),
						overflow:'hidden'
					});
				}else{
					$('#category, #cover_layer').css('height', $(window).height());
					$('html, body').css({
						height:$(window).height(),
						overflow:'hidden'
					});
				}
				
				$('#cover_layer').show();
				$('#category').animate({left:'0%'}, 500);
				$('#shop_page_contents').animate({margin:'0 -70% 0 70%'}, 500);
				window.scrollTo(0);
				
				return false;
			});
		}
	  </script>
       <div id="shop_skin_index">
         <div class="index_header">
		<div class="shop_skin_index_list logo" rel="edit-t01">
			<div class="img"></div>
		</div>
        <div class="search">
            <form action="category/0/" method="get">
                <input type="text" name="Keyword" class="input" value="" placeholder="输入商品名称..." />
                <input type="submit" class="submit" value=" " />
            </form>
        </div>
	</div>
	<div class="shop_skin_index_list banner" rel="edit-t02">
		<div class="img"></div>
    </div>
	<div id="index-h">
		<div><a href="javascript:;" class="category"><img src="/static/api/shop/skin/<?php echo $rsConfig['Skin_ID'];?>/category-1.jpg" /><br />产品分类</a></div>
		<div class="c"><a href="category/0/?IsHot=1"><img src="/static/api/shop/skin/<?php echo $rsConfig['Skin_ID'];?>/hot.jpg" /><br />热销产品</a></div>
		<div class="d"><a href="category/0/?IsNew=1"><img src="/static/api/shop/skin/<?php echo $rsConfig['Skin_ID'];?>/new.jpg" /><br />新品上市</a></div>
	</div>
	<div class="blank6"></div>
        </div>
      </div>
      <div class="m_righter">
        <form id="home_form">
			<div id="setbanner">
				<div class="item">
					<div class="rows">
						<div class="b_l">
							<strong>图片(1)</strong><span class="tips">大图建议尺寸：<label></label>px</span><a href="#shop_home_img_del" value='0'><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a><br />
							<div class="blank6"></div>
							<div><input name="FileUpload" id="HomeFileUpload_0" type="file" /></div>
						</div>
						<div class="b_r"></div>
						<input type="hidden" name="ImgPathList[]" value="" /><input type="hidden" name="TitleList[]" value="" />
					</div>
					<div class="blank9"></div>
					<div class="rows url_select">
						<div class="u_l">链接页面</div>
						<div class="u_r">
                        	<select name='UrlList[]'>
								<?php UrlList(); ?>
                            </select>
                        </div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="item">
					<div class="rows">
						<div class="b_l">
							<strong>图片(2)</strong><span class="tips">大图建议尺寸：<label></label>px</span><a href="#shop_home_img_del" value='1'><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a><br />
							<div class="blank6"></div>
							<div><input name="FileUpload" id="HomeFileUpload_1" type="file" /></div>
						</div>
						<div class="b_r"></div>
						<input type="hidden" name="ImgPathList[]" value="" /><input type="hidden" name="TitleList[]" value="" />
					</div>
					<div class="blank9"></div>
					<div class="rows url_select">
						<div class="u_l">链接页面</div>
						<div class="u_r">
                       		<select name='UrlList[]'>
								<?php UrlList(); ?>
                            </select>
                        </div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="item">
					<div class="rows">
						<div class="b_l">
							<strong>图片(3)</strong><span class="tips">大图建议尺寸：<label></label>px</span><a href="#shop_home_img_del" value='2'><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a><br />
							<div class="blank6"></div>
							<div><input name="FileUpload" id="HomeFileUpload_2" type="file" /></div>
						</div>
						<div class="b_r"></div>
						<input type="hidden" name="ImgPathList[]" value="" /><input type="hidden" name="TitleList[]" value="" />
					</div>
					<div class="blank9"></div>
					<div class="rows url_select">
						<div class="u_l">链接页面</div>
						<div class="u_r">
                        	<select name='UrlList[]'>
								<?php UrlList(); ?>
                            </select>
                        </div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="item">
					<div class="rows">
						<div class="b_l">
							<strong>图片(4)</strong><span class="tips">大图建议尺寸：<label></label>px</span><a href="#shop_home_img_del" value='3'><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a><br />
							<div class="blank6"></div>
							<div><input name="FileUpload" id="HomeFileUpload_3" type="file" /></div>
						</div>
						<div class="b_r"></div>
						<input type="hidden" name="ImgPathList[]" value="" /><input type="hidden" name="TitleList[]" value="" />
					</div>
					<div class="blank9"></div>
					<div class="rows url_select">
						<div class="u_l">链接页面</div>
						<div class="u_r">
                        	<select name='UrlList[]'>
								<?php UrlList(); ?>
                            </select>
                        </div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="item">
					<div class="rows">
						<div class="b_l">
							<strong>图片(5)</strong><span class="tips">大图建议尺寸：<label></label>px</span><a href="#shop_home_img_del" value='4'><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a><br />
							<div class="blank6"></div>
							<div><input name="FileUpload" id="HomeFileUpload_4" type="file" /></div>
						</div>
						<div class="b_r"></div>
						<input type="hidden" name="ImgPathList[]" value="" /><input type="hidden" name="TitleList[]" value="" />
					</div>
					<div class="blank9"></div>
					<div class="rows url_select">
						<div class="u_l">链接页面</div>
						<div class="u_r">
                        	<select name='UrlList[]'>
								<?php UrlList(); ?>
                            </select>
                        </div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<div id="setimages">
				<div class="item">
					<div value="title">
						<span class="fc_red">*</span> 标题<br />
						<div class="input"><input name="Title" value="" type="text" /></div>
						<div class="blank20"></div>
					</div>
					<div value="images">
						<span class="fc_red">*</span> 图片<span class="tips">大图建议尺寸：<label></label>px</span><br />
						<div class="blank6"></div>
						<div><input name="FileUpload" id="HomeFileUpload" type="file" /></div>
						<div class="blank20"></div>
					</div>
					<div class="url_select">
						<span class="fc_red">*</span> 链接页面<br />
						<div class="input">
                        	<select name='Url'>
								<?php UrlList(); ?>
                            </select>
                        </div>
					</div>
					<input type="hidden" name="ImgPath" value="" />
				</div>
			</div>
			<div class="button"><input type="submit" class="btn_green" name="submit_button" value="提交保存" /></div>
			<input type="hidden" name="PId" value="" />
			<input type="hidden" name="SId" value="" />
			<input type="hidden" name="ContentsType" value="" />
			<input type="hidden" name="no" value="" />
		</form>
      </div>
      <div class="clear"></div>
    </div>
    <div id="home_mod_tips" class="lean-modal pop_win">
      <div class="h">首页设置<a class="modal_close" href="#"></a></div>
      <div class="tips">首页设置成功</div>
    </div>
  </div>
</div>
</body>
</html>