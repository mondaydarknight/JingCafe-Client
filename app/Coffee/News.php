<?php

namespace Market;

use Exception;
use Application\ConfigSql;
use Command\CommandInterface;
use Core\DataBaseConnection;

class News implements CommandInterface {

	private $result = null;

	public function execute($instruction) {
		$this->$instruction['operation']($instruction);
	}

	protected function findCurrentNews() {
		$dataStore = array();
		$connect = new DataBaseConnection('admin');
		$news = $connect->setSql(ConfigSql::SEARCH_NEWS)->query();
		if (!$news) {
			throw new Exception('SQL Query Error');
		}

		foreach ($connect->fetchAllAssoc() as $key => $data) {
			$dataStore[] = 
				'<div class="panel panel-default">'.
					'<div class="panel-heading">'.
						'<h3 class="panel-title">'.
							'<a class="accordion-toggle" data-toggle="collapse" data-parent="accordionNews" href="#collapseNews'.$key.'">'.
								$data['title'].
								'<span class="pull-right">'.
									'<i class="fa fa-angle-right"></i>'.
								'</span>'.
							'</a>'.
                    	'</h3>'.
                    '</div>'.
                    '<div id="collapseNews'.$key.'" class="panel-collapse collapse">'.
						'<div class="panel-body">'.
							'<p>'.$data['context'].'</p>'.
						'</div>'.
					'</div>'.
				'</div>';
		}
		
		$this->result = array('news' => $dataStore);
	}

	public function getResult() {
		return $this->result;
	}

}