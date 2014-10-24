<?php

class AutoCountController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	

	public function actionUpdatePriceRegion()
	{
		$attributes = $_POST;
		
		if( isset($attributes['region_id']) ) {
			$id = $attributes['region_id'];
			$model = AutoCount::model()->findByAttributes(array( 'region_id' => $id ));

			if( $model ) {
				$model->price_id = $attributes['price_id'];
				if( $model->save() ) {
					$success = 'Запись обновлена';
				}
				
			} else {
				$newModel = new AutoCount();
				$newModel->attributes = $attributes;
				if( $newModel->save() ) {
					$success = 'Запись сохранена';
				}
				
			}
		}
		

		print json_encode(array(
			"success" => $success
		));
	}
}