<?php

namespace Coffee;

use Core\User;

class Menu {

	private $user;

	public function __construct(User $user) {
		$this->user = $user;
	}

	public function navbarOrder() {
		return 
			'<li class="nav-item dropdown">'.
			'<a class="dropdown-toggle external" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">訂單<span class="caret"></span></a>'.
			'<ul class="dropdown-menu">'.
			'<li><a href="/JiCoffee/view/pay/">結帳</a></li>'.
			'<li><a href="/JiCoffee/view/order/view/">訂單查詢</a></li>'.
			'<li><a href="#">購物聲明</a></li>'.
			'</ul>'.
			'</li>';
	}


	public function navbarCart() {

	}

	public function navbarUser() {
		return 
			// '<li class="dropdown user user-menu">'.
			'<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.
			'<i class="fa fa-user"></i>'.
			'<small>'.$this->user->getUsername().'<i class="caret"></i></small>'.
			'</a>'.
			'<ul class="dropdown-menu">'.
			'<li class="user-header">'.
			'<img src="//ssl.gstatic.com/accounts/ui/avatar_2x.png" class="img-circle" alt="user">'.
			'<p>'.$this->user->getEmail().'<small>Member since Nov. 2012</small></p>'.
			'</li>'.
			'<li class="user-footer">'.
			'<a href="#" class="btn btn-default">查看個人資料</a>'.
			'<a href="#" class="btn btn-default" id="logout">登出</a>'.
			'</li>'.
			'</ul>';
			// '</li>';
	}
}