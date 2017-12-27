cancel.php - cancel for recurrent payment - Example: /cancel.php?hash=aef0b116cdd7b58834660f9676377170&invid=1 hash - (md5(email.invid))
neatek_process.php - Do redirect payment using $_REQUEST data
result.php - ResultURL for Robokassa Merch
crontab.php - Run it in Crontab for */1 every min

Create database + Run SQL

CREATE TABLE IF NOT EXISTS `payments` (
  `inv_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Invoice ID',
  `sum` int(11) NOT NULL DEFAULT '0' COMMENT 'money',
  `email` varchar(255) DEFAULT NULL COMMENT 'email for recurrent payments',
  `recurrent` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'is this recurrent?',
  `success` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'successful or not payment',
  `canceled` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'canceled or not for recurrent payments',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date when first payment was success',
  `desc` varchar(255) NOT NULL COMMENT 'payment description',
  `params` varchar(512) DEFAULT NULL COMMENT 'shp_params',
  `redirect` varchar(512) DEFAULT NULL COMMENT 'first redirect url for once type of payments',
  `last_recurrent` timestamp NULL DEFAULT NULL COMMENT 'date of last recurrent payment',
  PRIMARY KEY (`inv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 COMMENT='Recurrent payments';

CREATE TABLE IF NOT EXISTS `recurrents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `PreviousInvoiceID` int(11) NOT NULL DEFAULT '0',
  `inv_id` int(11) NOT NULL DEFAULT '0',
  `sum` int(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;


Edit - Robokassa.recurrent.class.php 
And push database login info.