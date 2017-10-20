<?php

namespace Coffee;

use Exception;
use Application\ConfigSql;
use Application\Process;
use Application\Time;
use Core\DataBaseConnection;
use Core\User;
use Mail\Mailer;

class Transaction {
	/**
     * database transaction data
     * @param array
     */
	private $deal = [
		':name' 		=> '',
		':address' 		=> '',
		':phone' 		=> '',
		':email' 		=> '',
		':list' 		=> '',
		':bankaccount' 	=> '',
		':deliverid' 	=> 0,
		':totalprice' 	=> 0,
		':message' 		=> '',
		':userid' 		=> 0,
		':updatedate' 	=> 0
	];

	// private $productName = array();

	/**
     * product each ID
     * @param array(int)
     */
	// private $productId = array();

	/**
	 * product each amount
	 * @param array(int)
	 */
	// private $productAmount = array();

	private $mailStyle = null;

	public function __construct(User $user) {
		$this->user = $user;
	}

	public function sendTransaction() {
		
		$this->deal[':name'] = $this->user->getUsername();
		$this->deal[':phone'] = $this->user->getPhone();
		$this->deal[':email'] = $this->user->getEmail();
		$this->deal[':list'] = Process::productCombineList($this->user->getProductId(), $this->user->getProductAmount());
		$this->deal[':bankaccount'] = $this->user->getBankAccount(); 
		$this->deal[':totalprice'] = $this->user->getPrice();
		$this->deal[':deliverid'] = $this->user->getDeliverId();
		$this->deal[':userid'] = $this->user->getUserId();
		$this->deal[':updatedate'] = Time::getCurrentTimeOnlyNumber();

		$this->insertTransaction();

		return $this->sendMailMessage();
	}

	protected function productListCombine() {
		$list = null;

		foreach ($this->user->getProductId() as $key => $pid) {
			$list .= $pid .'-'. $this->user->getProductAmount()[$key] . '|';
		}

		$this->deal[':list'] = mb_substr($list, 0, -1);
	}

	protected function insertTransaction() {
		$connect = new DataBaseConnection('admin');

		$query = $connect->setSql(ConfigSql::INSERT_TRANSACTION)->setFactor($this->deal)->query();
		
		if (!$query) {
			throw new Exception('SQL_ERROR');
		}
	}

	protected function mailStyleLoad() {
		ob_start();
		require_once(dirname(__DIR__) . '\Application\Responsive.php');
		$this->mailStyle = ob_get_contents();
		ob_end_clean();
	}

	protected function sendMailMessage() {
		$productList = null;
		$mail = new Mailer;

		$this->mailStyleLoad();
		
		$html = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- If you delete this meta tag, Half Life 3 will never be released. -->
<meta name="viewport" content="width=device-width" />

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ZURBemails</title>
	{$this->mailStyle}
</head>
 
<body bgcolor="#FFFFFF">
<!-- HEADER -->
<table class="head-wrap" bgcolor="#795548">
	<tr>
		<td></td>
		<td class="header container" >	
			<div class="content">
				<table bgcolor="#795548">
					<tr>
						<td></td>
						<td align="left"><h3 class="collapse" style="color: #ffffff;font-weight: bold;">寂咖啡</h3></td>
					</tr>
				</table>
			</div>	
		</td>
	</tr>
</table><!-- /HEADER -->
<!-- BODY -->
<table class="body-wrap">
	<tr>
		<td></td>
		<td class="container" bgcolor="#FFFFFF">
			<div class="content">
			<table>
				<tr>
					<td>
						<h3>您的交易已送出</h3>
						<p class="lead">提醒您至【購買清單-總覽】查看交易進度並完成接續的交易動作。</p>
												
						<!-- product list -->
						<table class="social" width="100%">
							<tr>
								<td>
									<table align="left" class="column">
										<tr>
											<td>
												<h5 class="">訂購清單:</h5>
												<!-- <p class=""><a href="#" class="soc-btn fb">Facebook</a> <a href="#" class="soc-btn tw">Twitter</a> <a href="#" class="soc-btn gp">Google+</a></p> -->
EOT;
		
		foreach ($this->user->getProductName() as $key => $product) {
			$html .= <<<EOT
<p>{$product}x{$this->user->getProductAmount()[$key]}</p>
EOT;
		}

		$html .= <<<EOT
<br/>									
											</td>
										</tr>
									</table>
									<table align="right" class="column">
										<tr>
											<td>
												<h5>結算</h5>					
												<p>運費總計: NT$ <strong>0</strong><br/>
												您的購買總金額: NT$ <strong>{$this->deal[':totalprice']}</strong>
                								</p>
											</td>
										</tr>
									</table>
									<span class="clear"></span>	
								</td>
							</tr>
						</table>
						<p class="callout">
							欲查詢訂單或修改訂單<a href="http://JiCoffee/">請點擊 &raquo;</a>
						</p>
					</td>
				</tr>
			</table>
			</div>	
		</td>
	</tr>
</table><!-- /BODY -->

<!-- FOOTER -->
<table class="footer-wrap">
	<tr>
		<td></td>
		<td class="container">
				<!-- content -->
				<div class="content">
				<table>
				<tr>
					<td align="center">
						<p>
							<a href="#">聯絡我們</a> |
							<a href="#">網站使用條例</a> |
							<a href="#"><unsubscribe>隱私權保護申明</unsubscribe></a>
						</p>
					</td>
				</tr>
			</table>
			</div><!-- /content -->	
		</td>
		<td></td>
	</tr>
</table><!-- /FOOTER -->
</body>
</html>
EOT;
	
		$mail->setUp();
		$mail->setAddress($this->user->getEmail());
		return $mail->setSubject('寂咖啡訂購商品成功')->setBody($html)->sendMail() ? '結帳成功' : array('error' => '發生錯誤');

	}
	
}

