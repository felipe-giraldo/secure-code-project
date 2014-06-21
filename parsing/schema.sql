
CREATE DATABASE parsing;
USE parsing;

DROP TABLE IF EXIST transactions;
CREATE TABLE IF NOT EXISTS transactions (
  id int(11) NOT NULL AUTO_INCREMENT,
  from_account int(11) NOT NULL,
  to_account int(11) NOT NULL,
  ammount int(11) NOT NULL,
  token varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  transaction_date int(11) NOT NULL,
  transaction_state int(11) NOT NULL,
  transaction_type int(11) NOT NULL,
  PRIMARY KEY (id, from_account, to_account),
  KEY id (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=7 ;
