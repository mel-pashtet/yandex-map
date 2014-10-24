<?php

class RegionsController extends Controller
{	

	public function accessRules() {
			return array(
				array('allow',  // allow all users to perform 'index' and 'view' actions
					'actions'=>array('index','list','insertIntoDb', 'create', 'loadPriceFromDb', 'edit', 'getPrice', 'getRegions', 'getPriceRegion'),
					'users'=>array('@'),
				),
				
				array('deny',  // deny all users
					'users'=>array('*'),
				),
			);
	}

	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
			
		);
	}
	
	public function actionIndex() {
		$this->render('index');
	}


	public function actionCreate() {
		$modelRegion = Regions::model();
		$this->render('create', array('modelRegion' => $modelRegion ) );
	}

	public function actionList() {
		$regions = Regions::model()->findAll();
		$arrayResult = array();
		foreach ($regions as $region) {
			$arrayResult[$region->title] = $region->coordinates;
		}
		
		echo json_encode($arrayResult);
	}

	public function actionInsertIntoDb() {
		$region = new Regions();
		$region->attributes = $_POST;
		$region->save(); 
		
	}

	public function actionLoadPriceFromDb() {
		$title = $_POST['title'];
		$region = Regions::model()->findByAttributes( array( 'title' => $title ) );
		$arrayResult = array();
		
		if( $region && $region->autoCount && $region->autoCount->price->cost ) {
			$arrayResult[$region->title] = $region->autoCount->price->cost; 
			echo json_encode($arrayResult);
			
		}
		
		
	}

	public function actionEdit() {
		$this->render('edit');
	}

	public function actionGetPrice() {
		$priceModels = Price::model()->findAll();
		
		$arrayResult = array();
		$array = array();
		
		foreach ($priceModels as $priceModel) {
			$arrayResult['id'] = $priceModel->id;
			$arrayResult['cost'] = $priceModel->cost;
			$array[] = $arrayResult;
		}
		echo json_encode($array);
	}

	public function actionGetRegions() {
		$regionModels = Regions::model()->findAll();
		
		$arrayResult = array();
		$array = array();
		
		foreach ($regionModels as $regionModel) {
			$arrayResult['id'] = $regionModel->id;
			$arrayResult['title'] = $regionModel->title;
			$array[] = $arrayResult;
		}
		echo json_encode($array);
	}

	public function actionGetPriceRegion() {

		$arrayResult = array();
		$array = array();
		$regionModels = Regions::model()->findAll();

		foreach ($regionModels as $regionModel) {
			if( $regionModel->autoCount && $regionModel->autoCount->price ) {
				$arrayResult['cost'] = $regionModel->autoCount->price->cost;
				$arrayResult['title'] = $regionModel->title;
				$array[] = $arrayResult;
			}
		}
		
		echo json_encode($array);
	}
}