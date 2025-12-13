CREATE TABLE IF NOT EXISTS `__PREFIX__pay_recharge_order` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '交易ID',
  `orderid` varchar(50) NOT NULL COMMENT '订单ID',
  `user_id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `amount` double(10,2) unsigned DEFAULT '0.00' COMMENT '订单金额',
  `payamount` double(10,2) unsigned DEFAULT '0.00' COMMENT '支付金额',
  `paytype` varchar(50) DEFAULT NULL COMMENT '支付类型',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT '操作IP',
  `useragent` varchar(255) DEFAULT NULL COMMENT 'UserAgent',
  `pay_time` int(10) unsigned DEFAULT NULL COMMENT '支付时间',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '添加时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  `status` enum('succ','failed','error','cancel','unpay') NOT NULL DEFAULT 'unpay' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT='充值表';