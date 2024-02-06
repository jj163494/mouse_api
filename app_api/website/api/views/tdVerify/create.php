<?php
/* @var $this TdVerifyController */
/* @var $model TdVerify */

$this->breadcrumbs=array(
	'Td Verifies'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List TdVerify', 'url'=>array('index')),
	array('label'=>'Manage TdVerify', 'url'=>array('admin')),
);
?>

<h1>Create TdVerify</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>