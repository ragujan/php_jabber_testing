
START TRANSACTION;

ALTER TABLE `plugin_invoice_items` MODIFY `qty` decimal(9,2) DEFAULT NULL;
ALTER TABLE `plugin_invoice_items` MODIFY `amount` decimal(9,2) DEFAULT NULL;

COMMIT;