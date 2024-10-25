
START TRANSACTION;


SET @id := (SELECT `id` FROM `fields` WHERE `key`='error_bodies_ARRAY_PSS01');
UPDATE `multi_lang` SET `content`='To send SMS you need a valid API Key from <a href="https://clicksend.com/?u=366773" target="_blank">ClickSend</a>. If you have one, enter it in the box below.' WHERE `foreign_id`=@id AND `model`='pjField' AND `field`='title';


COMMIT;