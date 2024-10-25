
START TRANSACTION;

ALTER TABLE `plugin_invoice` MODIFY `subtotal` decimal(9,2) DEFAULT NULL;
ALTER TABLE `plugin_invoice` MODIFY `discount` decimal(9,2) DEFAULT NULL;
ALTER TABLE `plugin_invoice` MODIFY `tax` decimal(9,2) DEFAULT NULL;
ALTER TABLE `plugin_invoice` MODIFY `shipping` decimal(9,2) DEFAULT NULL;
ALTER TABLE `plugin_invoice` MODIFY `total` decimal(9,2) DEFAULT NULL;
ALTER TABLE `plugin_invoice` MODIFY `paid_deposit` decimal(9,2) DEFAULT NULL;
ALTER TABLE `plugin_invoice` MODIFY `amount_due` decimal(9,2) DEFAULT NULL;

ALTER TABLE `plugin_invoice_items` MODIFY `unit_price` decimal(9,2) DEFAULT NULL;
ALTER TABLE `plugin_invoice_items` MODIFY `amount` decimal(9,2) DEFAULT NULL;

COMMIT;