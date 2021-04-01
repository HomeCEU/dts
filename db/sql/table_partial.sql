DROP TABLE IF EXISTS partial;
CREATE TABLE partial
(
    partial_id CHAR(36)     NOT NULL COMMENT 'UUID to identify a single unique version',
    doc_type   VARCHAR(255) NOT NULL COMMENT 'user provided document type, works as a category',
    name       VARCHAR(255) NOT NULL COMMENT 'user provided constant',
    author     VARCHAR(255) COMMENT 'user provided author',
    body       MEDIUMTEXT   NOT NULL COMMENT 'template body',
    meta       MEDIUMTEXT   NOT NULL COMMENT 'client provided meta data',
    created_at DATETIME     NOT NULL COMMENT 'UTC datetime',
    PRIMARY KEY (partial_id),
    INDEX doctype_name (doc_type, name)
) COMMENT 'document template versions, insert only, no update please';
