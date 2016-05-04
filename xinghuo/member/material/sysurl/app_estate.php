      <?php
      $rsConfig=$DB->GetRs("app_estate_config","*","where Users_ID='".$_SESSION["Users_ID"]."'");
	  ?>
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
            <td nowrap="nowrap">微房产首页</td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_estate/
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">2</td>
            <td nowrap="nowrap"><?php echo $rsConfig['Estate_ArticleName'];?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_estate/introduce/
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">3</td>
            <td nowrap="nowrap"><?php echo $rsConfig['Estate_AlbumsName'];?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_estate/albums/
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">4</td>
            <td nowrap="nowrap"><?php echo $rsConfig['Estate_HouseHoldName'];?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_estate/household/
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">5</td>
            <td nowrap="nowrap"><?php echo $rsConfig['Estate_NewsName'];?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_estate/news/
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">6</td>
            <td nowrap="nowrap"><?php echo $rsConfig['Estate_ReserveName'];?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_estate/reserve/
            </td>
          </tr>
        </tbody>
        <thead>
          <tr>
            <td colspan="3"><?php echo $rsConfig['Estate_AlbumsName'];?></td>
          </tr>
        </thead>
        <tbody>
          <?php
				$list_column = array();
				$i = 0;
				$DB->Get("app_estate_albums_category","*","where Users_ID='".$_SESSION["Users_ID"]."' order by Category_Index asc");
				while($r=$DB->fetch_assoc()){
					$list_column[] = $r;
				}
				foreach($list_column as $k=>$v){
					$i++;
		  ?>
          <tr>
            <td nowrap="nowrap"><?php echo $i;?></td>
            <td nowrap="nowrap"><?php echo $v["Category_Name"] ?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_estate/albums_detail/<?php echo $v["Category_ID"] ?>/
            </td>
          </tr>
          <?php
		  		}
		  ?>
        </tbody>
        <thead>
          <tr>
            <td colspan="3"><?php echo $rsConfig['Estate_NewsName'];?></td>
          </tr>
        </thead>
        <tbody>
          <?php
				$list_column = array();
				$i = 0;
				$DB->Get("app_estate_article","*","where Users_ID='".$_SESSION["Users_ID"]."' order by Article_ID asc");
				while($r=$DB->fetch_assoc()){
					$list_column[] = $r;
				}
				foreach($list_column as $k=>$v){
					$i++;
		  ?>
          <tr>
            <td nowrap="nowrap"><?php echo $i;?></td>
            <td nowrap="nowrap"><?php echo $v["Article_Title"] ?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/app_estate/news/article/<?php echo $v["Article_ID"] ?>/
            </td>
          </tr>
          <?php
		  		}
		  ?>
        </tbody>
      </table>