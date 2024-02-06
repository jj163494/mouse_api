<?php
Class MyCrudCode extends CrudCode{
	public function generateActiveLabelWithOption($modelClass,$column, $option)
	{
		return "\$form->labelEx(\$model,'{$column->name}', {$option})";
	}	

	public function generateActiveFieldWithOption($modelClass,$column, $option = array())
	{
		if($column->type==='boolean')
			return "\$form->checkBox(\$model,'{$column->name}', ".self::arrayToString($option).")";
		elseif(stripos($column->dbType,'text')!==false){
			$option['rows'] = 6;
			$option['cols'] = 50;
			return "\$form->textArea(\$model,'{$column->name}',".self::arrayToString($option).")";
		}else
		{
			if(preg_match('/^(password|pass|passwd|passcode)$/i',$column->name))
				$inputField='passwordField';
			else
				$inputField='textField';

			if($column->type!=='string' || $column->size===null)
				return "\$form->{$inputField}(\$model,'{$column->name}', ".self::arrayToString($option).")";
			else
			{
				if(($size=$maxLength=$column->size)>60)
					$size=60;
				$option['size'] = $size;
				$option['maxlength'] = $maxLength;
				return "\$form->{$inputField}(\$model,'{$column->name}',".self::arrayToString($option).")";
			}
		}
	}

	private function arrayToString($array)
	{
		$string = '';
		if (is_array($array)) {
			
			$dealArray = array();
			foreach ($array as $key => $value) {
				$dealArray[] = "'".$key."'=>'".$value."'";
			}
			$string = 'array('.implode($dealArray, ",").')';
		}
		return $string;
	}
}

?>