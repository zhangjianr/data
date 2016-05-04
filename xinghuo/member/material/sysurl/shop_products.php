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
			$rsConfig = $DB->GetRs("shop_config","Distribute_Type,Distribute_Limit","where Users_ID='".$_SESSION["Users_ID"]."'");
			if($rsConfig["Distribute_Type"]==3){
				$p = $DB->GetRs("shop_products","Products_Name,Products_ID","where Users_ID='".$_SESSION["Users_ID"]."' and Products_ID=".$rsConfig["Distribute_Limit"]);
		  ?>
		  <tr>
            <td nowrap="nowrap">1</td>
            <td nowrap="nowrap"><?php echo $p["Products_Name"];?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/shop/products_virtual/<?php echo $p["Products_ID"];?>/ (分销门槛购买产品)
            </td>
          </tr>
		  <?php }?>
          <?php
			$j=0;
				$list_column = array();
				if($rsConfig["Distribute_Type"]==3){
					$j = 1;
				}
				$DB->getPage("shop_products","*","where Users_ID='".$_SESSION["Users_ID"]."' order by Products_ID asc",40);
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
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/shop/products/<?php echo $v["Products_ID"];?>/
            </td>
          </tr>
          <?php
		  		}
		  ?>
        </tbody>
      </table>
	  <div class="blank20"></div>
      <?php $DB->showPage(); ?>