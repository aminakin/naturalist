create table if not exists b_bronevik_advance_hotels
(
    ID int(11) not null auto_increment,
    NAME varchar(255) NULL,
    CODE varchar(50) NOT NULL,
    ADDRESS varchar(255) NULL,
    CITY varchar(255) NULL,
    COUNTRY varchar(255) NULL,
    ZIP varchar(255) NULL,
    LAT varchar(255) NULL,
    LON varchar(255) NULL,
    DESCRIPTION text NULL,
    PHOTOS text NULL,
    LAST_MODIFIED datetime null,
    PRIMARY KEY (ID),
    INDEX ix_b_bronevik_name (NAME)
    );