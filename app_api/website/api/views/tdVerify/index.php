<?php
/* @var $this TdVerifyController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Td Verifies',
);

$this->menu=array(
	array('label'=>'Create TdVerify', 'url'=>array('create')),
	array('label'=>'Manage TdVerify', 'url'=>array('admin')),
);
?>

<h1>Td Verifies</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
