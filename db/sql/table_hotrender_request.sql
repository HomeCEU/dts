DROP TABLE IF EXISTS hotrender_request;
CREATE TABLE hotrender_request
(
    request_id CHAR(36) NOT NULL COMMENT 'UUID to identify the HotRender Request',
    template MEDIUMTEXT NOT NULL COMMENT 'The compiled template to render the data into',
    data JSON NOT NULL COMMENT 'json data used to render template',
    created_at DATETIME NOT NULL COMMENT 'UTC DateTime',

    PRIMARY KEY (request_id)
) COMMENT 'requests for hotrenders, generally used when testing templates/formats'
