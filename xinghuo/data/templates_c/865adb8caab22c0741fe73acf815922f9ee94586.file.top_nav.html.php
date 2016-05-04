<?php /* Smarty version Smarty-3.1.13, created on 2016-03-14 13:47:14
         compiled from "E:\wwwroot\spark\public_html\api\kanjia\skin\1\lbi\top_nav.html" */ ?>
<?php /*%%SmartyHeaderCode:2648856e650628596c0-14349664%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '865adb8caab22c0741fe73acf815922f9ee94586' => 
    array (
      0 => 'E:\\wwwroot\\spark\\public_html\\api\\kanjia\\skin\\1\\lbi\\top_nav.html',
      1 => 1425864332,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2648856e650628596c0-14349664',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'base_url' => 0,
    'UsersID' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_56e650628d37e6_24887884',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56e650628d37e6_24887884')) {function content_56e650628d37e6_24887884($_smarty_tpl) {?><div class="nav">
		 <div class="container">
			<div class="row">  
      	
        	<div class="col-xs-2 header-icon">              
		  	<a href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/kanjia/"><img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
static/api/kanjia/image/home.png" width="30px" height="30px"/></a>
		</div>
	
          <div class="col-xs-8">
	  <form id="kanjia_search" action="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
api/kanjia/index.php" />
      	<input type="hidden" name="UsersID" value="<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
"/>
      	<input type="hidden" name="search" value="1"/>
      	<input type="text" class="form-control" name="Keyword" id="nav-keyword" aria-describedby="inputSuccess3Status" notnull>
      	<span  id="search_btn" class="icon-search  nav-search-icon grey" aria-hidden="true"></span>
        </form>  
        </div>
		  
      		<div  class="col-xs-2 header-icon ">
       		<a href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/"><img src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
static/api/kanjia/image/user.png" width="30px" height="30px"/></a>
     	 </div>
        	
 	    
        </div>
		
         </div>
	</div>
    <?php }} ?>