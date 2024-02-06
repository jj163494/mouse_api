<?php

/**
 * This is the model class for table "td_macros".
 *
 * The followings are the available columns in table 'td_macros':
 * @property integer $macro_id
 * @property integer $member_id
 * @property integer $cmacro_id
 * @property string $macro_name
 * @property string $macro_content
 * @property string $chara_image
 * @property integer $created_time
 * @property integer $update_time
 */
class TdMacros extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_macros';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, created_time', 'required'),
			array('member_id, cmacro_id, created_time, update_time', 'numerical', 'integerOnly'=>true),
			array('macro_name', 'length', 'max'=>32),
			array('chara_image', 'length', 'max'=>255),
			array('macro_content', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('macro_id, member_id, cmacro_id, macro_name, macro_content, chara_image, created_time, update_time', 'safe', 'on'=>'search'),
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
			'macro_id' => '宏id',
			'member_id' => '用户id',
			'cmacro_id' => '角色宏id',
			'macro_name' => '宏名称',
			'macro_content' => '宏内容',
			'chara_image' => '角色图像',
			'created_time' => '创建时间',
			'update_time' => '修改时间',
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

		$criteria->compare('macro_id',$this->macro_id);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('cmacro_id',$this->cmacro_id);
		$criteria->compare('macro_name',$this->macro_name,true);
		$criteria->compare('macro_content',$this->macro_content,true);
		$criteria->compare('chara_image',$this->chara_image,true);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('update_time',$this->update_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdMacros the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * 获取宏明细
	 * @param string $condition
	 * @param string $field
	 * @param string $order
	 * @return array|mixed|null
	 */
	public function getMacroInfo($condition = "", $field = "*", $order = "macro_id DESC"){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
			'order' => $order,
		);
		return $this->find($criteria);
	}


	/**
	 * 获取宏列表
	 * @param string $condition
	 * @param string $field
	 * @param int $page_size
	 * @param int $offset
	 * @param string $order
	 * @param string $group
	 * @return array|mixed|null
	 */
	public function getMacroList($condition = "", $field = '*', $page_size = 1000, $offset = 0, $order = 'macro_id DESC', $group = ''){
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
