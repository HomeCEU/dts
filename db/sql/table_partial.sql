DROP TABLE IF EXISTS partial;
CREATE TABLE partial
(
    partial_id  CHAR(36)     NOT NULL COMMENT 'UUID to identify a single unique version',
    partial_key VARCHAR(255) NOT NULL COMMENT 'user provided constant',
    doc_type    VARCHAR(255) NOT NULL COMMENT 'user provided document type, works as a category',
    author      VARCHAR(255) COMMENT 'user provided author',
    body        MEDIUMTEXT   NOT NULL COMMENT 'template body',
    metadata    MEDIUMTEXT   NOT NULL COMMENT 'client provided metadata',
    created_at  DATETIME     NOT NULL COMMENT 'UTC datetime',
    PRIMARY KEY (partial_id),
    INDEX doctype_name (doc_type, partial_key)
) COMMENT 'document template versions, insert only, no update please';
