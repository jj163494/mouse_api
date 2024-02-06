<?php

/**
 * This is the model class for table "td_setting".
 *
 * The followings are the available columns in table 'td_setting':
 * @property integer $id
 * @property integer $main_key
 * @property string $sub_key
 * @property string $value
 * @property string $remark
 */
class TdSetting extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_setting';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('main_key', 'numerical', 'integerOnly'=>true),
			array('sub_key, value', 'length', 'max'=>16),
			array('remark', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, main_key, sub_key, value, remark', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'main_key' => 'Main Key',
			'sub_key' => 'Sub Key',
			'value' => 'Value',
			'remark' => 'Remark',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('main_key',$this->main_key);
		$criteria->compare('sub_key',$this->sub_key,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('remark',$this->remark,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdSetting the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
