<?php
if($_POST)
{
	$no = intval($_POST['no']);
	$Material[]=array(
		"Title"=>["","","","",""],
		"ImgPath"=>$_POST["ImgPathList"],
		"Url"=>$_POST['UrlList'],
	);
	for($i=1; $i<count($json); $i++){
		if($i==$no){
			$Material[]=array(
				"Title"=>$_POST["Title"],
				"ImgPath"=>$_POST["ImgPath"],
				"Url"=>$_POST['Url'],
			);
		}else{
			$Material[] = $json[$i];
		}
	}
	
	$Data=array(
		"Home_Json"=>substr(json_encode($Material,JSON_UNESCAPED_UNICODE), 1, -1),
	);
    $flag=true;
	$Set=$DB->Set("shop_home",$Data,"where Users_ID='".$_SESSION["Users_ID"]."' and Skin_ID=".$rsConfig['Skin_ID']);
	$flag=$flag&&$Set;
	if($flag){
		echo '1';
	}else{
		echo '0';
	}
	exit;
}
?>
    <link href='/static/js/plugin/lean-modal/style.css' rel='stylesheet' type='text/css' />
    <link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/js/plugin/lean-modal/lean-modal.min.js'></script> 
    <script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script>
    <link href='/static/api/shop/skin/<?php echo $rsConfig['Skin_ID'];?>/page.css?t=<?php echo time() ?>' rel='stylesheet' type='text/css' />
    <script language="javascript">var shop_skin_data=[
 {"ContentsType":"1","Title":"[\"<?php echo implode('\",\"',$json[0]['Title']) ?>\"]","ImgPath":"[\"<?php echo $json[0]['ImgPath'][0] ? str_replace('/','\\\\\\/',implode('\",\"',$json[0]['ImgPath'])) : '\\\/api\\\/shop\\\/skin\\\/'.$rsConfig['Skin_ID'].'\\\/banner.jpg'; ?>\"]","Url":"[\"<?php echo str_replace('/','\\\\\\/',implode('\",\"',$json[0]['Url'])) ?>\"]","Postion":"t01","Width":"640","Height":"320","NeedLink":"1"},];
    </script> 
    <script language="javascript">$(document).ready(shop_obj.home_init);</script>
    <div id="home" class="r_con_wrap">
      <div class="m_lefter">
        <div id="shop_skin_index">
          <div class="shop_skin_index_list banner" rel="edit-t01">
            <div class="img"></div>
          </div>
        </div>
      </div>
      <div class="m_righter">
        <form action="home.php" method="post" id="home_form">
          <div id="setbanner">
            <div class="item">
              <div class="rows">
                <div class="b_l"> <strong>图片(1)</strong><span class="tips">大图建议尺寸：
                  <label></label>
                  px</span><a href="#shop_home_img_del" value='0'><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a><br />
                  <div class="blank6"></div>
                  <input type="hidden" name="Title[]" value="" />
                  <div>
                    <input name="FileUpload" id="HomeFileUpload_0" type="file" />
                  </div>
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
                <div class="b_l"> <strong>图片(2)</strong><span class="tips">大图建议尺寸：
                  <label></label>
                  px</span><a href="#shop_home_img_del" value='1'><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a><br />
                  <div class="blank6"></div>
                  <input type="hidden" name="Title[]" value="" />
                  <div>
                    <input name="FileUpload" id="HomeFileUpload_1" type="file" />
                  </div>
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
                <div class="b_l"> <strong>图片(3)</strong><span class="tips">大图建议尺寸：
                  <label></label>
                  px</span><a href="#shop_home_img_del" value='2'><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a><br />
                  
                  <div class="blank6"></div>
                  <input type="hidden" name="Title[]" value="" />
                  <div>
                    <input name="FileUpload" id="HomeFileUpload_1" type="file" />
                  </div>
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
                <div class="b_l"> <strong>图片(4)</strong><span class="tips">大图建议尺寸：
                  <label></label>
                  px</span><a href="#shop_home_img_del" value='3'><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a><br />
                  <div class="blank6"></div>
                  <input type="hidden" name="Title[]" value="" />
                  <div>
                    <input name="FileUpload" id="HomeFileUpload_1" type="file" />
                  </div>
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
                <div class="b_l"> <strong>图片(5)</strong><span class="tips">大图建议尺寸：
                  <label></label>
                  px</span><a href="#shop_home_img_del" value='4'><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a><br />
                  <div class="blank6"></div>
                  <input type="hidden" name="Title[]" value="" />
                  <div>
                    <input name="FileUpload" id="HomeFileUpload_1" type="file" />
                  </div>
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