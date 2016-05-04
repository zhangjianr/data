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
            <td nowrap="nowrap">首页</td>
            <td nowrap="nowrap" class="left last">http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/web/</td>
          </tr>
          <?php
				$list_column = array();
				$i = 1;
				$DB->get("web_column","*","where Users_ID='".$_SESSION["Users_ID"]."' order by Column_Index asc");
				while($r=$DB->fetch_assoc()){
					$list_column[] = $r;
				}
				foreach($list_column as $k=>$v){
					$i++;
		  ?>
          <tr>
            <td nowrap="nowrap"><?php echo $i;?></td>
            <td nowrap="nowrap"><?php echo $v["Column_Name"];?></td>
            <td nowrap="nowrap" class="left last">
            	http://<?php echo $_SERVER["HTTP_HOST"] ?>/api/<?php echo $_SESSION["Users_ID"] ?>/web/column/<?php echo $v["Column_ID"];?>/
                <?php
                	$DB->get("web_article","*","where Users_ID='".$_SESSION["Users_ID"]."' and Column_ID=".$v["Column_ID"]." order by Article_ID asc");
					while($a=$DB->fetch_assoc()){
						echo '<br />【'.$a["Article_Title"].'】http://'.$_SERVER["HTTP_HOST"].'/api/'.$_SESSION["Users_ID"].'/web/article/'.$a["Article_ID"].'/';
					}
				?>
            </td>
          </tr>
          <?php
		  		}
		  ?>
        </tbody>
      </table>