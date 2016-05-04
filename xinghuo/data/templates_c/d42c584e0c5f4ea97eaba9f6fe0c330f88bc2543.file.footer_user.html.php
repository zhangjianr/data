<?php /* Smarty version Smarty-3.1.13, created on 2016-03-27 10:51:12
         compiled from "C:\xh\api\user\html\lbi\footer_user.html" */ ?>
<?php /*%%SmartyHeaderCode:792956f74aa0f244b5-87592452%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd42c584e0c5f4ea97eaba9f6fe0c330f88bc2543' => 
    array (
      0 => 'C:\\xh\\api\\user\\html\\lbi\\footer_user.html',
      1 => 1425631786,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '792956f74aa0f244b5-87592452',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'UsersID' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_56f74aa0f2bec1_59438229',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56f74aa0f2bec1_59438229')) {function content_56f74aa0f2bec1_59438229($_smarty_tpl) {?><div id="footer_user"> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/" class="m0">会员卡</a> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/message/" class="m1">消息
    </a> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/integral/" class="m2">签到&nbsp;&nbsp;</a> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/my/" class="m4">我的</a> </div><?php }} ?>