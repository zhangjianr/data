<?php /* Smarty version Smarty-3.1.13, created on 2016-04-08 00:06:39
         compiled from "C:\xinghuo\api\user\html\lbi\footer_user.html" */ ?>
<?php /*%%SmartyHeaderCode:216175706858fd83133-76215508%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '85c590ae7d0c4fc3293ba5c7cf371a230d1c3b8e' => 
    array (
      0 => 'C:\\xinghuo\\api\\user\\html\\lbi\\footer_user.html',
      1 => 1425631786,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '216175706858fd83133-76215508',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'UsersID' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5706858fd83134_17174671',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5706858fd83134_17174671')) {function content_5706858fd83134_17174671($_smarty_tpl) {?><div id="footer_user"> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/" class="m0">会员卡</a> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/message/" class="m1">消息
    </a> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/integral/" class="m2">签到&nbsp;&nbsp;</a> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/my/" class="m4">我的</a> </div><?php }} ?>