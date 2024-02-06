<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */

?>
<?php echo "<?php\n"; ?>
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/css/content.css");
?>
<?php echo "<?php\n"; ?>
/* @var $this <?php echo $this->getControllerClass(); ?> */
/* @var $model <?php echo $this->getModelClass(); ?> */
/* @var $form CActiveForm */
?>




<div class="main_right_content">
	<ol class="breadcrumb main_right_content_breadcrumb">
		<li><a href="#">用户管理</a></li>
		<li><a href="#">用户详细</a></li>
	</ol>

	<div class="main_right_content_content">
		<?php 
		echo '<?php if($msg) {?>
				<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>警告！</strong> <?php echo $msg;?>
				</div>
				<?php }?>';
		?>

		<?php echo "<?php \$form=\$this->beginWidget('CActiveForm', array(
			'id'=>'".$this->class2id($this->modelClass)."-form',
			'htmlOptions'=>array('enctype'=>'multipart/form-data', 'class'=>'form-horizontal'),
			'enableAjaxValidation'=>false,
		)); ?>\n"; ?>
				
			<?php
				foreach($this->tableSchema->columns as $column)
				{
					if($column->autoIncrement)
						continue;
				?>
	<div class="form-group">
					<?php echo "<?php echo ".MyCrudCode::generateActiveLabelWithOption($this->modelClass,$column, 'array("class"=>"col-sm-2 control-label")').";?>\n";?>
					<div class="col-sm-8">
					<?php echo "\t<?php echo ".MyCrudCode::generateActiveFieldWithOption($this->modelClass,$column, array('class'=>'form-control'))."; ?>\n"; ?>
					</div>
				</div>
			<?php } ?>	

			<div class="form-group form-group-center">
				<button class="btn btn-success btn-lg" type="submit">　确定　</button>
				<a class="btn btn-default btn-lg" type="button">　取消　</a>
			</div>

		<?php echo "<?php\n"; ?> $this->endWidget (); ?>
	</div>
</div>

