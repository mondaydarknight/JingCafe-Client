CREATE TABLE member
(
	id serial NOT NULL,
	username character varying(100) NOT NULL DEFAULT ''::character varying,
  password character varying(200) NOT NULL DEFAULT ''::character varying,
  sex character(1) NOT NULL DEFAULT 'U'::bpchar,                             -- 'M': Male, 'F':Female, 'U':Unknown
  phone character varying(50) NOT NULL DEFAULT ''::character varying,
  email character varying(300) NOT NULL DEFAULT ''::character varying,
  status character(1) NOT NULL DEFAULT ''::bpchar,                            -- '': none,D: 'Disabled' 
  createdate bigint NOT NULL DEFAULT 0,
  CONSTRAINT member_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE member
OWNER TO admin;
  
CREATE INDEX user_key1
  ON member
  USING btree
  (id, email COLLATE pg_catalog."default", password COLLATE pg_catalog."default");

CREATE INDEX user_key2
  ON member
  USING btree
  (id, username COLLATE pg_catalog."default", phone COLLATE pg_catalog."default");


