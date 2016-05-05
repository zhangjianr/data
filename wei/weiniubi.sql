/*
Navicat MySQL Data Transfer

Source Server         : kkk
Source Server Version : 50614
Source Host           : localhost:3306
Source Database       : weiniubi

Target Server Type    : MYSQL
Target Server Version : 50614
File Encoding         : 65001

Date: 2016-04-13 16:52:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for yehnet_admin
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_admin`;
CREATE TABLE `yehnet_admin` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID号',
  `name` varchar(50) NOT NULL COMMENT '账号',
  `email` varchar(100) NOT NULL COMMENT '邮箱',
  `pass` varchar(50) NOT NULL COMMENT '密码',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1正常0锁定',
  `if_system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1系统管理员0普通管理员',
  `popedom` text NOT NULL COMMENT '权限',
  `langid` varchar(255) NOT NULL COMMENT '可操作的语言权限，系统管理员不限',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_admin
-- ----------------------------
INSERT INTO `yehnet_admin` VALUES ('1', 'admin', 'admin@admin.com', 'c3284d0f94606de1fd2af172aba15bf3', '1', '1', '', '');

-- ----------------------------
-- Table structure for yehnet_cache
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_cache`;
CREATE TABLE `yehnet_cache` (
  `id` varchar(50) NOT NULL COMMENT 'ID号',
  `langid` varchar(5) NOT NULL DEFAULT 'zh' COMMENT '语言ID',
  `content` longtext NOT NULL COMMENT '缓存内容',
  `postdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '缓存时间',
  PRIMARY KEY (`id`,`langid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_cache
-- ----------------------------

-- ----------------------------
-- Table structure for yehnet_cate
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_cate`;
CREATE TABLE `yehnet_cate` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID号',
  `identifier` varchar(30) NOT NULL COMMENT '标识串，必须是唯一的',
  `langid` varchar(5) NOT NULL COMMENT '语言标识',
  `cate_name` varchar(100) NOT NULL COMMENT '分类名称',
  `parentid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID，如果为根分类，则使用0',
  `module_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '模块ID',
  `tpl_index` varchar(100) NOT NULL COMMENT '封面模板',
  `tpl_list` varchar(100) NOT NULL COMMENT '列表模板',
  `tpl_file` varchar(100) NOT NULL COMMENT '内容模板',
  `if_index` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是封面，0否1是',
  `taxis` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序，值越小越往前靠',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1使用中0禁用',
  `if_hidden` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1隐藏0显示',
  `keywords` varchar(255) NOT NULL COMMENT 'SEO关键字',
  `description` varchar(255) NOT NULL COMMENT 'SEO描述',
  `ifspec` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0非单页1单页',
  `note` text NOT NULL COMMENT '简要描述',
  `psize` tinyint(3) unsigned NOT NULL DEFAULT '30' COMMENT '每页显示数量，默认30',
  `inpic` varchar(100) NOT NULL COMMENT '前台默认图片关联',
  `linkurl` varchar(255) NOT NULL COMMENT '自定义链接',
  `target` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '新窗口打开1是0否',
  `ordertype` varchar(100) NOT NULL DEFAULT 'post_date:desc' COMMENT '排序类型，默认是发布时间',
  `subcate` varchar(100) NOT NULL COMMENT '分类副标题',
  `ico` varchar(255) NOT NULL COMMENT '图标',
  `small_pic` varchar(255) NOT NULL COMMENT '小图',
  `medium_pic` varchar(255) NOT NULL COMMENT '中图',
  `big_pic` varchar(255) NOT NULL COMMENT '大图',
  `fields` varchar(255) NOT NULL COMMENT '有效字段',
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_cate
-- ----------------------------

-- ----------------------------
-- Table structure for yehnet_commission
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_commission`;
CREATE TABLE `yehnet_commission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID号',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `uname` varchar(200) NOT NULL,
  `cid` mediumint(8) NOT NULL,
  `username` varchar(100) NOT NULL COMMENT '会员名称',
  `proname` varchar(220) NOT NULL DEFAULT '0',
  `ctype` varchar(20) NOT NULL DEFAULT '0',
  `money` float NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0未审核1正常2锁定',
  `postdate` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `listid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_commission
-- ----------------------------
INSERT INTO `yehnet_commission` VALUES ('1', '10', '彭凯', '6', '熊军', '九墅', '佣金', '1000', '1', '1411548305');

-- ----------------------------
-- Table structure for yehnet_customer
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_customer`;
CREATE TABLE `yehnet_customer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID号',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `uname` varchar(200) NOT NULL,
  `username` varchar(100) NOT NULL COMMENT '会员名称',
  `cellphone` varchar(20) NOT NULL,
  `proname` varchar(220) NOT NULL DEFAULT '0',
  `appointment_date` varchar(10) NOT NULL DEFAULT '0',
  `appointment_time` varchar(10) NOT NULL,
  `remark` text NOT NULL,
  `guwenname` varchar(100) NOT NULL,
  `guwentel` varchar(100) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0未审核1正常2锁定',
  `postdate` int(10) unsigned NOT NULL DEFAULT '0',
  `daofang` int(1) NOT NULL DEFAULT '0',
  `dfnote` text NOT NULL,
  `renchou` int(1) NOT NULL DEFAULT '0',
  `rcnote` text NOT NULL,
  `rengou` int(1) NOT NULL DEFAULT '0',
  `rgnote` text NOT NULL,
  `qianyue` int(1) NOT NULL DEFAULT '0',
  `qynote` text NOT NULL,
  `huikuan` int(1) NOT NULL DEFAULT '0',
  `hknote` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `listid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_customer
-- ----------------------------
INSERT INTO `yehnet_customer` VALUES ('1', '4', '张三丰', '李四', '18888585858', '金域缇香', '20141111', '14:00', '', '', '', '0', '1411525109', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('2', '6', '熊峻', '彭凯', '15807936588', '金色乐府', '2014', '08:00', '', '', '', '0', '1411529585', '1', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('3', '7', '杨哲', '你具体', '15232225522', '金色乐府', '12.5', '10:30', '', '', '', '0', '1411534100', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('4', '8', '谭总', '财哥', '15198992013', '天逸', '9月30日', '12:00', '', '', '', '0', '1411545847', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('5', '9', '花树彪', '刘总', '15620652692', '金色乐府', '27', '11:00', '', '', '', '0', '1411546114', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('6', '10', '彭凯', '熊军', '18607939996', '金域缇香', '2014', '08:00', '', '张末', '18888888888', '1', '1411548249', '1', '321', '1', '', '1', '', '1', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('7', '12', '苏皓', '苏皓', '15128225205', '天逸', '7', '10:30', '', '', '', '0', '1411550383', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('8', '13', '很咯', '聊几句', '18259067996', '天逸', '20148', '16:00', '', '', '', '0', '1411553537', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('9', '14', '张琳', '我啦啊', '15656565656', '金色乐府', '2014', '08:00', '', '', '', '0', '1411561852', '1', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('10', '15', '李晋博', '张睿', '13698565874', '龙山', '25', '15:00', '', '', '', '0', '1411566221', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('11', '10', '彭凯', '余芳', '13627039069', '金域缇香', '2014', '12:00', '', '', '', '0', '1411574042', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('12', '16', '饭否', '飞飞', '13582552369', '金色乐府', '20140925', '18:00', '', '', '', '0', '1411579403', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('13', '17', 'jjj', 'www', '13355556666', '金色乐府', '2014.01.11', '14:00', '', '', '', '0', '1411615887', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('14', '18', 'www', 'wer', '13366665555', '金色乐府', '2014.1.1', '15:00', '', '', '', '0', '1411617834', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('15', '20', '张三', '李四', '13666666666', '金色乐府', '2014.9.9', '12:00', '此客户想购买面积为120平方左右的房子。朝南向。', '', '', '0', '1411628404', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('16', '21', '小胡', '小鸡', '18671449663', '九墅', '明天', '12:00', '', '', '', '0', '1411632566', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('17', '10', '彭凯', '王二', '18679389996', '天逸', '2014', '11:00', '', '', '', '0', '1411635088', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('18', '22', '李四', '张木木', '13654625879', '金域缇香', '2014', '15:00', '此客户需求120平方。', '', '', '0', '1411638141', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('19', '28', 'feiji', '有可以', '18253698574', '金域缇香', '5588666', '12:00', '', '', '', '0', '1411717617', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('20', '27', '曲志高', '赵迪', '13733128085', '金域缇香', '9月30号', '14:00', '', '', '', '0', '1411718970', '0', '', '0', '', '0', '', '0', '', '0', '');
INSERT INTO `yehnet_customer` VALUES ('21', '32', '马良', '乔刘佳', '15386657507', '九墅', '2014-09-28', '08:00', '看看旅途', '', '', '1', '1411783623', '1', '已经成交', '1', '', '1', '', '1', '', '1', '');

-- ----------------------------
-- Table structure for yehnet_gd
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_gd`;
CREATE TABLE `yehnet_gd` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID号',
  `pictype` varchar(50) NOT NULL DEFAULT '' COMMENT '图片类型标识',
  `picsubject` varchar(255) NOT NULL DEFAULT '' COMMENT '类型名称',
  `width` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片宽度',
  `height` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片高度',
  `water` varchar(255) NOT NULL DEFAULT '' COMMENT '水印图片位置',
  `picposition` varchar(100) NOT NULL DEFAULT '' COMMENT '水印位置',
  `trans` tinyint(3) unsigned NOT NULL DEFAULT '65' COMMENT '透明度，默认是60',
  `cuttype` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '图片生成方式，支持缩放法和裁剪法两种，默认使用缩放法',
  `quality` tinyint(3) unsigned NOT NULL DEFAULT '80' COMMENT '图片生成质量，默认是80',
  `border` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否支持边框，1是0否',
  `bordercolor` varchar(10) NOT NULL DEFAULT 'FFFFFF' COMMENT '边框颜色',
  `padding` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '间距，默认是0,最大不超过255',
  `bgcolor` varchar(10) NOT NULL DEFAULT 'FFFFFF' COMMENT '补白背景色，默认是白色',
  `bgimg` varchar(255) NOT NULL DEFAULT '' COMMENT '背景图片，默认为空',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否使用，默认是使用',
  `taxis` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序，值越小越往前靠，最大不超过255，最小为0',
  `edit_default` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_gd
-- ----------------------------
INSERT INTO `yehnet_gd` VALUES ('1', 'thumb', '头像缩图', '75', '75', '', 'middle-middle', '50', '0', '80', '0', '', '0', 'CD332C', '', '1', '1', '0');
INSERT INTO `yehnet_gd` VALUES ('3', 'big', '大图', '600', '600', '', 'bottom-right', '70', '2', '80', '0', '', '0', 'FFFFFF', '', '1', '2', '1');
INSERT INTO `yehnet_gd` VALUES ('2', 'mid', '项目标志', '282', '120', '', 'middle-middle', '50', '1', '60', '0', '', '0', 'CD332C', '', '1', '2', '0');

-- ----------------------------
-- Table structure for yehnet_identifier
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_identifier`;
CREATE TABLE `yehnet_identifier` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `sign` varchar(32) NOT NULL COMMENT '标识符，用于本系统内所有需要此功能，仅限字母数字及下划线且第一个必须是字母',
  `title` varchar(100) NOT NULL COMMENT '名称',
  `langid` varchar(5) NOT NULL COMMENT '语言编号，如zh,en等',
  `module_id` mediumint(8) unsigned NOT NULL COMMENT '一个标识符只能用于一个模块，一个模块有多个标识符',
  `if_system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1系统0自定义',
  `g_sign` varchar(100) NOT NULL COMMENT '组标识，仅在核心技术中使用',
  `taxis` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_identifier
-- ----------------------------
INSERT INTO `yehnet_identifier` VALUES ('1', 'popedom', '权限管理', 'zh', '1', '1', '', '255');
INSERT INTO `yehnet_identifier` VALUES ('2', 'module', '模块管理', 'zh', '2', '1', '', '255');
INSERT INTO `yehnet_identifier` VALUES ('3', 'add', '添加', 'zh', '0', '1', 'popedom', '201');
INSERT INTO `yehnet_identifier` VALUES ('4', 'modify', '修改', 'zh', '0', '1', 'popedom', '202');
INSERT INTO `yehnet_identifier` VALUES ('5', 'check', '审核', 'zh', '0', '1', 'popedom', '203');
INSERT INTO `yehnet_identifier` VALUES ('6', 'delete', '删除', 'zh', '0', '1', 'popedom', '204');
INSERT INTO `yehnet_identifier` VALUES ('7', 'list', '查看', 'zh', '0', '1', 'popedom', '200');
INSERT INTO `yehnet_identifier` VALUES ('8', 'setting', '设置', 'zh', '0', '1', 'popedom', '205');
INSERT INTO `yehnet_identifier` VALUES ('9', 'save', '存储', 'zh', '0', '1', 'popedom', '206');
INSERT INTO `yehnet_identifier` VALUES ('10', 'group', '组管理', 'zh', '0', '1', 'popedom', '255');

-- ----------------------------
-- Table structure for yehnet_input
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_input`;
CREATE TABLE `yehnet_input` (
  `input` varchar(50) NOT NULL COMMENT '扩展框类型',
  `langid` varchar(5) NOT NULL DEFAULT 'zh' COMMENT '语言ID',
  `name` varchar(100) NOT NULL COMMENT '名字',
  `taxis` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `ifuser` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否允许会员表使用0否1是',
  PRIMARY KEY (`input`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_input
-- ----------------------------
INSERT INTO `yehnet_input` VALUES ('text', 'zh', '文本框', '10', '1');
INSERT INTO `yehnet_input` VALUES ('radio', 'zh', '单选框', '20', '1');
INSERT INTO `yehnet_input` VALUES ('checkbox', 'zh', '复选框', '30', '1');
INSERT INTO `yehnet_input` VALUES ('textarea', 'zh', '文本区域', '40', '1');
INSERT INTO `yehnet_input` VALUES ('edit', 'zh', '可视化编辑器', '50', '0');
INSERT INTO `yehnet_input` VALUES ('select', 'zh', '下拉菜单', '60', '1');
INSERT INTO `yehnet_input` VALUES ('img', 'zh', '图片选择器', '70', '1');
INSERT INTO `yehnet_input` VALUES ('video', 'zh', '影音选择器', '80', '1');
INSERT INTO `yehnet_input` VALUES ('download', 'zh', '下载框选择器', '90', '1');
INSERT INTO `yehnet_input` VALUES ('opt', 'zh', '联动选择', '100', '1');
INSERT INTO `yehnet_input` VALUES ('simg', 'zh', '图片选择器（单张）', '75', '1');
INSERT INTO `yehnet_input` VALUES ('module', 'zh', '内联模块', '110', '0');

-- ----------------------------
-- Table structure for yehnet_lang
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_lang`;
CREATE TABLE `yehnet_lang` (
  `langid` varchar(5) NOT NULL DEFAULT 'zh' COMMENT '语言ID',
  `title` varchar(100) NOT NULL COMMENT '显示名',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态1不使用2使用',
  `note` varchar(255) NOT NULL COMMENT '描述',
  `taxis` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序，小值排前',
  `ifdefault` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是系统默认',
  `ifsystem` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1系统语言0应用语言',
  `ico` varchar(255) NOT NULL COMMENT '图标',
  `small_pic` varchar(255) NOT NULL COMMENT '小图',
  `medium_pic` varchar(255) NOT NULL COMMENT '中图',
  `big_pic` varchar(255) NOT NULL COMMENT '大图',
  PRIMARY KEY (`langid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_lang
-- ----------------------------
INSERT INTO `yehnet_lang` VALUES ('zh', '简体中文', '1', '', '1', '1', '1', '', '', '', '');

-- ----------------------------
-- Table structure for yehnet_lang_msg
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_lang_msg`;
CREATE TABLE `yehnet_lang_msg` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID号',
  `langid` varchar(5) NOT NULL DEFAULT 'zh' COMMENT '语言ID',
  `ltype` enum('www','admin','all') NOT NULL DEFAULT 'all' COMMENT '语言包应用范围',
  `var` varchar(100) NOT NULL COMMENT '语言变量名，仅英文数字及下划线',
  `val` varchar(255) NOT NULL COMMENT '语言值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=469 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_lang_msg
-- ----------------------------
INSERT INTO `yehnet_lang_msg` VALUES ('1', 'zh', 'admin', 'cp_name', '后台管理');
INSERT INTO `yehnet_lang_msg` VALUES ('2', 'zh', 'all', 'error_note', '如果系统不能在 <span style=\"color:red;\">{time}</span> 秒后自动返回，请点这里');
INSERT INTO `yehnet_lang_msg` VALUES ('11', 'zh', 'admin', 'select_cate', '请选择分类');
INSERT INTO `yehnet_lang_msg` VALUES ('4', 'zh', 'admin', 'no_popedom', 'Error: 对不起，您没有操作此功能权限');
INSERT INTO `yehnet_lang_msg` VALUES ('5', 'zh', 'admin', 'login_false', '管理员登录失败，请检查…');
INSERT INTO `yehnet_lang_msg` VALUES ('6', 'zh', 'admin', 'login_not_user_pass', '账号或密码不能为空');
INSERT INTO `yehnet_lang_msg` VALUES ('7', 'zh', 'admin', 'login_success', '欢迎您 <span style=\'color:red;\'>{admin_realname}</span> 登录网站系统后台，请稍候…');
INSERT INTO `yehnet_lang_msg` VALUES ('8', 'zh', 'all', 'login_vcode_empty', '验证码不能为空');
INSERT INTO `yehnet_lang_msg` VALUES ('9', 'zh', 'all', 'login_vcode_false', '验证码填写错误');
INSERT INTO `yehnet_lang_msg` VALUES ('10', 'zh', 'admin', 'logout_success', '管理员 <span style=\'color:red;\'>{admin_realname}</span> 成功退出！');
INSERT INTO `yehnet_lang_msg` VALUES ('23', 'zh', 'www', 'login_false_empty', '登录失败，账号或密码为空！');
INSERT INTO `yehnet_lang_msg` VALUES ('24', 'zh', 'www', 'login_false_rs', '登录失败，会员信息不存在，请检查。');
INSERT INTO `yehnet_lang_msg` VALUES ('25', 'zh', 'www', 'login_false_password', '登录失败，会员密码不正确。');
INSERT INTO `yehnet_lang_msg` VALUES ('26', 'zh', 'www', 'login_false_lock', '登录失败，会员账号已被管理员锁定，请联系管理员。');
INSERT INTO `yehnet_lang_msg` VALUES ('27', 'zh', 'www', 'login_false_check', '登录失败，您的账号尚未激活！');
INSERT INTO `yehnet_lang_msg` VALUES ('28', 'zh', 'www', 'login_usccess', '您的账号已经正常登录，请稍候……');
INSERT INTO `yehnet_lang_msg` VALUES ('29', 'zh', 'www', 'login_exists', '您已经登录，请返回…');
INSERT INTO `yehnet_lang_msg` VALUES ('30', 'zh', 'www', 'module_is_close', '模块未启用');
INSERT INTO `yehnet_lang_msg` VALUES ('31', 'zh', 'www', 'not_any_title_in_module', '没有任何相关主题');
INSERT INTO `yehnet_lang_msg` VALUES ('32', 'zh', 'www', 'not_found_any_module', '没有找到模块信息！');
INSERT INTO `yehnet_lang_msg` VALUES ('34', 'zh', 'www', 'not_any_cate_in_module', '当前模块中没有任何分类信息');
INSERT INTO `yehnet_lang_msg` VALUES ('59', 'zh', 'www', 'download_error', '没有指定附件信息！');
INSERT INTO `yehnet_lang_msg` VALUES ('60', 'zh', 'www', 'download_empty', '附件已经不存在！');
INSERT INTO `yehnet_lang_msg` VALUES ('61', 'zh', 'www', 'login', '合伙人登录');
INSERT INTO `yehnet_lang_msg` VALUES ('63', 'zh', 'www', 'login_user_email_chk', '账号或邮箱不允许为空！');
INSERT INTO `yehnet_lang_msg` VALUES ('64', 'zh', 'www', 'login_not_user_email', '账号不存在或是邮箱填写不正确！');
INSERT INTO `yehnet_lang_msg` VALUES ('467', 'zh', 'www', 'recommend', '推荐客户');
INSERT INTO `yehnet_lang_msg` VALUES ('468', 'zh', 'www', 'customer', '我的客户');
INSERT INTO `yehnet_lang_msg` VALUES ('67', 'zh', 'www', 'login_not_code_user', '会员账号或验证串不允许为空');
INSERT INTO `yehnet_lang_msg` VALUES ('68', 'zh', 'www', 'login_not_user', '账号不存在！');
INSERT INTO `yehnet_lang_msg` VALUES ('71', 'zh', 'www', 'login_not_pass', '密码不允许为空！');
INSERT INTO `yehnet_lang_msg` VALUES ('72', 'zh', 'www', 'login_error_pass', '两次输入的密码不一致！');
INSERT INTO `yehnet_lang_msg` VALUES ('73', 'zh', 'www', 'login_update', '会员密码更新成功！');
INSERT INTO `yehnet_lang_msg` VALUES ('74', 'zh', 'www', 'msg_not_id', '获取数据失败，没有指定主题标识串或ID');
INSERT INTO `yehnet_lang_msg` VALUES ('75', 'zh', 'www', 'msg_not_rs', '无法获取内容信息，请检查');
INSERT INTO `yehnet_lang_msg` VALUES ('76', 'zh', 'www', 'open_user', '非会员不支持此功能！');
INSERT INTO `yehnet_lang_msg` VALUES ('77', 'zh', 'all', 'open_not_picture', '批量生成图片错误，没有取得一张有效图片');
INSERT INTO `yehnet_lang_msg` VALUES ('78', 'zh', 'all', 'open_not_id', '没有指定要生成的图片ID');
INSERT INTO `yehnet_lang_msg` VALUES ('79', 'zh', 'all', 'open_pl_ok', '图片批量生成完毕');
INSERT INTO `yehnet_lang_msg` VALUES ('80', 'zh', 'all', 'open_pl_wait', '请稍候，系统正在批量生成新的图片方案');
INSERT INTO `yehnet_lang_msg` VALUES ('81', 'zh', 'all', 'open_not_pre_id', '没有选择要预览的ID');
INSERT INTO `yehnet_lang_msg` VALUES ('82', 'zh', 'www', 'please_login', '请先登录！');
INSERT INTO `yehnet_lang_msg` VALUES ('83', 'zh', 'www', 'usercp', '会员中心');
INSERT INTO `yehnet_lang_msg` VALUES ('86', 'zh', 'all', 'error', '操作有错误，请检查！');
INSERT INTO `yehnet_lang_msg` VALUES ('93', 'zh', 'all', 'all_category', '全部分类');
INSERT INTO `yehnet_lang_msg` VALUES ('94', 'zh', 'all', 'category_select', '请选择分类');
INSERT INTO `yehnet_lang_msg` VALUES ('97', 'zh', 'all', 'error_save', '数据存储失败，请检查！');
INSERT INTO `yehnet_lang_msg` VALUES ('98', 'zh', 'all', 'save_success', '数据存储成功，请稍候…');
INSERT INTO `yehnet_lang_msg` VALUES ('99', 'zh', 'all', 'del_not_id', 'error：删除失败，没有指定ID');
INSERT INTO `yehnet_lang_msg` VALUES ('101', 'zh', 'www', 'is_logined', '您已经登录了，不能使用注册功能');
INSERT INTO `yehnet_lang_msg` VALUES ('102', 'zh', 'all', 'register', '合伙人注册');
INSERT INTO `yehnet_lang_msg` VALUES ('103', 'zh', 'www', 'empty_pass', '密码不允许为空！');
INSERT INTO `yehnet_lang_msg` VALUES ('104', 'zh', 'www', 'pass_not_right', '两次输入的密码不一致');
INSERT INTO `yehnet_lang_msg` VALUES ('106', 'zh', 'www', 'register_ok', '恭喜您注册成为我们的合伙人');
INSERT INTO `yehnet_lang_msg` VALUES ('107', 'zh', 'www', 'user_exists', '账号已经存在');
INSERT INTO `yehnet_lang_msg` VALUES ('108', 'zh', 'www', 'empty_user', '账号不允许为空');
INSERT INTO `yehnet_lang_msg` VALUES ('109', 'zh', 'all', 'error_not_id', 'error：操作错误，没有取得ID信息');
INSERT INTO `yehnet_lang_msg` VALUES ('110', 'zh', 'all', 'error_not_rs', 'error：操作错误，没有取得数据信息');
INSERT INTO `yehnet_lang_msg` VALUES ('114', 'zh', 'all', 'search', '站内搜索');
INSERT INTO `yehnet_lang_msg` VALUES ('116', 'zh', 'www', 'user_not_login', '非会员不允许执行此操作，请先登录！');
INSERT INTO `yehnet_lang_msg` VALUES ('117', 'zh', 'www', 'usercp_info', '修改资料');
INSERT INTO `yehnet_lang_msg` VALUES ('118', 'zh', 'www', 'usercp_save_success', '会员信息更新成功！');
INSERT INTO `yehnet_lang_msg` VALUES ('119', 'zh', 'www', 'usercp_changepass', '修改个人密码');
INSERT INTO `yehnet_lang_msg` VALUES ('120', 'zh', 'www', 'usercp_not_oldpass', '旧密码为空或是旧密码填写不正确！');
INSERT INTO `yehnet_lang_msg` VALUES ('121', 'zh', 'www', 'usercp_not_newpass', '新密码不允许为空或是两次输入的新密码不一致！');
INSERT INTO `yehnet_lang_msg` VALUES ('122', 'zh', 'www', 'usercp_old_new', '新旧密码一致，不需要修改！');
INSERT INTO `yehnet_lang_msg` VALUES ('123', 'zh', 'www', 'pass_save_success', '密码已经更新成功，下次请用新密码登录。');
INSERT INTO `yehnet_lang_msg` VALUES ('344', 'zh', 'all', 'page_home', '首页');
INSERT INTO `yehnet_lang_msg` VALUES ('345', 'zh', 'all', 'page_prev', '上一页');
INSERT INTO `yehnet_lang_msg` VALUES ('346', 'zh', 'all', 'page_next', '下一页');
INSERT INTO `yehnet_lang_msg` VALUES ('347', 'zh', 'all', 'page_last', '尾页');
INSERT INTO `yehnet_lang_msg` VALUES ('348', 'zh', 'all', 'not_popedom', '您没有此权限！');
INSERT INTO `yehnet_lang_msg` VALUES ('349', 'zh', 'www', 'logout_user_success', '成功退出');

-- ----------------------------
-- Table structure for yehnet_list
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_list`;
CREATE TABLE `yehnet_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `module_id` mediumint(8) unsigned NOT NULL COMMENT '模块ID',
  `cate_id` mediumint(8) unsigned NOT NULL COMMENT '分类ID',
  `title` varchar(255) NOT NULL COMMENT '主题',
  `subtitle` varchar(255) NOT NULL COMMENT '副标题',
  `style` varchar(255) NOT NULL COMMENT '主题样式',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态，1正常0锁定',
  `hidden` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1隐藏0显示',
  `link_url` varchar(255) NOT NULL COMMENT '访问网址',
  `target` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否在新窗口打开1是0否',
  `author` varchar(100) NOT NULL COMMENT '发布人',
  `author_type` enum('admin','user','guest') NOT NULL DEFAULT 'user' COMMENT '发布人类型',
  `keywords` varchar(255) NOT NULL COMMENT '关键字，标签',
  `description` varchar(255) NOT NULL COMMENT '简要描述用于SEO优化',
  `note` text NOT NULL COMMENT '简要描述，用于列表简要说明',
  `identifier` varchar(100) NOT NULL COMMENT '访问标识串，为空时使用系统ID',
  `hits` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点击率',
  `good_hits` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支持次数',
  `bad_hits` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拍砖次数',
  `post_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `modify_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  `thumb_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '缩略图ID',
  `istop` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1置顶0非置顶',
  `isvouch` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1推荐0非推荐',
  `isbest` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1精华0非精华',
  `langid` varchar(5) NOT NULL DEFAULT 'zh' COMMENT '语言ID，默认是中文',
  `points` int(10) NOT NULL DEFAULT '0' COMMENT '积分，点数',
  `ip` varchar(100) NOT NULL COMMENT '发布人IP号',
  `replydate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后回复时间',
  `taxis` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '自定义排序，值越大越往前靠',
  `htmltype` enum('mid','cateid','date','root') NOT NULL DEFAULT 'date' COMMENT 'HTML存储方式，默认是以时间来存储',
  `tplfile` varchar(100) NOT NULL COMMENT '模板文件',
  `star` float unsigned NOT NULL DEFAULT '0' COMMENT '星级评论，默认为0，根据评论表中的星数来决定',
  `yongjin` varchar(200) NOT NULL,
  `jili` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`,`cate_id`,`title`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_list
-- ----------------------------
INSERT INTO `yehnet_list` VALUES ('1', '2', '0', '注册协议', '', '', '1', '0', '', '0', 'admin', '', '', '', '', '', '54', '0', '0', '1407889552', '1411232737', '0', '0', '0', '0', 'zh', '0', '127.0.0.1', '0', '0', 'cateid', '', '0', '', '');
INSERT INTO `yehnet_list` VALUES ('2', '2', '0', '活动细则', '', '', '1', '0', '', '0', 'admin', '', '', '', '', '', '105', '0', '0', '1407889644', '1411204961', '0', '0', '0', '0', 'zh', '0', '127.0.0.1', '0', '0', 'cateid', '', '0', '', '');
INSERT INTO `yehnet_list` VALUES ('3', '2', '0', '推荐流程', '', '', '1', '0', '', '0', 'admin', '', '', '', '', '', '0', '0', '0', '1407889679', '1407918347', '0', '0', '0', '0', 'zh', '0', '127.0.0.1', '0', '0', 'cateid', '', '0', '', '');
INSERT INTO `yehnet_list` VALUES ('4', '3', '0', '九墅', '', '', '1', '0', '', '0', 'admin', '', '', '', '', '', '20', '0', '0', '1407916090', '1411204694', '1', '0', '0', '0', 'zh', '0', '127.0.0.1', '0', '0', 'cateid', '', '0', '4.0‰的总房款', '到访10.00元');
INSERT INTO `yehnet_list` VALUES ('5', '3', '0', '公园五号', '', '', '1', '0', 'http://xinjiang.juzhen.net/2014/partner2014/zygy.shtml', '0', 'admin', '', '', '', '', '', '2', '0', '0', '1407920821', '1411525190', '2', '0', '0', '0', 'zh', '0', '127.0.0.1', '0', '0', 'cateid', '', '0', '1.0%的总房款', '到访10.00元');
INSERT INTO `yehnet_list` VALUES ('6', '3', '0', '龙山', '', '', '1', '0', 'http://xinjiang.juzhen.net/2014/partner2014/lanqiao.shtml', '0', 'admin', '', '', '', '', '', '1', '0', '0', '1407920866', '1411525166', '3', '0', '0', '0', 'zh', '0', '127.0.0.1', '0', '0', 'cateid', '', '0', '1.0%的总房款', '到访10.00元');
INSERT INTO `yehnet_list` VALUES ('7', '3', '0', '天逸', '', '', '1', '0', 'http://xinjiang.juzhen.net/2014/partner2014/guoji.shtml', '0', 'admin', '', '', '', '', '', '1', '0', '0', '1407921042', '1411525137', '4', '0', '0', '0', 'zh', '0', '127.0.0.1', '0', '0', 'cateid', '', '0', '1.0%的总房款', '到访10.00元');
INSERT INTO `yehnet_list` VALUES ('8', '3', '0', '金域缇香', '', '', '1', '0', 'http://xinjiang.juzhen.net/2014/partner2014/tixiang.shtml', '0', 'admin', '', '', '', '', '', '3', '0', '0', '1407921082', '1411525060', '5', '0', '0', '0', 'zh', '0', '127.0.0.1', '0', '0', 'cateid', '', '0', '4.0‰的总房款', '到访10.00元');
INSERT INTO `yehnet_list` VALUES ('9', '3', '0', '金色乐府', '', '', '1', '0', 'http://xinjiang.juzhen.net/2014/partner2014/huafu.shtml', '0', 'admin', '', '', '', '', '', '22', '0', '0', '1407921108', '1411709720', '6', '0', '0', '0', 'zh', '0', '127.0.0.1', '0', '0', 'cateid', '', '0', '2000', '');

-- ----------------------------
-- Table structure for yehnet_list_c
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_list_c`;
CREATE TABLE `yehnet_list_c` (
  `id` int(10) unsigned NOT NULL COMMENT '主题ID',
  `field` varchar(30) NOT NULL DEFAULT '' COMMENT '字段名',
  `val` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`,`field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_list_c
-- ----------------------------
INSERT INTO `yehnet_list_c` VALUES ('1', 'content', '<p class=\"grey2 txt_indent f12 mt25 clearfix\">欢迎您使用新疆万科合伙人软件产品，我们将竭诚为您提供更全面、更优质的服务。本协议适用于新疆万科开发的商品房。在您注册之前，请先仔细阅读本协议的条款。您在使用程序前请点击&quot;我已阅读并同意以上条款&quot;按钮即表示您与公司达成协议，视为您已了解并完全接受本协议项下的全部条款。</p><p class=\"f14 grey1 mt25 clearfix\"><span class=\"b l\">1、</span><span class=\"r s4 b\">协议的完善和修改</span></p>    <p class=\"grey2 f12\">公司有权在必要时将根据互联网的发展和中华人民共和国有关法律、法规的变化，不断地完善服务质量并依此修改本协议的条款，修改后的协议条款将及时予以公布，用户确认公司无需就协议条款的修改事宜逐一通知用户。用户继续使用公司软件产品的，视为该用户接受修改后的协议条款，有关公司与该用户之间的权利以及义务的表述关系，均适用以最新修改后的协议条款为准。</p>    <p class=\"f14 grey1 mt25 clearfix\"><span class=\"b l\">2、</span><span class=\"r s4 b\">资质认证及推荐流程须知</span></p>    <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">2.1</span><span class=\"r s4\">合伙人身份认证：</span></p>    <p class=\"grey2 f12\">合伙人需年满18周岁，具有完全民事行为能力，并能够提供合法的身份证明，且合伙人需保证注册个人信息的真实性，不得以虚假信息注册，否则将被取消推荐资格，公司有权收回一切合伙人权益。</p><p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">2.2</span><span class=\"r s4\">被推荐人身份认证：</span></p>    <p class=\"grey2 f12\">由合伙人推荐的被推荐人，不得是合伙人本人或其直系亲属，且经系统筛查，被推荐人自到访之日起前30天内无到访新疆万科在售项目记录，则认定为新客户，否则相应的推荐行为无效。</p>    <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">2.3</span><span class=\"r s4\">信息传播的真实性：</span></p>    <p class=\"grey2 f12\">合伙人需通过万科现场培训、网络培训等形式，实际掌握各项目资料、在售产品情况，并真实、准确传播项目相关销售信息，不得弄虚作假传播任何虚假信息；</p>    <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">2.4</span><span class=\"r s4\">推荐有效期限：</span></p>    <p class=\"grey2 f12\">推荐双方经身份认证后，推荐人必须提前在&quot;我的客户&quot;模块中预约到访时间【预约时间最长不超过3天，若超过，则自动过期；预约时间最迟不得超过2014年8月31日】；若无预约记录，则不能享有推荐奖励；</p>    <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">2.5</span><span class=\"r s4\">具体推荐流程及奖励方式：</span></p>    <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">2.5.1</span><span class=\"r s2\">被推荐人权益：</span></p><p class=\"grey2 f12\">被推荐人在成功购买合伙人推荐的住宅时，若在2014年7月31日前完成《商品房买卖合同》的签订与签约，可在对应楼盘正常对外折扣的基础上，<span class=\"red\">额外享受该房源公示总价的1%优惠，逾期作废。</span></p>     <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">2.5.2</span><span class=\"r s2\">合伙人权益：</span></p>     <p class=\"grey2 f12\">被推荐人成功认购，<span class=\"red\">合伙人即可获得现金奖励500元</span>；被推荐人完成网上备案并成功签订《商品房买卖合同》，合伙人即可获得<span class=\"red\">最终成交价4‰（税前）推荐奖励（含500元）。</span></p>     <p class=\"grey2 f12\">（万科自有营销团队合伙人可获得5‰（税前）推荐奖励）</p>         <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">2.6</span><span class=\"r s2\">奖励结算方式：</span></p>     <p class=\"grey2 f12\">合伙人推荐成功后，合伙人和被推荐人需在2014年8月31日前到楼盘销售现场书面签订《新疆万科合伙人推荐表》确认双方身份，否则双方不享受相应的推荐奖励。</p>     <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">2.7</span><span class=\"r s5 b\">新疆万科对此次活动保留解释和说明的权利。</span></p>          <p class=\"f14 grey1 mt25 clearfix\"><span class=\"b l\">3.</span><span class=\"r s4 b\">注册信息和隐私保护</span></p>     <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">3.1</span><span class=\"r s5\">用户完成注册申请手续后，获得帐号的使用权，帐号的所有权归公司所有。用户应提供及时、详尽及准确的个人资料，并不断更新注册资料，符合及时、详尽、准确的要求。如果因注册信息不真实而引起的问题，并对问题发生所带来的后果，公司不负任何责任。</span></p>     <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">3.2</span><span class=\"r s5\">用户不应将其帐号、密码转让或出借予他人使用。如用户发现其帐号遭他人非法使用，应立即通知公司。因黑客行为或用户的保管疏忽导致帐号、密码遭他人非法使用，公司不承担任何责任。</span></p>     <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">3.3</span><span class=\"r s5\">用户因忘记密码遗失或密码被盗向公司查询密码时，必须提供完整、准确的注册信息，否则公司有权本着为用户保密的原则不予告知。</span></p>     <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">3.4</span><span class=\"r s5\">用户的用户名和密码只能供用户本人使用，不得以任何形式转让或授权他人使用，如果发现同一帐号和密码在同一时间内被多人同时登陆使用，公司有权取消此帐号的用户资格，并不予任何赔偿或者退还任何服务费用。</span></p>     <p class=\"f14 grey1 mt25 clearfix\"><span class=\"b l\">4.</span><span class=\"r s4 b\">服务内容的版权</span></p>     <p class=\"grey2 f12 mt25 clearfix\">公司的软件产品均受版权保护，用户不能擅自复制、随意发布相关服务内容，不得根据服务内容，改造或创造与服务内容有关的派生产品，不得将公司提供的数据和信息内容向他人发布并收取不正当的费用。</p>     <p class=\"f14 grey1 mt25 clearfix\"><span class=\"b l\">5、</span><span class=\"r s4 b\">免责条款</span></p>     <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">5.1</span><span class=\"r s5\">在服务期内，公司将有权根据经营活动的需要，随时对软件产品的服务内容进行更新、增加或删除，且无需另行通知或取得用户的同意。</span></p>     <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">5.2</span><span class=\"r s5\">公司尽力提供完整、及时、准确的信息，但不对所提供信息的完整性、及时性、准确性承担任何责任。</span></p>     <p class=\"f12 grey2 mt25 clearfix\"><span class=\"l  b\">5.3</span><span class=\"r s5\">由于地震、台风、战争、罢工、政府行为、瘟疫、爆发性和流行性传染病或其他重大疫情、各方原因造成的火灾、基础电信网络中断造成的及其它各方不能预见并且对其发生后果不能预防或避免的不可抗力原因，致使相关服务中断，公司不承担由此产生的损失，但应及时通知服务中断原因，并积极加以解决。</span></p>');
INSERT INTO `yehnet_list_c` VALUES ('2', 'content', '<p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px; line-height: 24px;\">1</span><span style=\"margin: 0px; padding: 0px; font-size: 16px; line-height: 24px; font-family: 宋体;\">、基础条款：</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">1<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）经纪人身份认证：</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-family: 宋体;\"></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 24px;\"><span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">经纪人需年满</span>18<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">周岁，具有完全民事行为能力。经纪人须提供合法的身份证明，且需保证注册的个人信息的真实性，任何人均不得以虚假信息注册，否则将被取消推荐资格，并不再享受与此有关的一切权益。</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 24px;\">2<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）被推荐人身份认证：</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 24px;\">Ø&nbsp;&nbsp;<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">由经纪人推荐的被推荐人，不得是经纪人本人或其直系亲属（配偶、父母、子女、兄弟姐妹）。</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 24px;\">Ø&nbsp;&nbsp;<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">经成都万科房地产有限公司（以下简称“成都万科”）销售系统查询，被推荐人在推荐日60日前有到访万科项目的记录，则相应的推荐行为无效。</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 24px;\">3<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）信息宣传的真实性：</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 24px;\">Ø&nbsp;&nbsp;<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">经纪人需熟练掌握各项目资料、在售产品情况，并真实、准确地宣传项目相关销售信息，不得夸大、隐瞒、虚假或过度承诺；</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 24px;\">Ø&nbsp;&nbsp;<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">经纪人需实施真实的推荐行为，即与被推荐人进行过明确的推荐沟通，否则相应推荐行为无效。</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">4）此政策执行至新政策发布之前。</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">&nbsp;</p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px; line-height: 24px;\">2</span><span style=\"margin: 0px; padding: 0px; font-size: 16px; line-height: 24px; font-family: 宋体;\">、推荐有效期限：</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">1<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）实施推荐后，被推荐人须在被推荐后</span><span style=\"margin: 0px; padding: 0px;\">20</span><span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">天内到访指定项目，并登记个人基本信息；若被推荐人在</span><span style=\"margin: 0px; padding: 0px;\">20<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">天</span></span><span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">内未到访指定项目，则视为推荐无效。</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">2<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）实施推荐后，被推荐人到访项目后</span>30<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">天内未认购所推荐的项目，则视为推荐无效；</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-family: 宋体;\"><br style=\"margin: 0px; padding: 0px;\" /></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px; line-height: 24px;\">3</span><span style=\"margin: 0px; padding: 0px; font-size: 16px; line-height: 24px; font-family: 宋体;\">、推荐人的权利与义务：</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">1<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）推荐人需自行配备上网的所需设备，负担个人上网所支付的与此相关的电话、网络及其他费用；</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">2<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）推荐人必须提供本人真实、准确、最新及完整的资料，并凭其身份证、注册的电话号码作为佣金结算的凭证。若推荐人提供任何错误、不实、过时或不完整的资料，导致不能成功结算佣金，成都万科概不承担任何责任。若成都万科确认或有合理理由相信推荐人的资料错误、不实、过时或不完整，成都万科有权暂停或终止推荐人的账号，推荐人不再享有成都万科官方微信所提供的全部或部分服务；</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">3<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）推荐人注册的用户名不能侵犯任何第三方的合法权益。否则，成都万科有权暂停或终止该账号的使用；</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">4<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）推荐人可以通过成都万科的微信公众账号或者现场培训（需要预约），了解万科各项目的资料、在售产品情况；</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">5<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）推荐人须真实、准确对外宣传所推荐项目的优惠信息，若有任何不实宣传，概由推荐人自行承担。若因此给成都万科或所推荐项目的开发企业造成任何损失，均有推荐人承担全部责任。</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-family: 宋体;\"><br style=\"margin: 0px; padding: 0px;\" /></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px; line-height: 24px;\">4</span><span style=\"margin: 0px; padding: 0px; font-size: 16px; line-height: 24px; font-family: 宋体;\">、推荐人与推荐客户推荐关系的确立细则：</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px;\"><span style=\"margin: 0px; padding: 0px; line-height: 24px; font-family: 宋体;\">1）推荐人通过官方微信推荐有购买意向且未曾到访过项目的客户，客户必须为诚意客户，有意向项目。若客户被推荐后</span><span style=\"margin: 0px; padding: 0px; line-height: 24px;\">20<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">天</span><span style=\"margin: 0px; padding: 0px;\"><span style=\"margin: 0px; padding: 0px; color: rgb(227, 108, 10); font-family: 宋体;\"></span></span></span><span style=\"margin: 0px; padding: 0px; line-height: 24px; font-family: 宋体;\">内未到访指定项目，则视为无效推荐。</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px;\">2<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）成都万科根据万科销售系统“明源系统”的记录反馈推荐客户的身份是否成立。登记客户推荐日前</span>60<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">天内未到访过万科项目或被其他人提前通过任何渠道推荐过，则推荐关系成立；</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px;\">3<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）推荐人不能把自己或直系亲属推荐为诚意客户。</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px;\">4<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）推荐人可以选择带或者不带诚意客户到成都万科购房中心。</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px;\">5<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）推荐人不得恶意推荐诚意客户，恶意推荐包括但不限于提供虚假的电话号码、推荐完全没有购买意向的客户。一经确认，成都万科有权暂停或终止推荐人的账号，并暂停前期未结佣金的发放。</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">&nbsp;</p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px; line-height: 24px;\">5</span><span style=\"margin: 0px; padding: 0px; font-size: 16px; line-height: 24px; font-family: 宋体;\">、被推荐人成交以及结佣：</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">1<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）被推荐人到达成都万科购房中心后由成都万科安排专人接待，客户到场、成交、签约的过程，推荐人可以通过成都万科的微信平台查询。</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">2<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）被推荐人成功购买万科购房中心所售房源且签订《商品房买卖合同》，经纪人可获得房屋最终成交推荐奖励；</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">3<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）具体推荐奖励比例，以当期公示为准。成都万科有权根据市场情况，进行推荐奖励比例的合理调整。</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">4<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）推荐人因此获得的佣金，需要按照国家相关税收法律法规的规定缴纳个人所得税。</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">5<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）“佣金收益”通过转账的方式结算，不直接支付现金。</span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">&nbsp;</p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px; line-height: 24px;\">6</span><span style=\"margin: 0px; padding: 0px; font-size: 16px; line-height: 24px; font-family: 宋体;\">、奖励结算方式：</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px;\">1<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）推荐成功后，推荐人需到成都万科购房中心填写推荐奖励凭据，否则不享受相应的推荐奖励。</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px;\">2<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）结算条件：</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px;\">Ø&nbsp;&nbsp;<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">推荐客户至购房中心成交，而非其他项目现场；</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px;\">Ø&nbsp;&nbsp;<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">被推荐人认购万科项目并签订《商品房买卖合同》。</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\">&nbsp;</p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px; line-height: 24px;\">7</span><span style=\"margin: 0px; padding: 0px; font-size: 16px; line-height: 24px; font-family: 宋体;\">、推荐关系成立，但不计算收益的情况：</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px;\">1<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）推荐人推荐成交的客户，如在成交后发现直系亲属（父母、配偶、子女）曾经由成都万科购房中心销售人员接待并登记（以明源的登记记录为准，登记的时间在推荐人推荐的时间之前），则此单推荐不获得收益；</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px;\">2<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）诚意客户有多个电话号码，其中一个电话号码（不同于推荐平台确认的电话号码）在销售现场登记过（以明源的登记记录为准，电话作为首要的认证凭证，登记的时间在推荐人推荐的时间之前），则此单推荐不获得收益；</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px;\">3<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）拥有多个电话号码的客户，被不同的推荐人以不同的电话号码同时推荐成功，先确定推荐关系的推荐人获得</span>100%<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">佣金收益，后推荐的推荐人不能获得收益；</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 19.5px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px;\">4<span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）推荐关系确立，但客户未先到访万科购房中心而是到访各项目销售现场，则此单推荐不获得收益；</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 21.059999465942383px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px;\"><span style=\"margin: 0px; padding: 0px; font-family: Calibri, sans-serif;\">5</span><span style=\"margin: 0px; padding: 0px; font-family: 宋体;\">）诚意客户认购，但最终退房，推荐人不能获得此单佣金收益；</span></span></p><p style=\"margin: 0px; padding: 0px; font-family: arial; font-size: 13px; line-height: 21.059999465942383px;\"><span style=\"margin: 0px; padding: 0px; font-size: 16px; font-family: 宋体;\">6）被推荐人经核实已通过其他第三方渠道进行过推荐，包括但不限于万客会APP、中介公司转介、行销代访等。</span></p><div><span style=\"margin: 0px; padding: 0px; font-size: 16px; font-family: 宋体;\"><br /></span></div>');
INSERT INTO `yehnet_list_c` VALUES ('3', 'content', '推荐流程');
INSERT INTO `yehnet_list_c` VALUES ('4', 'content', '万科五十峰万科五十峰万科五十峰');
INSERT INTO `yehnet_list_c` VALUES ('5', 'content', '万科中央公园');
INSERT INTO `yehnet_list_c` VALUES ('6', 'content', '111');
INSERT INTO `yehnet_list_c` VALUES ('7', 'content', '2222');
INSERT INTO `yehnet_list_c` VALUES ('8', 'content', '333');
INSERT INTO `yehnet_list_c` VALUES ('9', 'content', '');

-- ----------------------------
-- Table structure for yehnet_module
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_module`;
CREATE TABLE `yehnet_module` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '组ID',
  `langid` varchar(5) NOT NULL DEFAULT 'zh' COMMENT '语言ID，默认是zh',
  `identifier` varchar(32) NOT NULL DEFAULT '0' COMMENT '标识符',
  `title` varchar(100) NOT NULL COMMENT '名称',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `ctrl_init` varchar(100) NOT NULL COMMENT '执行文件，不同模块可能执行相同的文件，使用标识符区分',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不使用1使用',
  `taxis` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序值越小越往靠，最小为0',
  `if_system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1系统模块2自定义添加模块',
  `if_cate` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否启用分类功能，1使用0不使用',
  `if_biz` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否支持电子商务，0否1是',
  `if_propety` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不支持属性，1支持',
  `if_hits` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不持点击1支持',
  `popedom` varchar(255) NOT NULL COMMENT '权限ID，多个权限ID用英文逗号隔开',
  `if_thumb` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1支持缩略图0不支持',
  `if_thumb_m` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1必填0非必填',
  `if_point` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不支持点数1支持点数',
  `if_url_m` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不支持自定义网址，1支持，2支持且必填',
  `inpic` varchar(100) NOT NULL COMMENT '前台默认图片关联',
  `insearch` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支持前台搜索',
  `if_content` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不支持读取内容1读取内容及管理员回复',
  `if_email` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1邮件通知0不通知',
  `link_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '联动ID，0为不使用联动搜索',
  `search_id` varchar(30) NOT NULL COMMENT '联动搜索的字段名',
  `psize` tinyint(3) unsigned NOT NULL DEFAULT '30' COMMENT '默认分页数量',
  `if_subtitle` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否启用副标题0否1是',
  `ico` varchar(255) NOT NULL COMMENT '图标',
  `small_pic` varchar(255) NOT NULL COMMENT '小图',
  `medium_pic` varchar(255) NOT NULL COMMENT '中图',
  `big_pic` varchar(255) NOT NULL COMMENT '大图',
  `tplset` enum('list','pic') NOT NULL DEFAULT 'list' COMMENT 'list列表，pic图文',
  `title_nickname` varchar(50) NOT NULL COMMENT '主题别称',
  `subtitle_nickname` varchar(50) NOT NULL COMMENT '副标题别称',
  `sign_nickname` varchar(50) NOT NULL COMMENT '标识串别称',
  `if_sign_m` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '标识串是否必填',
  `if_ext` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '可选扩展1使用0不使用',
  `if_des` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '简短描述1允许0不使用',
  `if_list` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1支持0不支持',
  `if_msg` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1支持0不支持',
  `layout` varchar(255) NOT NULL COMMENT '后台布局设置',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_module
-- ----------------------------
INSERT INTO `yehnet_module` VALUES ('16', '1', 'zh', 'module', '模块管理', '', 'ctrl', '0', '16', '1', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');
INSERT INTO `yehnet_module` VALUES ('8', '1', 'zh', 'setting', '网站信息', '', 'setting', '1', '8', '1', '0', '0', '0', '0', '8', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');
INSERT INTO `yehnet_module` VALUES ('13', '1', 'zh', 'mypass', '修改密码', '', 'mypass', '1', '13', '1', '0', '0', '0', '0', '8', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');
INSERT INTO `yehnet_module` VALUES ('3', '3', 'zh', 'projects', '项目管理', '', 'list', '1', '3', '0', '0', '0', '0', '1', '7,3,4,5,6', '1', '1', '0', '1', '', '1', '1', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '0', '1', '1', 'hits');
INSERT INTO `yehnet_module` VALUES ('7', '4', 'zh', 'customer', '推荐人管理', '', 'customer', '1', '7', '0', '0', '0', '0', '0', '7,3', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');
INSERT INTO `yehnet_module` VALUES ('14', '1', 'zh', 'tpl', '前台风格', '', 'tpl', '0', '14', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');
INSERT INTO `yehnet_module` VALUES ('15', '1', 'zh', 'lang', '语言设置', '', 'lang', '0', '15', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');
INSERT INTO `yehnet_module` VALUES ('6', '6', 'zh', 'user', '合伙人列表', '', 'user', '1', '6', '0', '0', '0', '0', '0', '7,3,4,5,6', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');
INSERT INTO `yehnet_module` VALUES ('10', '1', 'zh', 'admin', '超级管理', '', 'admin', '1', '10', '0', '0', '0', '0', '0', '7,3,4,5,6', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');
INSERT INTO `yehnet_module` VALUES ('9', '6', 'zh', 'commission', '佣金明细', '', 'commission', '1', '9', '0', '0', '0', '0', '0', '7,3,4,5,6', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');
INSERT INTO `yehnet_module` VALUES ('2', '3', 'zh', 'onepage', '单页管理', '', 'list', '1', '2', '0', '0', '0', '0', '0', '7,3,4,5,6', '0', '0', '0', '0', 'thumb', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '0', '0', '0', '1', '');
INSERT INTO `yehnet_module` VALUES ('11', '1', 'zh', 'gd', '图片设置', '', 'gd', '1', '11', '0', '0', '0', '0', '0', '7,4', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');
INSERT INTO `yehnet_module` VALUES ('4', '3', 'zh', 'files', '附件管理', '', 'files', '1', '4', '0', '0', '0', '0', '0', '7,6', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');
INSERT INTO `yehnet_module` VALUES ('1', '0', 'zh', 'home', '后台首页', '', 'home', '1', '1', '0', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');
INSERT INTO `yehnet_module` VALUES ('12', '1', 'zh', 'phpoksql', '数据备份', '', 'phpoksql', '1', '12', '0', '0', '0', '0', '0', '7,8', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');
INSERT INTO `yehnet_module` VALUES ('5', '6', 'zh', 'usergroup', '合伙人组别', '', 'usergroup', '1', '5', '0', '0', '0', '0', '0', '7,3,4,6', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');
INSERT INTO `yehnet_module` VALUES ('17', '6', 'zh', 'excel_user', '导出合伙人', '', 'excel_user', '1', '17', '0', '0', '0', '0', '0', '7', '0', '0', '0', '0', '', '0', '0', '0', '0', '', '30', '0', '', '', '', '', 'list', '', '', '', '0', '1', '1', '1', '1', '');

-- ----------------------------
-- Table structure for yehnet_module_fields
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_module_fields`;
CREATE TABLE `yehnet_module_fields` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `module_id` mediumint(8) unsigned NOT NULL COMMENT '模块ID',
  `identifier` varchar(32) NOT NULL COMMENT '标识符',
  `title` varchar(100) NOT NULL COMMENT '主题',
  `if_post` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1支持会员0不支持',
  `if_guest` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1支持游客0不支持',
  `sub_left` varchar(60) NOT NULL COMMENT '左侧主题',
  `sub_note` varchar(120) NOT NULL COMMENT '右侧备注信息',
  `input` varchar(50) NOT NULL DEFAULT 'text' COMMENT '表单类型',
  `width` varchar(20) NOT NULL COMMENT '宽度',
  `height` varchar(20) NOT NULL COMMENT '高度',
  `default_val` varchar(50) NOT NULL COMMENT '默认值',
  `list_val` varchar(255) NOT NULL COMMENT '值列表',
  `link_id` int(10) NOT NULL DEFAULT '0' COMMENT '联动组ID',
  `taxis` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '自定义排序，值越小越往前靠',
  `if_must` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1必填0非必填',
  `if_html` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1支持HTML，0不支持',
  `error_note` varchar(255) NOT NULL COMMENT '错误时的提示',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1启用0禁用',
  `if_system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1系统字段，0用户配置字段',
  `tbl` enum('ext','c') NOT NULL COMMENT 'ext指长度不大于255的表中，c指长度大于255的数据',
  `show_html` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不显示源码1显示源码',
  `if_js` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1支持0不支持',
  `if_search` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许搜索',
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_module_fields
-- ----------------------------
INSERT INTO `yehnet_module_fields` VALUES ('1', '2', 'content', '内容', '0', '0', '', '', 'edit', '650px', '400px', '', '', '0', '1', '1', '1', '请输入内容', '1', '0', 'c', '0', '1', '0');
INSERT INTO `yehnet_module_fields` VALUES ('2', '3', 'content', '内容', '0', '0', '', '', 'edit', '650px', '400px', '', '', '0', '2', '0', '1', '请输入内容', '1', '0', 'c', '0', '1', '0');

-- ----------------------------
-- Table structure for yehnet_module_group
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_module_group`;
CREATE TABLE `yehnet_module_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `langid` varchar(32) NOT NULL DEFAULT 'zh' COMMENT '语言编号，如zh,en等',
  `title` varchar(100) NOT NULL COMMENT '组名称',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不使用1使用',
  `taxis` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '值越小越往靠，最小为0',
  `js_function` varchar(100) NOT NULL DEFAULT '' COMMENT 'JS控制器，为空使用系统自动生成',
  `if_system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1系统0自定义',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_module_group
-- ----------------------------
INSERT INTO `yehnet_module_group` VALUES ('1', 'zh', '核心配置', '1', '4', '', '1');
INSERT INTO `yehnet_module_group` VALUES ('2', 'zh', '退出', '1', '7', 'logout', '1');
INSERT INTO `yehnet_module_group` VALUES ('3', 'zh', '内容管理', '1', '1', '', '1');
INSERT INTO `yehnet_module_group` VALUES ('4', 'zh', '客户管理', '1', '3', '', '1');
INSERT INTO `yehnet_module_group` VALUES ('5', 'zh', '网站首页', '1', '6', 'gohome', '1');
INSERT INTO `yehnet_module_group` VALUES ('6', 'zh', '房产合伙人', '1', '2', '', '0');
INSERT INTO `yehnet_module_group` VALUES ('7', 'zh', '清空缓存', '1', '5', 'clear_cache', '1');

-- ----------------------------
-- Table structure for yehnet_session
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_session`;
CREATE TABLE `yehnet_session` (
  `id` varchar(32) NOT NULL COMMENT 'session_id',
  `data` text NOT NULL COMMENT 'session 内容',
  `lasttime` int(10) unsigned NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_session
-- ----------------------------
INSERT INTO `yehnet_session` VALUES ('rrc4sidlj4ttu1ichslmelk180', 'sys_lang_id|s:2:\"zh\";user_id|i:32;user_name|s:6:\"马良\";group_id|s:1:\"2\";user_rs|a:9:{s:8:\"username\";s:6:\"马良\";s:5:\"phone\";s:11:\"15386657506\";s:3:\"job\";s:4:\"HZHB\";s:7:\"company\";s:6:\"趁机\";s:4:\"pass\";s:32:\"1c899bdf3598fef830572edbf6962b29\";s:7:\"regdate\";i:1411782248;s:6:\"status\";i:1;s:7:\"groupid\";s:1:\"2\";s:2:\"id\";i:32;}', '1411783673');
INSERT INTO `yehnet_session` VALUES ('ptvjvrqflgr3feqcd0883ccl20', 'sys_lang_id|s:2:\"zh\";', '1411774080');
INSERT INTO `yehnet_session` VALUES ('fggvun3eatnqe0gc3auc1lptk3', 'sys_lang_id|s:2:\"zh\";', '1411774104');
INSERT INTO `yehnet_session` VALUES ('dl6kr1r82slgb1najl7secj410', 'sys_lang_id|s:2:\"zh\";user_id|i:31;user_name|s:6:\"马克\";group_id|s:1:\"2\";user_rs|a:9:{s:8:\"username\";s:6:\"马克\";s:5:\"phone\";s:11:\"13682550311\";s:3:\"job\";s:4:\"GSYG\";s:7:\"company\";b:0;s:4:\"pass\";s:32:\"88309b120de716122f0f00dd74aff9dd\";s:7:\"regdate\";i:1411774078;s:6:\"status\";i:1;s:7:\"groupid\";s:1:\"2\";s:2:\"id\";i:31;}', '1411774211');
INSERT INTO `yehnet_session` VALUES ('plgaig66ml6150e1d3hm3bugk6', 'sys_lang_id|s:2:\"zh\";admin_id|s:1:\"1\";admin_name|s:5:\"admin\";admin_realname|s:5:\"admin\";admin_md5|s:32:\"abcae11b2503110d4fb4b61d5b85137d\";', '1411783647');
INSERT INTO `yehnet_session` VALUES ('mqqolfbht58s7jls0v9k4mhq04', 'sys_lang_id|s:2:\"zh\";', '1411783692');
INSERT INTO `yehnet_session` VALUES ('ggqbq2snvsaeajmll06oik3ag1', 'sys_lang_id|s:2:\"zh\";admin_id|s:1:\"1\";admin_name|s:5:\"admin\";admin_realname|s:5:\"admin\";admin_md5|s:32:\"abcae11b2503110d4fb4b61d5b85137d\";', '1411783630');
INSERT INTO `yehnet_session` VALUES ('es7cbd6t7bmv2qkgdss3tv2c80', 'sys_lang_id|s:2:\"zh\";', '1411783401');
INSERT INTO `yehnet_session` VALUES ('bnfmq23i92h5g226qhir5dpah4', 'sys_lang_id|s:2:\"zh\";admin_id|s:1:\"1\";admin_name|s:5:\"admin\";admin_realname|s:5:\"admin\";admin_md5|s:32:\"4610d67b75d74f03fc028b1af34b602f\";', '1411783716');
INSERT INTO `yehnet_session` VALUES ('mnkoadhfi2qrhpbcoggkhun1f5', 'sys_lang_id|s:2:\"zh\";', '1411782295');
INSERT INTO `yehnet_session` VALUES ('6kf3t7okg1eigcfn8k7g0n3sj2', 'sys_lang_id|s:2:\"zh\";', '1411782339');
INSERT INTO `yehnet_session` VALUES ('fnkg9hhaolgbtfa2h8qls0t297', 'sys_lang_id|s:2:\"zh\";', '1411782423');

-- ----------------------------
-- Table structure for yehnet_tpl
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_tpl`;
CREATE TABLE `yehnet_tpl` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID号',
  `langid` varchar(5) NOT NULL DEFAULT 'zh' COMMENT '语言ID，默认是zh',
  `title` varchar(100) NOT NULL COMMENT '名称',
  `folder` varchar(50) NOT NULL COMMENT '文件夹',
  `ext` varchar(10) NOT NULL DEFAULT 'html' COMMENT '模板后缀',
  `autoimg` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否自动解析图片地址',
  `ifdefault` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认',
  `ifsystem` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1系统模板0用户模板',
  `taxis` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1正在使用0未使用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_tpl
-- ----------------------------
INSERT INTO `yehnet_tpl` VALUES ('1', 'zh', '前台默认风格', 'www', 'html', '1', '1', '0', '1', '1');

-- ----------------------------
-- Table structure for yehnet_upfiles
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_upfiles`;
CREATE TABLE `yehnet_upfiles` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '图片ID',
  `title` varchar(100) NOT NULL COMMENT '标题',
  `filename` varchar(255) NOT NULL COMMENT '图片路径，基于网站根目录的相对路径',
  `thumb` varchar(255) NOT NULL COMMENT '缩略图路径，基于网站根目录的相对路径',
  `postdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `ftype` varchar(10) NOT NULL COMMENT '附件类型',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID号，0表示管理员上传',
  `flv_pic` varchar(255) NOT NULL COMMENT 'FLV封面图片',
  `sessid` varchar(50) NOT NULL COMMENT '游客上传标识串',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_upfiles
-- ----------------------------
INSERT INTO `yehnet_upfiles` VALUES ('1', '九墅', 'upfiles/201409/20/a7581617757c4d5d.jpg', 'upfiles/201409/20/thumb1.jpg', '1411204311', 'jpg', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('2', '公园五号', 'upfiles/201409/20/d0665f4ab2638e28.jpg', 'upfiles/201409/20/thumb2.jpg', '1411204357', 'jpg', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('3', '龙山', 'upfiles/201409/20/e4ee64d8c040f46a.jpg', 'upfiles/201409/20/thumb3.jpg', '1411204387', 'jpg', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('4', '天逸', 'upfiles/201409/20/6f2bbc69a1ec1889.jpg', 'upfiles/201409/20/thumb4.jpg', '1411204414', 'jpg', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('5', '金域缇香', 'upfiles/201409/20/883a83f6fdb2137c.jpg', 'upfiles/201409/20/thumb5.jpg', '1411204441', 'jpg', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('6', '金色乐府', 'upfiles/201409/20/7b95e0b8b93dec48.jpg', 'upfiles/201409/20/thumb6.jpg', '1411204469', 'jpg', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('7', '弹出提示', 'upfiles/201409/21/7e8b99a1eb4d8907.png', 'upfiles/201409/21/thumb7.png', '1411270558', 'png', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('8', '微信分享图标', 'upfiles/201409/21/e0b6a40a8fd833a6.jpg', 'upfiles/201409/21/thumb8.jpg', '1411274455', 'jpg', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('9', '53d5ed7d93bce.jpg', 'upfiles/201409/23/d1716a42ce902731.jpg', 'upfiles/201409/23/thumb9.jpg', '1411472792', 'jpg', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('10', '01.jpg', 'upfiles/201409/24/e54137ce73ba1542.jpg', 'upfiles/201409/24/thumb10.jpg', '1411525769', 'jpg', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('11', '02.jpg', 'upfiles/201409/24/b0ee90f5bff26dd5.jpg', 'upfiles/201409/24/thumb11.jpg', '1411525779', 'jpg', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('12', '03.jpg', 'upfiles/201409/24/7e729ad7912a8f58.jpg', 'upfiles/201409/24/thumb12.jpg', '1411525789', 'jpg', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('13', '04.jpg', 'upfiles/201409/24/a66c663f285cd820.jpg', 'upfiles/201409/24/thumb13.jpg', '1411525800', 'jpg', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('14', '05.jpg', 'upfiles/201409/24/dde80ee0b1fca7d0.jpg', 'upfiles/201409/24/thumb14.jpg', '1411525811', 'jpg', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('15', '06.jpg', 'upfiles/201409/24/1303704809fb8827.jpg', 'upfiles/201409/24/thumb15.jpg', '1411525819', 'jpg', '0', '', '');
INSERT INTO `yehnet_upfiles` VALUES ('16', '07.jpg', 'upfiles/201409/24/ea9014f4a3fa0ca4.jpg', 'upfiles/201409/24/thumb16.jpg', '1411525828', 'jpg', '0', '', '');

-- ----------------------------
-- Table structure for yehnet_upfiles_gd
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_upfiles_gd`;
CREATE TABLE `yehnet_upfiles_gd` (
  `pid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '图片ID，对应upfiles里的ID',
  `gdtype` varchar(100) NOT NULL COMMENT '图片类型',
  `filename` varchar(255) NOT NULL COMMENT '图片地址（生成类型的图片地址）',
  PRIMARY KEY (`pid`,`gdtype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_upfiles_gd
-- ----------------------------
INSERT INTO `yehnet_upfiles_gd` VALUES ('1', 'thumb', 'upfiles/201409/20/thumb_1.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('1', 'big', 'upfiles/201409/20/big_1.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('1', 'mid', 'upfiles/201409/20/mid_1.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('2', 'thumb', 'upfiles/201409/20/thumb_2.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('2', 'big', 'upfiles/201409/20/big_2.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('2', 'mid', 'upfiles/201409/20/mid_2.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('3', 'thumb', 'upfiles/201409/20/thumb_3.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('3', 'big', 'upfiles/201409/20/big_3.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('3', 'mid', 'upfiles/201409/20/mid_3.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('4', 'thumb', 'upfiles/201409/20/thumb_4.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('4', 'big', 'upfiles/201409/20/big_4.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('4', 'mid', 'upfiles/201409/20/mid_4.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('5', 'thumb', 'upfiles/201409/20/thumb_5.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('5', 'big', 'upfiles/201409/20/big_5.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('5', 'mid', 'upfiles/201409/20/mid_5.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('6', 'thumb', 'upfiles/201409/20/thumb_6.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('6', 'big', 'upfiles/201409/20/big_6.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('6', 'mid', 'upfiles/201409/20/mid_6.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('7', 'thumb', 'upfiles/201409/21/thumb_7.png');
INSERT INTO `yehnet_upfiles_gd` VALUES ('7', 'big', 'upfiles/201409/21/big_7.png');
INSERT INTO `yehnet_upfiles_gd` VALUES ('7', 'mid', 'upfiles/201409/21/mid_7.png');
INSERT INTO `yehnet_upfiles_gd` VALUES ('8', 'thumb', 'upfiles/201409/21/thumb_8.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('8', 'big', 'upfiles/201409/21/big_8.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('8', 'mid', 'upfiles/201409/21/mid_8.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('9', 'thumb', 'upfiles/201409/23/thumb_9.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('9', 'big', 'upfiles/201409/23/big_9.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('9', 'mid', 'upfiles/201409/23/mid_9.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('10', 'thumb', 'upfiles/201409/24/thumb_10.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('10', 'big', 'upfiles/201409/24/big_10.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('10', 'mid', 'upfiles/201409/24/mid_10.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('11', 'thumb', 'upfiles/201409/24/thumb_11.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('11', 'big', 'upfiles/201409/24/big_11.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('11', 'mid', 'upfiles/201409/24/mid_11.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('12', 'thumb', 'upfiles/201409/24/thumb_12.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('12', 'big', 'upfiles/201409/24/big_12.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('12', 'mid', 'upfiles/201409/24/mid_12.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('13', 'thumb', 'upfiles/201409/24/thumb_13.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('13', 'big', 'upfiles/201409/24/big_13.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('13', 'mid', 'upfiles/201409/24/mid_13.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('14', 'thumb', 'upfiles/201409/24/thumb_14.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('14', 'big', 'upfiles/201409/24/big_14.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('14', 'mid', 'upfiles/201409/24/mid_14.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('15', 'thumb', 'upfiles/201409/24/thumb_15.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('15', 'big', 'upfiles/201409/24/big_15.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('15', 'mid', 'upfiles/201409/24/mid_15.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('16', 'thumb', 'upfiles/201409/24/thumb_16.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('16', 'big', 'upfiles/201409/24/big_16.jpg');
INSERT INTO `yehnet_upfiles_gd` VALUES ('16', 'mid', 'upfiles/201409/24/mid_16.jpg');

-- ----------------------------
-- Table structure for yehnet_user
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_user`;
CREATE TABLE `yehnet_user` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `groupid` mediumint(8) unsigned NOT NULL DEFAULT '1' COMMENT '会员组ID',
  `username` varchar(100) NOT NULL COMMENT '会员名称',
  `pass` varchar(50) NOT NULL COMMENT '密码',
  `phone` varchar(100) NOT NULL COMMENT '手机',
  `regdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态1已审核0未审核2锁定',
  `fxstatus` tinyint(1) NOT NULL,
  `job` varchar(150) NOT NULL,
  `company` varchar(20) NOT NULL,
  `thumb_id` int(10) unsigned NOT NULL COMMENT '个性头像ID',
  `bankAccount` varchar(100) NOT NULL,
  `cardCode` varchar(100) NOT NULL,
  `bankName` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_user
-- ----------------------------
INSERT INTO `yehnet_user` VALUES ('1', '3', '张飞龙', 'c12a8c843cdc55e043f1a434b668f887', '18599044720', '1411523785', '1', '1', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('2', '3', '陈景俊', '14e1b600b1fd579f47433b88e8d85291', '18299131849', '1411523963', '1', '1', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('3', '3', '王海锋', '62bdea827b3ca05feca9c78090d38bec', '15199065150', '1411524068', '1', '1', 'GSYG', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('4', '3', '张三丰', '14e1b600b1fd579f47433b88e8d85291', '15550986688', '1411525039', '1', '1', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('5', '3', '李轩', '84ec490fcbe9ff23f4f80395f1009166', '13579248094', '1411525081', '1', '1', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('6', '2', '熊峻', '9977d2b44b3496178a232470d632ef34', '18679389996', '1411529172', '1', '0', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('7', '2', '杨哲', '14e1b600b1fd579f47433b88e8d85291', '15832228855', '1411533994', '1', '0', 'GSYG', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('8', '2', '谭总', 'ac4feede3caf63720889fd058b0576e3', '18214580837', '1411545799', '1', '0', 'HZHB', '贝趣', '0', '中信', '55555551', '的额');
INSERT INTO `yehnet_user` VALUES ('9', '3', '花树彪', '14e1b600b1fd579f47433b88e8d85291', '15102229989', '1411546004', '1', '1', 'GSYG', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('10', '2', '彭凯', '224cf2b695a5e8ecaecfb9015161fa4b', '18507936588', '1411547640', '1', '1', 'GSYG', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('11', '2', '理解', '377af5367827c5cd8234fa4cf9690126', '15956122426', '1411549905', '1', '0', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('12', '2', '苏皓', '2c06b2ca3ec05554b7ff6a6c7bb11dcd', '15128225201', '1411550283', '1', '0', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('13', '2', '很咯', 'bacc26e115971587a943d03be08e9537', '18649805572', '1411553438', '1', '0', 'GSYG', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('14', '2', '张琳', '9977d2b44b3496178a232470d632ef34', '18720302922', '1411554758', '1', '0', 'GSYG', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('15', '2', '李晋博', '14e1b600b1fd579f47433b88e8d85291', '15151629590', '1411566121', '1', '0', 'ZJGS', '信中介', '0', '张睿', '4367422450687458745', '建设银行');
INSERT INTO `yehnet_user` VALUES ('16', '2', '饭否', '52e4e7fec4c2c9fe6606f8e0bb737eeb', '13645887522', '1411579306', '1', '0', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('17', '2', 'jjj', '7833974fe7f90cb2d2503745f88400f1', '15566663333', '1411615770', '1', '0', 'GSYG', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('18', '3', 'www', '14e1b600b1fd579f47433b88e8d85291', '13355556666', '1411617799', '1', '1', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('19', '2', '陈哥哥', 'b075d6cc91c64b2f76cfc8b9fb6064f5', '18610131816', '1411626569', '1', '0', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('20', '2', '张三', '14e1b600b1fd579f47433b88e8d85291', '13888888888', '1411628264', '1', '0', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('21', '2', '小胡', '14e1b600b1fd579f47433b88e8d85291', '18671449663', '1411632479', '1', '0', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('22', '2', '李四', '14e1b600b1fd579f47433b88e8d85291', '13564265879', '1411638086', '1', '0', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('23', '3', '张三', '14e1b600b1fd579f47433b88e8d85291', '15932228855', '1411651660', '1', '1', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('24', '3', '任飞洋', '91162bb438816af87127817a319f1875', '15538998177', '1411662480', '1', '1', 'GSYG', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('25', '2', '杨明明', '07f88cea06ac40351aa1d0b3ebd458dc', '15637972535', '1411698833', '1', '0', 'WXFS', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('26', '2', '组织者', '14e1b600b1fd579f47433b88e8d85291', '13290407820', '1411699900', '1', '0', 'GSYG', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('27', '3', '曲志高', '550e1bafe077ff0b0b67f4e32f29d751', '13733128085', '1411715276', '1', '1', 'GSYG', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('28', '2', 'feiji', '5a31bc294cd66ee4fa0d858ee069bb6c', '18352698475', '1411717586', '1', '0', 'GSYG', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('29', '3', '飞机', '14e1b600b1fd579f47433b88e8d85291', '18636936936', '1411717996', '1', '1', 'GSYG', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('30', '3', '蘑菇', '550e1bafe077ff0b0b67f4e32f29d751', '13525178218', '1411718383', '1', '1', 'GSYG', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('31', '2', '马克', '88309b120de716122f0f00dd74aff9dd', '13682550311', '1411774078', '1', '0', 'GSYG', '', '0', '', '', '');
INSERT INTO `yehnet_user` VALUES ('32', '2', '马良', '1c899bdf3598fef830572edbf6962b29', '15386657506', '1411782248', '1', '0', 'HZHB', '趁机', '0', '', '', '');

-- ----------------------------
-- Table structure for yehnet_user_group
-- ----------------------------
DROP TABLE IF EXISTS `yehnet_user_group`;
CREATE TABLE `yehnet_user_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '会员组ID',
  `group_type` enum('user','guest') NOT NULL DEFAULT 'user' COMMENT '用户组类型',
  `title` varchar(100) NOT NULL COMMENT '组名称',
  `popedom_post` text NOT NULL COMMENT '发布权限',
  `popedom_reply` text NOT NULL COMMENT '回复权限',
  `popedom_read` text NOT NULL COMMENT '阅读权限，默认为all',
  `post_cert` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发布0需要验证1免验证',
  `reply_cert` tinyint(1) NOT NULL DEFAULT '0' COMMENT '回复0需要验证1免验证',
  `ifsystem` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否系统组0否1是',
  `ifdefault` tinyint(1) NOT NULL DEFAULT '0' COMMENT '会员注册后默认选择的组',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yehnet_user_group
-- ----------------------------
INSERT INTO `yehnet_user_group` VALUES ('1', 'guest', '游客', '', '', 'all', '0', '0', '1', '1');
INSERT INTO `yehnet_user_group` VALUES ('2', 'user', '初级合伙人', '', '', 'all', '1', '0', '1', '1');
INSERT INTO `yehnet_user_group` VALUES ('3', 'user', '金牌合伙人', '', '', '', '0', '0', '0', '0');
