alter table phones add column created TIMESTAMP;

create table post_texts (id integer auto_increment primary key, `text` longtext character set 'utf8' not null );


alter table phone_proofs add column post_text_id integer, add FOREIGN KEY (post_text_id) references post_texts(id) on delete set null on update set null;
