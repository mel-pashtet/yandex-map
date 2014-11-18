<?php

class PriceController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionUpdate() {
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

	public function actionDelete() {
		$attributes = $_POST;
		if(isset($_POST['id'])) {
			$id = $_POST['id'];
			$model = Price::model()->findByPk($id);

			if($model) {
				
				$msg = 'цена удалена';
				$success = true;

				try {
				   $model->delete();
				} catch (Exception $e) {
				    $msg = 'невозможно удалить цену, возможно она используеться';
				    $success = false;
				}
				
				print json_encode(array(
					"success" => $success,
					"msg" => $msg,
				));
				
			}
		}

	}
}