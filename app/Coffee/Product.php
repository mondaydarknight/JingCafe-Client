<?php

namespace Coffee;

use Exception;
use Application\ConfigSql;
use Core\DataBaseConnection;
use Core\User;

class Product {

	private $connect;
	
	public function __construct(User $user) {
		$this->user = $user;
	}

	public function connectDatabase() {
		$this->connect = new DataBaseConnection('admin');
	}

	public function findProduct() {
		$this->connectDatabase();

		return $this->connect->setSql(ConfigSql::SEARCH_PRODUCT.' WHERE category = :category')->setFactor(array(':category' => $this->user->getCategory()))->query() ? $this->productList() : $this->productNotFound();
	}

	public function findProductSerial() {
		$this->connectDatabase();

		$sql = ConfigSql::SEARCH_PRODUCT.' WHERE category = :category AND substr(serialid, :index, :len) = :serial';
		$factor = [
			':category' => $this->user->getCategory(), 
			':index' => 2, 
			':len' => 1, 
			':serial' => $this->user->getSerial()
		];
	
		return $this->connect->setSql($sql)->setFactor($factor)->query() ? $this->productList() : $this->productNotFound();
	}

	public function findProductDetail() {
		$this->connectDatabase();

		$query = $this->connect->setSql(ConfigSql::SEARCH_PRODUCT_DETAIL)
			->setFactor(['serialid' => $this->user->getSerial()])->query();

		return $query ? $this->productListDetail() : $this->productNotFound();
	}

	public function productList() {
		$storage = array();

		foreach ($this->connect->fetchAllAssoc() as $key => $product) {
			$storage[] = 
				'<div class="col-md-4 col-sm-4 col-xs-12">'.
					'<div class="product-image-wrapper">'.
						'<div class="single-products">'.
							'<div class="productinfo text-center">'.
								'<img src="/JiCoffee/images/product/normal/'.(!empty($product['image']) ? $product['image'] : 'none.png').'" class="img-responsive">'.
								'<p>'.$product['name'].'</p>'.
								'<h2>NT '.number_format($product['price']).'</h2>'.						
								'<a href="/JiCoffee/view/product/detail/?serial='.$product['serialid'].'" class="btn btn-coffee"><i class="fa fa-shopping-cart"></i>加入購物車</a>'.
							'</div>'.
						'</div>'.
					'</div>'.
				'</div>';
		}

		return array('products' => $storage);
	}

	public function productNotFound() {
		$storage = '<div class="text-center">'.
				   '<h2>尚無資料</h2>'.
				   '</div>';
		
		return array('products' => $storage);
	}

	protected function productListDetail() {
		$htmlComposition = null;
		$process = $this->connect->fetchAssoc();

		if (!empty($process['composition'])) {
			$compositions = explode('|', $process['composition']);
			$htmlComposition = '<div class="panel-body"><ul>';
								
			foreach ($compositions as $composition) {
				$htmlComposition .= '<li>'.$composition.'</li>';
			}
			$htmlComposition .= '</ul></div>';
		}

		$image = !empty($process['image']) ? $process['image'] : 'none.png';

		$product = 
			'<div class="col-sm-5">'.
				'<div class="view-product">'.
					'<img src="/JiCoffee/images/product/large/'.$image.'" class="img-thumbnail product">'.
				'</div>'.
			'</div>'.
			'<div class="col-sm-7">'.
				'<h2 class="name" data-id="'.$process['id'].'" data-image="'.$image.'">'.$process['name'].'</h2>'.
				'<hr>'.
				'<form role="form">'.
					'<div class="form-group">'.
						'<label>編號 : <span class="serial">'.$this->user->getSerial().'</span></label>'.
						'<label>狀態 :</label>'.
						$htmlComposition.
						'<span class="badge">有庫存</span>'.
						'<h3>NT$ <span class="price">'.number_format($process['price']).'</span></h3>'.
						'<div class="input-group">'.
							'<span class="input-group-btn">'.
								'<a href="#" id="minus" class="btn"><span><i class="fa fa-minus"></i></span></a>'.
							'</span>'.
							'<input type="text" id="productAmount" class="form-control text-center input-xs" value="1" style="font-size:18px;">'.
							'<a href="#" id="plus" class="btn"><span><i class="fa fa-plus"></i></span></a>'.
							'<button class="btn btn-primary" id="purchaseCart" data-toggle="modal" data-target="#cart-modal"><i class="fa fa-shopping-cart"></i>加入購物車</button>'.
						'</div>'.
					'</div>'.
				'</form>'.
			'</div>';

		return array('product' => $product);
	}

}