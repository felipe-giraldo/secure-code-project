
CREATE DATABASE parsing;
USE parsing;

DROP TABLE IF EXIST transactions;
CREATE TABLE transactions (
  id int auto_increment not null primary key,
  date datetime,
  from_account varchar(20),
  to_account varchar(20),
  value double,
  token varchar(15),
  type varchar(20),
  status varchar(20)
);
