create table ajax_stats (
  id integer auto_increment primary key,
  created timestamp default CURRENT_TIMESTAMP,
  `call` varchar(250) not null,
  referrer varchar(1024),
  result tinyint(1) default 1
)