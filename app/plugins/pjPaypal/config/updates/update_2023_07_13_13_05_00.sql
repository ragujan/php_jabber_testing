
START TRANSACTION;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_paypal_client_id', 2, '', NULL, 'string', 12, 1, NULL),
(1, 'o_paypal_client_secret', 2, '', NULL, 'string', 13, 1, NULL),
(1, 'o_paypal_cancel_url', 2, '', NULL, 'string', 14, 4, NULL);

UPDATE `options` SET `is_visible`='0' WHERE `key`='o_paypal_address';

UPDATE `options` SET `order`=15 WHERE `key`='o_allow_authorize';
UPDATE `options` SET `order`=16 WHERE `key`='o_authorize_transkey';
UPDATE `options` SET `order`=17 WHERE `key`='o_authorize_merchant_id';
UPDATE `options` SET `order`=18 WHERE `key`='o_authorize_timezone';
UPDATE `options` SET `order`=19 WHERE `key`='o_authorize_md5_hash';
UPDATE `options` SET `order`=20 WHERE `key`='o_allow_cash';
UPDATE `options` SET `order`=21 WHERE `key`='o_allow_creditcard';
UPDATE `options` SET `order`=22 WHERE `key`='o_allow_bank';
UPDATE `options` SET `order`=23 WHERE `key`='o_bank_account';

INSERT INTO `fields` VALUES (NULL, 'opt_o_paypal_client_id', 'backend', 'Plugin / Paypal / Client ID', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Paypal Client ID', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'opt_o_paypal_client_secret', 'backend', 'Plugin / Paypal / Secret', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Paypal Client Secret', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'opt_o_paypal_cancel_url', 'backend', 'Plugin / Paypal / Cancel page', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cancel page', 'plugin');


INSERT INTO `fields` VALUES (NULL, 'plugin_paypal_payment_title', 'backend', 'Plugin / Paypal / PayPal payment', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'PayPal payment', 'plugin');


COMMIT;