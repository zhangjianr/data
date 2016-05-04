      <table border="0" cellpadding="5" cellspacing="0" class="r_con_table">
        <thead>
          <tr>
            <td width="10%" nowrap="nowrap">序号</td>
            <td width="20%" nowrap="nowrap">名称</td>
            <td width="60%" nowrap="nowrap" class="last">Url</td>
          </tr>
        </thead>
        <tbody>
          <?php
				$list_column = array();
				$j = 0;
				$DB->getPage("weicbd_products","*","where Users_ID='".$_SESSION["Users_ID"]."' and Products_Status=1 order by Products_Sort asc, Products_ID desc",40);
				while($r=$DB->fetch_assoc()){
					$list_column[] = $r;
				}
				foreach($list_column as $k=>$v){
					$j++;
		  ?>
          <tr>
            <td nowrap="nowrap"><?php echo $j;?></td>
            <td nowrap="nowrap"><?php echo $v["Products_Name"];?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/weicbd/products.php?UsersID=<?php echo $_SESSION["Users_ID"];?>&ProductsID=<?php echo $v["Products_ID"];?>&wxref=mp.weixin.qq.com
            </td>
          </tr>
          <?php
		  		}
		  ?>
        </tbody>
      </table>
	  <div class="blank20"></div>
      <?php $DB->showPage(); ?>