
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_api_username', 'backend', 'SMS plugin / Label / SMS API Username', 'plugin', '2017-11-20 07:46:39');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMS API Username', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_api_key', 'backend', 'SMS plugin / Label / SMS API Key', 'plugin', '2017-11-20 07:46:39');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMS API Key', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_SUCCESS', 'arrays', 'plugin_sms_statuses_ARRAY_SUCCESS', 'plugin', '2017-11-20 08:11:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message sent', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_MISSING_CREDENTIALS', 'arrays', 'plugin_sms_statuses_ARRAY_MISSING_CREDENTIALS', 'plugin', '2017-11-20 08:11:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Not enough information has been supplied for authentication. Please ensure that your Username and Unique Key are supplied in your request.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_ACCOUNT_NOT_ACTIVATED', 'arrays', 'plugin_sms_statuses_ARRAY_ACCOUNT_NOT_ACTIVATED', 'plugin', '2017-11-20 08:11:42');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your account has not been activated.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_INVALID_RECIPIENT', 'arrays', 'plugin_sms_statuses_ARRAY_INVALID_RECIPIENT', 'plugin', '2017-11-20 08:11:54');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The destination mobile number is invalid.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_THROTTLED', 'arrays', 'plugin_sms_statuses_ARRAY_THROTTLED', 'plugin', '2017-11-20 08:12:05');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Identical message body recently sent to the same recipient. Please try again in a few seconds.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_INVALID_SENDER_ID', 'arrays', 'plugin_sms_statuses_ARRAY_INVALID_SENDER_ID', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Invalid Sender ID. Please ensure Sender ID is no longer than 11 characters (if alphanumeric), and contains no spaces.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_INSUFFICIENT_CREDIT', 'arrays', 'plugin_sms_statuses_ARRAY_INSUFFICIENT_CREDIT', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You have reached the end of your message credits. You will need to purchase more message credits.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_INVALID_CREDENTIALS', 'arrays', 'plugin_sms_statuses_ARRAY_INVALID_CREDENTIALS', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your Username or Unique Key is incorrect.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_ALREADY_EXISTS', 'arrays', 'plugin_sms_statuses_ARRAY_ALREADY_EXISTS', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The resource you''re trying to add already exists.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_EMPTY_MESSAGE', 'arrays', 'plugin_sms_statuses_ARRAY_EMPTY_MESSAGE', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message is empty.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_TOO_MANY_RECIPIENTS', 'arrays', 'plugin_sms_statuses_ARRAY_TOO_MANY_RECIPIENTS', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Too many recipients.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_MISSING_REQUIRED_FIELDS', 'arrays', 'plugin_sms_statuses_ARRAY_MISSING_REQUIRED_FIELDS', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Some required fields are missing.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_INVALID_SCHEDULE', 'arrays', 'plugin_sms_statuses_ARRAY_INVALID_SCHEDULE', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The schedule specified is invalid. Use a unix timestamp e.g. 1429170372.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_NOT_ENOUGH_PERMISSION_TO_LIST_ID', 'arrays', 'plugin_sms_statuses_NOT_ENOUGH_PERMISSION_TO_LIST_ID', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Don''t have enough privilege to access or send to a list_id.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_INTERNAL_ERROR', 'arrays', 'plugin_sms_statuses_ARRAY_INTERNAL_ERROR', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Internal error.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_INVALID_LANG', 'arrays', 'plugin_sms_statuses_ARRAY_INVALID_LANG', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'An invalid language option has been provided.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_INVALID_VOICE', 'arrays', 'plugin_sms_statuses_ARRAY_INVALID_VOICE', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'An invalid voice (gender) option has been provided.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_SUBJECT_REQUIRED', 'arrays', 'plugin_sms_statuses_ARRAY_SUBJECT_REQUIRED', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Usually happens when MMS Subject is empty.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_INVALID_MEDIA_FILE', 'arrays', 'plugin_sms_statuses_ARRAY_INVALID_MEDIA_FILE', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Usually MMS media file is invalid file.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_sms_statuses_ARRAY_SOMETHING_IS_WRONG', 'arrays', 'plugin_sms_statuses_ARRAY_SOMETHING_IS_WRONG', 'plugin', '2017-11-20 08:12:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Generic Error happened.', 'plugin');

COMMIT;