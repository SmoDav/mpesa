CREATE DATABASE securities;
USE securities;
CREATE USER 'scottdb'@'localhost' IDENTIFIED BY 'nycdsa';
GRANT ALL ON securities.* TO 'scottdb'@'localhost';
FLUSH PRIVLIGES;

CREATE TABLE stock_data (
ticker varchar(5) NOT NULL,
close decimal(19,4) NULL,
date date NOT NULL
):

INSERT INTO stock_data VALUES ('AAPL', 100, STR_TO_DATE('2/1/08','%e/%c/%y'))


CREATE TABLE common_data (
SPX decimal(10,10) NULL,
NYSEVOL decimal(10,10) NULL,
CCMP decimal(10,10) NULL,
date date NOT NULL
);
