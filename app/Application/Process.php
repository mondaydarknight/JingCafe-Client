<?php

namespace Application;

class Process {

	public static function productCombineList($productId, $productAmount) {
		$list = null;

		foreach ($productId as $key => $pid) {
			$list .= $pid .'-'. $productAmount[$key] . '|';
		}

		return mb_substr($list, 0, -1);
	}

}
