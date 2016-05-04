      <table border="0" cellpadding="5" cellspacing="0" class="r_con_table">
        <thead>
          <tr>
            <td width="10%" nowrap="nowrap">序号</td>
            <td width="20%" nowrap="nowrap">名称</td>
            <td width="60%" nowrap="nowrap" class="last">Url</td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td nowrap="nowrap">1</td>
            <td nowrap="nowrap">微汽车首页</td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_car/
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">2</td>
            <td nowrap="nowrap">关于我们</td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_car/about/
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">3</td>
            <td nowrap="nowrap">联系销售</td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_car/contact/
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">4</td>
            <td nowrap="nowrap">优惠活动</td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_car/news/1/
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">5</td>
            <td nowrap="nowrap">最新资讯</td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_car/news/2/
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">6</td>
            <td nowrap="nowrap">在线预约</td>
            <td nowrap="nowrap" class="left last">
            	【预约试驾】http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_car/reserve/<br />
                【车主关怀】http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_car/care/
            </td>
          </tr>
        </tbody>
        <thead>
          <tr>
            <td colspan="3">优惠活动</td>
          </tr>
        </thead>
        <tbody>
          <?php
				$list_column = array();
				$i = 0;
				$DB->Get("app_car_article","*","where Users_ID='".$_SESSION["Users_ID"]."' and Column_ID=1 and Article_Type=1 order by Article_ID asc");
				while($r=$DB->fetch_assoc()){
					$list_column[] = $r;
				}
				foreach($list_column as $k=>$v){
					$i++;
		  ?>
          <tr>
            <td nowrap="nowrap"><?php echo $i;?></td>
            <td nowrap="nowrap"><?php echo $v["Article_Title"];?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_car/article/<?php echo $v["Article_ID"];?>/
            </td>
          </tr>
          <?php
		  		}
		  ?>
        </tbody>
        <thead>
          <tr>
            <td colspan="3">最新资讯</td>
          </tr>
        </thead>
        <tbody>
          <?php
				$list_column = array();
				$i = 0;
				$DB->Get("app_car_article","*","where Users_ID='".$_SESSION["Users_ID"]."' and Column_ID=2 and Article_Type=1 order by Article_ID asc");
				while($r=$DB->fetch_assoc()){
					$list_column[] = $r;
				}
				foreach($list_column as $k=>$v){
					$i++;
		  ?>
          <tr>
            <td nowrap="nowrap"><?php echo $i;?></td>
            <td nowrap="nowrap"><?php echo $v["Article_Title"];?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_car/article/<?php echo $v["Article_ID"];?>/
            </td>
          </tr>
          <?php
		  		}
		  ?>
        </tbody>
        <thead>
          <tr>
            <td colspan="3">车系</td>
          </tr>
        </thead>
        <tbody>
          <?php
				$list_column = array();
				$i = 0;
				$DB->Get("app_car_category","*","where Users_ID='".$_SESSION["Users_ID"]."'");
				while($r=$DB->fetch_assoc()){
					$list_column[] = $r;
				}
				foreach($list_column as $k=>$v){
					$i++;
		  ?>
          <tr>
            <td nowrap="nowrap"><?php echo $i;?></td>
            <td nowrap="nowrap"><?php echo $v["Category_Name"];?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_car/category/<?php echo $v["Category_ID"];?>/
            </td>
          </tr>
          <?php
		  		}
		  ?>
        </tbody>
        <thead>
          <tr>
            <td colspan="3">前100款车</td>
          </tr>
        </thead>
        <tbody>
          <?php
				$list_column = array();
				$i = 0;
				$DB->Get("app_car_products","*","where Users_ID='".$_SESSION["Users_ID"]."' order by Products_ID asc");
				while($r=$DB->fetch_assoc()){
					$list_column[] = $r;
				}
				foreach($list_column as $k=>$v){
					$i++;
		  ?>
          <tr>
            <td nowrap="nowrap"><?php echo $i;?></td>
            <td nowrap="nowrap"><?php echo $v["Products_Title"];?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_car/product/<?php echo $v["Products_ID"];?>/
            </td>
          </tr>
          <?php
		  		}
		  ?>
        </tbody>
        <thead>
          <tr>
            <td colspan="3">实用小工具</td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td nowrap="nowrap">1</td>
            <td nowrap="nowrap">车贷计算器</td>
            <td nowrap="nowrap" class="left last">
            	http://car.m.yiche.com/qichedaikuanjisuanqi/
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">2</td>
            <td nowrap="nowrap">保险计算</td>
            <td nowrap="nowrap" class="left last">
            	http://car.m.yiche.com/qichebaoxianjisuan/
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">3</td>
            <td nowrap="nowrap">全款计算</td>
            <td nowrap="nowrap" class="left last">
            	http://car.m.yiche.com/gouchejisuanqi/
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">4</td>
            <td nowrap="nowrap">车型比较</td>
            <td nowrap="nowrap" class="left last">
            	http://car.m.yiche.com/chexingduibi/?carIDs=102501
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">5</td>
            <td nowrap="nowrap">违章查询</td>
            <td nowrap="nowrap" class="left last">
            	http://m.cheshouye.com/api/weizhang/
            </td>
          </tr>
        </tbody>
      </table>