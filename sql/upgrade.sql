-- This file contains a series of sql statements that upgrade your database from the
-- official version of poMMo to the current version on:
-- https://github.com/soonick/poMMo/
-- Change all occurrences of <prefix> to the correct prefix your database is using
-- Change all occurrences of <username> with the user you will use to login
-- Change all occurrences of <password> with the password you will use to login

ALTER TABLE
    <prefix>_mailings
ADD COLUMN
    track tinyint(1) NULL;

CREATE TABLE <prefix>_attachment_files(
    file_id smallint(5) unsigned NOT NULL auto_increment,
    file_name tinytext  NOT NULL,
    PRIMARY KEY  (file_id)
);

CREATE TABLE <prefix>_mailings_attachments(
    mailing_id int(10) unsigned NOT NULL,
    file_id smallint(5) unsigned NOT NULL,
    PRIMARY KEY  (mailing_id, file_id)
);

CREATE TABLE <prefix>_mailings_hits (
    subscriber_id int(10) unsigned NOT NULL,
    mailing_id int(10) unsigned NOT NULL,
    hit_date date NOT NULL,
    PRIMARY KEY (subscriber_id, mailing_id)
);

CREATE TABLE <prefix>_users (
    username char(50) NOT NULL,
    password char(40) NOT NULL,
    PRIMARY KEY  (username)
);

INSERT INTO
    <prefix>_users (
        username,
        password
    )
VALUES(
    '<username>',
    SHA1('<password>')
);

