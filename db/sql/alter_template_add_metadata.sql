ALTER TABLE template ADD COLUMN metadata MEDIUMTEXT NOT NULL COMMENT 'client provided metadata' AFTER template_key;
