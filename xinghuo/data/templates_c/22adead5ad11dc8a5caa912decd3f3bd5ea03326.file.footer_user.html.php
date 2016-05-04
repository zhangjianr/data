<?php /* Smarty version Smarty-3.1.13, created on 2016-04-04 00:01:34
         compiled from "C:\xinghuo2\api\user\html\lbi\footer_user.html" */ ?>
<?php /*%%SmartyHeaderCode:772857013e5e72ce33-52949660%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '22adead5ad11dc8a5caa912decd3f3bd5ea03326' => 
    array (
      0 => 'C:\\xinghuo2\\api\\user\\html\\lbi\\footer_user.html',
      1 => 1425631786,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '772857013e5e72ce33-52949660',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'UsersID' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_57013e5e734850_92734899',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57013e5e734850_92734899')) {function content_57013e5e734850_92734899($_smarty_tpl) {?><div id="footer_user"> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/" class="m0">会员卡</a> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/message/" class="m1">消息
    </a> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/integral/" class="m2">签到&nbsp;&nbsp;</a> <a href="/api/<?php echo $_smarty_tpl->tpl_vars['UsersID']->value;?>
/user/my/" class="m4">我的</a> </div><?php }} ?>