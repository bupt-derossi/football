create table games (
	`id` bigint auto_increment not null comment '自增id',
	`number` varchar(5) not null default '' comment '当天比赛序号001',
	`home` varchar(20) not null default '' comment '主队',
	`visit` varchar(20) not null default '' comment '客队',
	`dt` timestamp not null DEFAULT CURRENT_TIMESTAMP comment '比赛日期',
	primary key (`id`),
	key `idx_dt` (`dt`)
)engine=innodb DEFAULT CHARSET=utf8 COMMENT='create by yuhualong 20170703，比赛表';

create  table jc_real_time_data (
	`id` bigint auto_increment comment '自增id',
	`game_id` bigint not null default 0 comment '比赛id,games表',
	`vict_odds` decimal(2,2) not null default 0 comment '竞彩主胜赔率',
	`draw_odds` decimal(2,2) not null default 0 comment '竞彩平局赔率',
	`fail_odds` decimal(2,2) not null default 0 comment '竞彩主负赔率',
	`let_vict_odds` decimal(2,2) not null default 0 comment '让球主胜',
	`let_draw_odds` decimal(2,2) not null default 0 comment '让球平局',
	`let_fail_odds` decimal(2,2) not null default 0 comment '让球主负',
	`bet_vict` varchar(20) not null default 0 comment '主胜投注量',
	`bet_draw` varchar(20) not null default 0 comment '平局投注量',
	`bet_fail` varchar(20) not null default 0 comment '主负投注量',
	`profit_vict` varchar(20) not null default 0 comment '主胜盈利',
	`profit_draw` varchar(20) not null default 0 comment '平局盈利',
	`profit_fail` varchar(20) not null default 0 comment '主负盈利',
	`create_time` timestamp not null DEFAULT CURRENT_TIMESTAMP comment '抓取时间',
	primary key (`id`),
	key `idx_gm` (`game_id`),
	key `idx_os_ct` (`vict_odds`,`create_time`)
)engine=innodb DEFAULT CHARSET=utf8 COMMENT='create by yuhualong 20170703，竞彩赔率投注变化基本表－按月分表';


create  table bf_real_time_data (
	`id` bigint auto_increment comment '自增id',
	`game_id` bigint not null default 0 comment '比赛id,games表',
	`vict_odds` decimal(2,2) not null default 0 comment '主胜赔率',
	`draw_odds` decimal(2,2) not null default 0 comment '平局赔率',
	`fail_odds` decimal(2,2) not null default 0 comment '主负赔率',
	`bet_vict` varchar(20) not null default 0 comment '主胜投注量',
	`bet_draw` varchar(20) not null default 0 comment '平局投注量',
	`bet_fail` varchar(20) not null default 0 comment '主负投注量',
	`profit_vict` varchar(20) not null default 0 comment '主胜盈利',
	`profit_draw` varchar(20) not null default 0 comment '平局盈利',
	`profit_fail` varchar(20) not null default 0 comment '主负盈利',
	`create_time` timestamp not null DEFAULT CURRENT_TIMESTAMP comment '抓取时间',
	primary key (`id`),
	key `idx_gm` (`game_id`),
	key `idx_os_ct` (`vict_odds`,`create_time`)
)engine=innodb DEFAULT CHARSET=utf8 COMMENT='create by yuhualong 20170703，必发赔率投注变化基本表－按月分表';

DROP TABLE football_games;
CREATE TABLE football_games (
  `id` bigint auto_increment comment '自增id',
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP comment '比赛时间',
  `number` VARCHAR (10) not null default '0' comment '比赛编号,001',
  `type` VARCHAR (10) not null default '' comment '比赛类型,欧冠',
  `host` VARCHAR (10) not null default '' comment '主队',
  `guest` VARCHAR (10) NOT NULL DEFAULT '' comment '客队',
  `rq` SMALLINT (2) not NULL default '0' comment '让球',
  `result` SMALLINT (2) NOT NULL DEFAULT -1 comment '比赛结果310',
  `r_result` SMALLINT (2) NOT NULL DEFAULT -1 comment '让球结果310',
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_dt_nb` (`date`,`number`)
)engine=innodb DEFAULT CHARSET=utf8 COMMENT='created by dalong 20171210 比赛基本信息表';

drop TABLE odds_2017;
CREATE TABLE odds_2017 (
  `id` bigint auto_increment comment '自增id',
  `game_id` bigint not null DEFAULT 0 comment 'football_games id',
  `jc_s` DECIMAL (10,2) not null DEFAULT 0 comment '竞彩胜',
  `jc_p` DECIMAL (10,2) not null DEFAULT 0 comment '竞彩平',
  `jc_f` DECIMAL (10,2) not null DEFAULT 0 comment '竞彩负',
  `jc_rs` DECIMAL (10,2) not null DEFAULT 0 comment '竞彩让胜',
  `jc_rp` DECIMAL (10,2) not null DEFAULT 0 comment '竞彩让平',
  `jc_rf` DECIMAL (10,2) not null DEFAULT 0 comment '竞彩让负',
  `wl_s` DECIMAL (10,2) not null DEFAULT 0 comment '威廉胜',
  `wl_p` DECIMAL (10,2) not null DEFAULT 0 comment '威廉平',
  `wl_f` DECIMAL (10,2) not null DEFAULT 0 comment '威廉负',
  `am_s` DECIMAL (10,2) not null DEFAULT 0 comment '澳门胜',
  `am_p` DECIMAL (10,2) not null DEFAULT 0 comment '澳门平',
  `am_f` DECIMAL (10,2) not null DEFAULT 0 comment '澳门负',
  `mh_s` DECIMAL (10,2) not null DEFAULT 0 comment '香港马会胜',
  `mh_p` DECIMAL (10,2) not null DEFAULT 0 comment '马会平',
  `mh_f` DECIMAL (10,2) not null DEFAULT 0 comment '马会负',
  `bet_s` DECIMAL (10,2) not null DEFAULT 0 comment 'bet365胜',
  `bet_p` DECIMAL (10,2) not null DEFAULT 0 comment 'bet平',
  `bet_f` DECIMAL (10,2) not null DEFAULT 0 comment 'bet负',
  `create_dt` TIMESTAMP not NULL DEFAULT CURRENT_TIMESTAMP comment '创建日期',
  PRIMARY KEY (`id`),
  KEY `idx_gi` (`game_id`)
)engine=innodb DEFAULT CHARSET=utf8 COMMENT='created by dalong 20171210 比赛欧赔赔率变化表';

CREATE TABLE asian_odds_2017 (
  `id` bigint auto_increment comment '自增id',
  `game_id` bigint not null DEFAULT 0 comment 'football_games id',
  `am_rq` VARCHAR (10) NOT NULL DEFAULT '' comment ''
)
