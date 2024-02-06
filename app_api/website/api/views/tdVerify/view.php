<?php
/* @var $this TdVerifyController */
/* @var $model TdVerify */

$this->breadcrumbs=array(
	'Td Verifies'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List TdVerify', 'url'=>array('index')),
	array('label'=>'Create TdVerify', 'url'=>array('create')),
	array('label'=>'Update TdVerify', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete TdVerify', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage TdVerify', 'url'=>array('admin')),
);
?>

<h1>View TdVerify #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'phone',
		'verify',
		'created_time',
		'type',
	),
)); ?>
