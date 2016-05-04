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
				$i = 0;
				$DB->Get("greeting_cards","*","where Users_ID='".$_SESSION["Users_ID"]."' order by Cards_ID asc");
				while($r=$DB->fetch_assoc()){
					$list_column[] = $r;
				}
				foreach($list_column as $k=>$v){
					$i++;
					$item = $DB->GetRs("greeting_cards_model","*","where Model_ID=".$v["Model_ID"]." order by Model_ID asc");
					$v["Model_Name"] = $item["Model_Name"];
		  ?>
          <tr>
            <td nowrap="nowrap"><?php echo $i;?></td>
            <td nowrap="nowrap"><?php echo $v["Model_Name"];?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/greeting_cards/detail/<?php echo $v["Cards_ID"];?>/
            </td>
          </tr>
          <?php
		  		}
		  ?>
        </tbody>
      </table>