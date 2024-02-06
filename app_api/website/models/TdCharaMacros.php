<?php

/**
 * This is the model class for table "td_chara_macros".
 *
 * The followings are the available columns in table 'td_chara_macros':
 * @property integer $cmacro_id
 * @property integer $chara_id
 * @property string $chara_name
 * @property string $chara_nameEN
 * @property string $chara_nameZHT
 * @property string $macro_name
 * @property string $macro_nameEN
 * @property string $macro_nameZHT
 * @property string $macro_content
 * @property integer $created_time
 * @property integer $update_time
 * @property integer $is_delete
 */
class TdCharaMacros extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_chara_macros';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('chara_id, macro_name, macro_content', 'required'),
			array('chara_id, created_time, update_time, is_delete', 'numerical', 'integerOnly'=>true),
			array('chara_name, macro_name, chara_nameZHT, macro_nameZHT', 'length', 'max'=>32),
			array('chara_nameEN, macro_nameEN', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('cmacro_id, chara_id, chara_name, chara_nameEN, chara_nameZHT, macro_name, macro_nameEN, macro_nameZHT, macro_content, created_time, update_time, is_delete', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'cmacro_id' => '主键id',
			'chara_id' => '游戏角色id',
			'chara_name' => '游戏角色名称',
			'chara_nameEN' => '游戏角色名称(英文)',
			'chara_nameZHT' => '游戏角色名称(繁体中文)',
			'macro_name' => '宏名称',
			'macro_nameEN' => '宏名称(英文)',
			'macro_nameZHT' => '宏名称(繁体中文)',
			'macro_content' => '宏内容',
			'created_time' => '创建时间',
			'update_time' => '修改时间',
			'is_delete' => '0未删除,1已删除',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('cmacro_id',$this->cmacro_id);
		$criteria->compare('chara_id',$this->chara_id);
		$criteria->compare('chara_name',$this->chara_name,true);
		$criteria->compare('chara_nameEN',$this->chara_nameEN,true);
		$criteria->compare('chara_nameZHT',$this->chara_nameZHT,true);
		$criteria->compare('macro_name',$this->macro_name,true);
		$criteria->compare('macro_nameEN',$this->macro_nameEN,true);
		$criteria->compare('macro_nameZHT',$this->macro_nameZHT,true);
		$criteria->compare('macro_content',$this->macro_content,true);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('update_time',$this->update_time);
		$criteria->compare('is_delete',$this->is_delete);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdCharaMacros the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * 获取角色宏明细
	 * @param string $condition
	 * @param string $field
	 * @param string $order
	 * @return array|mixed|null
	 */
	public function getCharaMacroInfo($condition = "", $field = "*", $order = "cmacro_id DESC"){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
			'order' => $order,
		);
		return $this->find($criteria);
	}


	/**
	 * 获取角色宏列表
	 * @param string $condition
	 * @param string $field
	 * @param int $page_size
	 * @param int $offset
	 * @param string $order
	 * @param string $group
	 * @return array|mixed|null
	 */
	public function getCharaMacroList($condition = "", $field = '*', $page_size = 1000, $offset = 0, $order = 'cmacro_id DESC', $group = ''){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
			'limit' => $page_size,
			'offset' => $offset,
			'order' => $order,
			'group' => $group
		);
		return $this->findAll($criteria);
	}
}
