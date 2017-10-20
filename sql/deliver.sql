CREATE TABLE deliver
(
	id serial NOT NULL,
	name character varying(100) NOT NULL DEFAULT ''::character varying,
  	type character(1) NOT NULL DEFAULT 'A'::bpchar,                         
  	fee int NOT NULL DEFAULT 0,
  	address character varying(500) NOT NULL DEFAULT ''::character varying,
    message character varying(100) NOT NULL DEFAULT ''::character varying,
  	createdate bigint NOT NULL DEFAULT 0,
  	CONSTRAINT deliver_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE deliver
OWNER TO admin;
  
CREATE INDEX deliver_key1
  ON deliver
  USING btree
  (id, name COLLATE pg_catalog."default", fee);

