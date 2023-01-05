CREATE TABLE IF NOT EXISTS `PREFIX_ntbr_config` (
    `id_ntbr_config`                int(10)         unsigned    NOT NULL    auto_increment,
    `is_default`                    tinyint(1)                  NOT NULL    DEFAULT "0",
    `name`                          varchar(255)                NOT NULL,
    `type_backup`                   varchar(255)                NOT NULL    DEFAULT "complete",
    `nb_backup`                     int(10)         unsigned    NOT NULL    DEFAULT "1",
    `send_email`                    tinyint(1)                  NOT NULL    DEFAULT "0",
    `email_only_error`              tinyint(1)                  NOT NULL    DEFAULT "0",
    `mail_backup`                   TEXT                        NOT NULL,
    `send_restore`                  tinyint(1)                  NOT NULL    DEFAULT "0",
    `activate_log`                  tinyint(1)                  NOT NULL    DEFAULT "0",
    `part_size`                     int(10)         unsigned    NOT NULL    DEFAULT "0",
    `max_file_to_backup`            int(10)         unsigned    NOT NULL    DEFAULT "0",
    `dump_max_values`               int(10)         unsigned    NOT NULL    DEFAULT "100",
    `dump_lines_limit`              int(10)         unsigned    NOT NULL    DEFAULT "25000",
    `disable_refresh`               tinyint(1)                  NOT NULL    DEFAULT "0",
    `time_between_refresh`          int(10)         unsigned    NOT NULL    DEFAULT "25",
    `time_pause_between_refresh`    int(10)         unsigned    NOT NULL    DEFAULT "0",
    `time_between_progress_refresh` int(10)         unsigned    NOT NULL    DEFAULT "1",
    `disable_server_timeout`        tinyint(1)                  NOT NULL    DEFAULT "0",
    `increase_server_memory`        tinyint(1)                  NOT NULL    DEFAULT "0",
    `server_memory_value`           int(10)         unsigned    NOT NULL    DEFAULT "128",
    `dump_low_interest_tables`      tinyint(1)                  NOT NULL    DEFAULT "0",
    `maintenance`                   tinyint(1)                  NOT NULL    DEFAULT "0",
    `time_between_backups`          int(10)         unsigned    NOT NULL    DEFAULT "600",
    `activate_xsendfile`            tinyint(1)                  NOT NULL    DEFAULT "0",
    `ignore_product_image`          int(10)         unsigned    NOT NULL    DEFAULT "0",
    `ignore_compression`            tinyint(1)                  NOT NULL    DEFAULT "0",
    `delete_local_backup`           tinyint(1)                  NOT NULL    DEFAULT "0",
    `create_on_distant`             tinyint(1)                  NOT NULL    DEFAULT "0",
    `js_download`                   tinyint(1)                  NOT NULL    DEFAULT "0",
    `backup_dir`                    text,
    `ignore_directories`            text,
    `ignore_file_types`             text,
    `ignore_tables`                 text,
    `date_add`                      datetime,
    `date_upd`                      datetime,
    PRIMARY KEY (`id_ntbr_config`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_ntbr_ftp` (
    `id_ntbr_ftp`       int(10)         unsigned    NOT NULL    auto_increment,
    `id_ntbr_config`    int(10)         unsigned    NOT NULL,
    `active`           	tinyint(1)                  NOT NULL    DEFAULT "0",
    `name`              varchar(255)                NOT NULL,
    `config_nb_backup`  int(10)         unsigned    NOT NULL    DEFAULT "0",
    `sftp`              tinyint(1)                  NOT NULL    DEFAULT "0",
    `ssl`               tinyint(1)                  NOT NULL    DEFAULT "0",
    `passive_mode`      tinyint(1)                  NOT NULL    DEFAULT "0",
    `server`            varchar(255)                NOT NULL,
    `login`             varchar(255)                NOT NULL,
    `password`          varchar(255)                NOT NULL,
    `port`              int(10)         unsigned    NOT NULL    DEFAULT "21",
    `directory`         varchar(255)                NOT NULL    DEFAULT "/",
    `date_add`          datetime,
    `date_upd`          datetime,
    PRIMARY KEY (`id_ntbr_ftp`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_ntbr_dropbox` (
    `id_ntbr_dropbox`   int(10)         unsigned    NOT NULL    auto_increment,
    `id_ntbr_config`    int(10)         unsigned    NOT NULL,
    `active`           	tinyint(1)                  NOT NULL    DEFAULT "0",
    `name`              varchar(255)                NOT NULL,
    `config_nb_backup`  int(10)         unsigned    NOT NULL    DEFAULT "0",
    `directory`         varchar(255)                NOT NULL    DEFAULT "",
    `token`             text                		NOT NULL,
    `date_add`          datetime,
    `date_upd`          datetime,
    PRIMARY KEY (`id_ntbr_dropbox`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_ntbr_owncloud` (
    `id_ntbr_owncloud`  int(10)         unsigned    NOT NULL    auto_increment,
    `id_ntbr_config`    int(10)         unsigned    NOT NULL,
    `active`           	tinyint(1)                  NOT NULL    DEFAULT "0",
    `name`              varchar(255)                NOT NULL,
    `config_nb_backup`  int(10)         unsigned    NOT NULL    DEFAULT "0",
    `login`             varchar(255)                NOT NULL,
    `password`          varchar(255)                NOT NULL,
    `server`            varchar(255)                NOT NULL,
    `directory`         varchar(255)                NOT NULL    DEFAULT "",
    `date_add`          datetime,
    `date_upd`          datetime,
    PRIMARY KEY (`id_ntbr_owncloud`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_ntbr_webdav` (
    `id_ntbr_webdav`    int(10)         unsigned    NOT NULL    auto_increment,
    `id_ntbr_config`    int(10)         unsigned    NOT NULL,
    `active`           	tinyint(1)                  NOT NULL    DEFAULT "0",
    `name`              varchar(255)                NOT NULL,
    `config_nb_backup`  int(10)         unsigned    NOT NULL    DEFAULT "0",
    `login`             varchar(255)                NOT NULL,
    `password`          varchar(255)                NOT NULL,
    `server`            varchar(255)                NOT NULL,
    `directory`         varchar(255)                NOT NULL    DEFAULT "",
    `date_add`          datetime,
    `date_upd`          datetime,
    PRIMARY KEY (`id_ntbr_webdav`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_ntbr_googledrive` (
    `id_ntbr_googledrive`   int(10)         unsigned    NOT NULL    auto_increment,
    `id_ntbr_config`        int(10)         unsigned    NOT NULL,
    `active`           		tinyint(1)                  NOT NULL    DEFAULT "0",
    `name`                  varchar(255)                NOT NULL,
    `config_nb_backup`      int(10)         unsigned    NOT NULL    DEFAULT "0",
    `directory_key`         varchar(255)                NOT NULL,
    `directory_path`        varchar(255)                NOT NULL    DEFAULT "",
    `token`                 text                		NOT NULL,
    `date_add`              datetime,
    `date_upd`              datetime,
    PRIMARY KEY (`id_ntbr_googledrive`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_ntbr_onedrive` (
    `id_ntbr_onedrive`  int(10)         unsigned    NOT NULL    auto_increment,
    `id_ntbr_config`    int(10)         unsigned    NOT NULL,
    `active`           	tinyint(1)                  NOT NULL    DEFAULT "0",
    `name`              varchar(255)                NOT NULL,
    `config_nb_backup`  int(10)         unsigned    NOT NULL    DEFAULT "0",
    `directory_key`     varchar(255)                NOT NULL,
    `directory_path`    varchar(255)                NOT NULL    DEFAULT "",
    `token`             text                		NOT NULL,
    `date_add`          datetime,
    `date_upd`          datetime,
    PRIMARY KEY (`id_ntbr_onedrive`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_ntbr_sugarsync` (
    `id_ntbr_sugarsync` int(10)         unsigned    NOT NULL    auto_increment,
    `id_ntbr_config`    int(10)         unsigned    NOT NULL,
    `active`           	tinyint(1)                  NOT NULL    DEFAULT "0",
    `name`              varchar(255)                NOT NULL,
    `config_nb_backup`  int(10)         unsigned    NOT NULL    DEFAULT "0",
    `directory_key`     varchar(255)                NOT NULL    DEFAULT "",
    `directory_path`    varchar(255)                NOT NULL    DEFAULT "",
    `token`             text                		NOT NULL,
    `login`             varchar(255)                NOT NULL,
    `date_add`          datetime,
    `date_upd`          datetime,
    PRIMARY KEY (`id_ntbr_sugarsync`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_ntbr_hubic` (
    `id_ntbr_hubic`     int(10)         unsigned    NOT NULL    auto_increment,
    `id_ntbr_config`    int(10)         unsigned    NOT NULL,
    `active`            tinyint(1)                  NOT NULL    DEFAULT "0",
    `name`              varchar(255)                NOT NULL,
    `config_nb_backup`  int(10)         unsigned    NOT NULL    DEFAULT "0",
    `directory`         varchar(255)                NOT NULL    DEFAULT "",
    `token`             text                		NOT NULL,
    `credential`        text                		NOT NULL,
    `date_add`          datetime,
    `date_upd`          datetime,
    PRIMARY KEY (`id_ntbr_hubic`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_ntbr_aws` (
    `id_ntbr_aws`       int(10)         unsigned    NOT NULL    auto_increment,
    `id_ntbr_config`    int(10)         unsigned    NOT NULL,
    `active`            tinyint(1)                  NOT NULL    DEFAULT "0",
    `name`              varchar(255)                NOT NULL,
    `config_nb_backup`  int(10)         unsigned    NOT NULL    DEFAULT "0",
    `access_key_id`     varchar(255)                NOT NULL    DEFAULT "",
    `secret_access_key` varchar(255)                NOT NULL    DEFAULT "",
    `region`            varchar(255)                NOT NULL    DEFAULT "",
    `bucket`            varchar(255)                NOT NULL    DEFAULT "",
    `storage_class`     TEXT                        NOT NULL,
    `directory_key`     varchar(255)                NOT NULL    DEFAULT "",
    `directory_path`    varchar(255)                NOT NULL    DEFAULT "",
    `date_add`          datetime,
    `date_upd`          datetime,
    PRIMARY KEY (`id_ntbr_aws`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_ntbr_backups` (
    `id_ntbr_backups`   int(10)         unsigned    NOT NULL    auto_increment,
    `id_ntbr_config`    int(10)         unsigned    NOT NULL,
    `backup_name`       varchar(255)                NOT NULL    DEFAULT "",
    `comment`           text                        NOT NULL,
    `safe`              tinyint(1)      unsigned    NOT NULL    DEFAULT "0",
    `date_add`          datetime,
    `date_upd`          datetime,
    PRIMARY KEY (`id_ntbr_backups`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;