
CREATE SEQUENCE ocmailalert_s
    START 1
    INCREMENT 1
    MAXVALUE 9223372036854775807
    MINVALUE 1
    CACHE 1;
CREATE TABLE ocmailalert (
  id integer DEFAULT nextval('ocmailalert_s'::text) NOT NULL,
  label VARCHAR(255) DEFAULT NULL,
  frequency VARCHAR(30) NOT NULL,
  query TEXT,
  condition VARCHAR(10) NOT NULL,
  condition_value VARCHAR(255) NOT NULL,
  recipients TEXT,
  subject TEXT,
  body TEXT,
  last_call INTEGER DEFAULT 0,
  last_log TEXT
);

ALTER TABLE ONLY ocmailalert ADD CONSTRAINT ocmailalert_pkey PRIMARY KEY (id);
