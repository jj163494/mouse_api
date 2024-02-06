<?php
/* @var $this TdVerifyController */
/* @var $model TdVerify */

$this->breadcrumbs=array(
	'Td Verifies'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List TdVerify', 'url'=>array('index')),
	array('label'=>'Create TdVerify', 'url'=>array('create')),
	array('label'=>'View TdVerify', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage TdVerify', 'url'=>array('admin')),
);
?>

<h1>Update TdVerify <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>