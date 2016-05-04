<?php /* Smarty version Smarty-3.1.13, created on 2016-03-14 13:32:18
         compiled from "E:\wwwroot\spark\public_html\api\user\html\lbi\footer_user.html" */ ?>
<?php /*%%SmartyHeaderCode:2167256e64ce28bd187-76134752%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'af1964236f6e17b32e34dd154d8712e32a5065ec' => 
    array (
      0 => 'E:\\wwwroot\\spark\\public_html\\api\\user\\html\\lbi\\footer_user.html',
      1 => 1425631786,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2167256e64ce28bd187-76134752',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'UsersID' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_56e64ce28fa215_49211579',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56e64ce28fa215_49211579')) {function content_56e64ce28fa215_49211579($_smarty_tpl) {?><div id="footer_user"> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/" class="m0">会员卡</a> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/message/" class="m1">消息
    </a> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/integral/" class="m2">签到&nbsp;&nbsp;</a> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/my/" class="m4">我的</a> </div><?php }} ?>