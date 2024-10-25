
START TRANSACTION;

UPDATE `options` SET `key`='o_authorize_hash' WHERE `key`='o_authorize_md5_hash';

INSERT INTO `fields` VALUES (NULL, 'opt_o_authorize_hash', 'backend', 'Authorize plugin / Authorize.Net signature key', 'plugin', '2016-05-03 17:12:32');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Authorize.Net signature key', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'opt_o_authorize_silent_post_url', 'backend', 'Authorize plugin / Authorize.net Silent post URL', 'plugin', '2016-05-03 17:12:32');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Authorize.net Silent post URL', 'plugin');


COMMIT;