create table `cities`(
  phone_code integer auto_increment primary key,
  `title` varchar(500) not null
);

insert into `cities` values(495, 'Москва');
insert into `cities` values(499, 'Москва');
insert into `cities` values(812, 'Санкт-Петербург');

alter table `known_sites`
add column `default_city_id` integer default null ;

alter table `known_sites`
add constraint `fk_city` foreign key (`default_city_id`) references `cities`(`phone_code`) on delete set null on update set null;

update `known_sites` set `default_city_id` = 812 where `domain` = 'hvosty.ru' or domain = 'vsehvosty.ru' or domain = 'poteryashka.spb.ru';
update `known_sites` set `default_city_id` = 495 where `domain` = 'pesikot.org';