CREATE TABLE product
(
  id serial NOT NULL,
  name character varying(200) NOT NULL DEFAULT ''::character varying,
  enname character varying(200) NOT NULL DEFAULT ''::character varying,
  amount integer NOT NULL DEFAULT 0,                                          -- storage amount
  category character(1) NOT NULL DEFAULT ''::bpchar,                          -- ex: Cake: C, Bean: B, Drip Coffee: D
  serialid character varying(50) NOT NULL DEFAULT ''::character varying,     -- product id
  countryid integer NOT NULL DEFAULT 0,                                       -- country from id 
  price integer NOT NULL DEFAULT 0,                                           
  image character varying(200) NOT NULL DEFAULT ''::character varying,
  composition character varying(1000) NOT NULL DEFAULT ''::character varying,
  weight numeric NOT NULL DEFAULT 0,                                          -- product weight (g)
  period smallint NOT NULL DEFAULT 0,                                         -- product range
  bakeid smallint NOT NULL DEFAULT 0,                                         -- bake id 0: none 1:淺焙 2: 中焙 3:深焙
  releasedate bigint NOT NULL DEFAULT 0,
  updatedate bigint NOT NULL DEFAULT 0,
  CONSTRAINT product_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE product
  OWNER TO admin;

-- Index: product_key1

-- DROP INDEX product_key1;

CREATE INDEX product_key1
  ON product
  USING btree
  (id, name COLLATE pg_catalog."default");

-- Index: product_key2

-- DROP INDEX product_key2;

CREATE INDEX product_key2
  ON product
  USING btree
  (id, serialid COLLATE pg_catalog."default", price, image COLLATE pg_catalog."default");

-- Index: product_key3

-- DROP INDEX product_key3;

CREATE INDEX product_key3
  ON product
  USING btree
  (id, releasedate, updatedate);

