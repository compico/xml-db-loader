create database `auto-catalog`;

use `auto-catalog`;

create table `Offers`
(
    `id`            integer,
    `mark`          varchar(100),
    `model`         varchar(100),
    `generation`    varchar(100),
    `year`          integer,
    `run`           integer,
    `color`         varchar(100),
    `body-type`      varchar(100),
    `engine-type`    varchar(100),
    `transmission`  varchar(100),
    `gear-type`      varchar(100),
    `generation_id` integer
)ENGINE = InnoDB;

create table `TestOffers`
(
    `id`            integer,
    `mark`          varchar(100),
    `model`         varchar(100),
    `generation`    varchar(100),
    `year`          integer,
    `run`           integer,
    `color`         varchar(100),
    `body-type`      varchar(100),
    `engine-type`    varchar(100),
    `transmission`  varchar(100),
    `gear-type`      varchar(100),
    `generation_id` integer
)ENGINE = InnoDB;