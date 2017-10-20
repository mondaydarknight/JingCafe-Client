<?php

namespace Application;

interface ConfigSql {

	const SEARCH_NEWS = 'SELECT title, context, updatedate FROM news';

	const SEARCH_BLOG = 'SELECT title, context, image FROM blog LIMIT :blogLimit';

	const SEARCH_DELIVER = 'SELECT DISTINCT ON (type) type, id, name, fee, message FROM deliver';

	const SEARCH_DELIVER_DETAIL = 'SELECT id, name, address FROM deliver';

	const SEARCH_PRODUCT = 'SELECT name, serialid, price, image FROM product';

	const SEARCH_PRODUCT_DETAIL = 'SELECT id, name, price, image, composition, weight, period, releasedate FROM product WHERE serialid = :serialid';

	const SEARCH_ORDER = 'SELECT id, name, email, phone, address, list, bankaccount, deliverid, totalprice, ispay, status, updatedate FROM transaction';

	const SEARCH_USER_ALL_ORDER = 'SELECT t.id, t.address, list, bankaccount, deliver.name AS delivername, totalprice, ispay, status, t.updatedate FROM transaction AS t INNER JOIN deliver ON t.deliverid = deliver.id WHERE t.userid = :userId ORDER BY t.updatedate DESC';







	const SEARCH_EMAIL_EXIST = 'SELECT id, username, password, phone FROM member WHERE email = :email';


	const INSERT_TRANSACTION = 'INSERT INTO transaction (name, address, phone, email, list, bankaccount, deliverid, totalprice, message, userid, updatedate) VALUES (:name, :address, :phone, :email, :list, :bankaccount, :deliverid, :totalprice, :message, :userid, :updatedate)';

	const INSERT_MEMBER = 'INSERT INTO member (username, password, sex, phone, email, createdate) VALUES (:username, :password, :sex, :phone, :email, :createdate)';


	const UPDATE_TRANSACTION = 'UPDATE transaction SET name = :username, email = :email, phone = :phone, address = :address, list = :list, bankaccount = :bankaccount, deliverid = :deliverid, totalprice = :totalprice, message = :message WHERE id = :transactionid AND userid = :userid';


	const CANCEL_TRANSACTION = 'UPDATE transaction SET status = :status WHERE userid = :userId AND id = :transactionId';
	

}
