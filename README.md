## 目录结构
<pre>
app/ #app打包目录
appsrc/ #app源码目录
doc/ #文档
h5/ #移动页面，广告营销等
server/ #服务端
测试测试
</pre>

## 接口约定
* 返回成功  
    <pre>
    {
      "status": 200,
      "data": {
        "id": 1
      }
    }
    </pre>

* 返回失败  		
    <pre>
    {
      "status": "非200",
      "data": "对不起，金币不足"
    }
    </pre>		

* status非200都表示预期失败，有几个特殊值有特定的作用：  
  * 501：未登录、或登录过期  





















## 数据结构
* article 文章  
    * 字典	
    <pre>
      CREATE TABLE `article` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态：1正常 2关闭',
          `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布者',
          `title` varchar(250) NOT NULL DEFAULT '' COMMENT '标题',
          `thumb` varchar(200) NOT NULL DEFAULT '' COMMENT '缩略图',
          `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
          `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
          `free_content` varchar(5000) NOT NULL DEFAULT '' COMMENT '免费内容',
          `charge_content` varchar(5000) NOT NULL DEFAULT '' COMMENT '收费内容',
          `charge_jinbi` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收费金币',
          `view_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '阅读次数',
          `close_msg` varchar(200) NOT NULL DEFAULT '' COMMENT '关闭的原因',
          PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    </pre>

- 附加缓存结构		
<pre>
charge_count 付费次数,
charge_total 付费价值金币统计,
reply_count 回复次数,
like_count 点赞次数,
unlink_count 点差次数,
gift_count 送礼物次数,
gift_total 礼物价值金币统计,
complaint_count 投诉次数,
</pre>

### article_topic 文章话题

- 字典		
<pre>
CREATE TABLE `article_toptic` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`name` varchar(50) NOT NULL DEFAULT '' COMMENT '主题名称',
		`pinyin` varchar(100) NOT NULL DEFAULT '' COMMENT '主题的拼音',
		`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态：1正常 2关闭',
		PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
</pre>

- 附加缓存结构		
<pre>
article_count 关联的文章数量
</pre>

### charge 金币消费纪录

- 字典		
<pre>
CREATE TABLE `charge` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`article_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章',
		`consumer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '消费者',
		`publisher_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布者',
		`jinbi` float unsigned NOT NULL DEFAULT '0' COMMENT '消费金币',
		`jinbi_system` float unsigned NOT NULL DEFAULT '0' COMMENT '系统扣除金币',
		`jinbi_publisher` float unsigned NOT NULL DEFAULT '0' COMMENT '发布者获得金币',
		`createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '消费时间',
		PRIMARY KEY (`id`),
		KEY `article_id` (`article_id`),
		KEY `consumer_id` (`consumer_id`),
		KEY `publisher_id` (`publisher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
</pre>

### drawmoney 提现	
	
- 字典		
<pre>
CREATE TABLE `drawmoney` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '提现申请人',
		`jinyuan` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '提现金元宝',
		`money_system` float unsigned NOT NULL DEFAULT '0' COMMENT '系统扣除金额',
		`money_user` float unsigned NOT NULL DEFAULT '0' COMMENT '用户得到金额',
		`createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
		`finishtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '完成时间',
		`step` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '提现步骤 1:申请中,2:提现成功,3:提现失败',
		`msg` varchar(200) NOT NULL DEFAULT '' COMMENT '系统备注',
		`pay_account` varchar(100) NOT NULL DEFAULT '' COMMENT '提现目标账号(微信/支付宝)',
		PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
</pre>

### recharge 充值记录		

- 字典		
<pre>
CREATE TABLE `recharge` (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`money` float unsigned NOT NULL DEFAULT '0' COMMENT '充值金额',
		`createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单创建时间',
		`finishtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单支付成功时间',
		`sn` varchar(50) NOT NULL DEFAULT '' COMMENT '支付平台编号',
		`uid` int(11) NOT NULL DEFAULT '0' COMMENT '订单用户',
		`step` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '步骤 1:未支付,2:支付成功,3:支付失败',
		`platform` char(10) NOT NULL DEFAULT '' COMMENT '支付平台 wechat,alipay',
		PRIMARY KEY (`id`),
		KEY `uid` (`uid`) USING BTREE,
		KEY `sn` (`sn`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT
</pre>

### syscfg 系统配置

- 字典		
<pre>
CREATE TABLE `syscfg` (
		`key` char(20) NOT NULL DEFAULT '' COMMENT '配置的索引',
		`value` varchar(250) NOT NULL DEFAULT '' COMMENT '值',
		PRIMARY KEY (`key`),
		UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
</pre>

### user 用户	
	
- 字典		
<pre>
CREATE TABLE `user` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`username` varchar(50) NOT NULL DEFAULT '' COMMENT '帐号',
		`password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
		`wechatId` char(50) NOT NULL DEFAULT '' COMMENT '微信openId',
		`nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
		`mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号码',
		`face` varchar(200) NOT NULL DEFAULT '' COMMENT '头像图片,如果是wechat则每2小时读微信头像',
		`sex` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别 1男2女3其他',
		`birthday` date NOT NULL COMMENT '出生',
		`regtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
		`logintime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录时间',
		`info` varchar(5000) NOT NULL DEFAULT '' COMMENT '详细信息json:注册时间、注册ip、登录时间、登录ip、国家省市区、教育、收入等信息',
		`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '帐号状态:1正常2禁用',
		`jinbi` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币',
		`jinyuan` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '元宝',
		`jifen` int(11) NOT NULL DEFAULT '0' COMMENT '积分',
		`xinyong` int(11) NOT NULL DEFAULT '0' COMMENT '信用',
		PRIMARY KEY (`id`),
		UNIQUE KEY `wechatId` (`wechatId`),
		UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
</pre>



				






