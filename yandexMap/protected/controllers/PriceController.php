<?php

class PriceController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionUpdate()
	{
		$attributes = $_POST;
		
		if( isset($attributes['id']) ) {
			$id = $attributes['id'];
			$model = Price::model()->findByPk($id );

			if( $model ) {
				$model->cost = $attributes['cost'];
				if( $model->save() ) {
					$success = 'Запись обновлена';
				}
				
			} else {
				$newModel = new Price();
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