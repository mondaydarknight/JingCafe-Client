CREATE TABLE transaction
(
  id bigserial NOT NULL,
  name character varying(100) NOT NULL DEFAULT ''::character varying,
  address character varying(1000) NOT NULL DEFAULT ''::character varying,
  phone character varying(50) NOT NULL DEFAULT ''::character varying,
  email character varying(300) NOT NULL DEFAULT ''::character varying,
  list character varying(800) NOT NULL DEFAULT ''::character varying,         -- ex: 39(product id)-3(product number)|27-2|19-1
  bankaccount character varying(100) NOT NULL DEFAULT ''::character varying,
  deliverid smallint NOT NULL DEFAULT 0,                                        -- if deliver is 7-11, then search their id
  totalprice numeric NOT NULL DEFAULT 0 CHECK(totalprice >= 0),
  message text NOT NULL DEFAULT '',
  ispay boolean NOT NULL DEFAULT FALSE,
  status character(1) NOT NULL DEFAULT ''::bpchar,
  userid integer NOT NULL DEFAULT 0,                                           -- JiCoffee user member
  updatedate bigint NOT NULL DEFAULT 0,
  CONSTRAINT transaction_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE transaction
  OWNER TO admin;

-- Index: transaction_key1

-- DROP INDEX transaction_key1;

CREATE INDEX transaction_key1
  ON transaction
  USING btree
  (id, name COLLATE pg_catalog."default", bankaccount COLLATE pg_catalog."default");

-- Index: transaction_key2

-- DROP INDEX transaction_key2;

CREATE INDEX transaction_key2
  ON transaction
  USING btree
  (id, list COLLATE pg_catalog."default", totalprice, email COLLATE pg_catalog."default");

-- Index: transaction_key3

-- DROP INDEX transaction_key3;

CREATE INDEX transaction_key3
  ON transaction
  USING btree
  (id, email, ispay);

CREATE INDEX transaction_key4
  ON transaction
  USING btree
  (id, deliverid, list COLLATE pg_catalog."default", updatedate);
