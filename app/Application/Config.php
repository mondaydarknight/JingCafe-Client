<?php

namespace Application;

interface Config {

	const HOST = 'localhost';
	const HOST_MAILER = 'smtp.gmail.com';
	const HOST_MAIL = 'jing2017coffee@gmail.com';
	const HOST_NAME = '靜咖啡';
	const CHARSET = 'utf-8';
	const ENCODING = 'base64';

	const DATABASE_USERNAME_ADMIN = '';
	const DATABASE_PASSWORD_ADMIN = '';
	const DATABASE_CAFE = 'Cafe';
	const DATABASE_PORT = 5435;

	// mail configuration

	const MAIL_PORT = 465;
	const SECURE_SSL = 'ssl';
	const SECURE_TLS = 'tls';

	const AUTH_TYPE = 'CRAM-MD5';
	
	const MAIL_USERNAME = '';
	const MAIL_PASSWORD = '';

	const CTYPE_DIGIT = 'ctype_digit';

	const FIVE_MINUTE = 300;

	const CONTROLLER = [
		'Administrator' => 'Core\Administrator',
		'Member' => 'Core\MemberController',
		'Visitor' => 'Core\VisitorController'
	];

	const CLASS_PATH = [
		// 'News' => 'CoffeeShop\News',
		'Product' => 'Coffee\Product',
		'Transaction' => 'Coffee\Transaction',
		'Order' => 'Coffee\Order',
	];

	const DELIVER = [
		'A' => '門市',
		'B' => '郵局',
		'C' => '7-11'
	];

	// const ORIGIN_TIMEZONE = 'UTC';
	// const TIMEZONE = 'Asia/Taipei';


}
