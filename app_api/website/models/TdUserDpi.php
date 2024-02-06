<?php

/**
 * This is the model class for table "td_user_dpi".
 *
 * The followings are the available columns in table 'td_user_dpi':
 * @property integer $id
 * @property integer $userId
 * @property integer $dpi_id
 * @property integer $custom_dpi_index
 * @property string $custom_dpi_name
 * @property integer $custom_dpi_value
 * @property integer $created_time
 */
class TdUserDpi extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_user_dpi';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId, dpi_id', 'required'),
			array('userId, dpi_id, custom_dpi_index, custom_dpi_value, created_time', 'numerical', 'integerOnly'=>true),
			array('custom_dpi_name', 'length', 'max'=>48),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userId, dpi_id, custom_dpi_index, custom_dpi_name, custom_dpi_value, created_time', 'safe', 'on'=>'search'),
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
			'userId' => 'User',
			'dpi_id' => 'Dpi',
			'custom_dpi_index' => 'Custom Dpi Index',
			'custom_dpi_name' => 'Custom Dpi Name',
			'custom_dpi_value' => 'Custom Dpi Value',
			'created_time' => 'Created Time',
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
		$criteria->compare('userId',$this->userId);
		$criteria->compare('dpi_id',$this->dpi_id);
		$criteria->compare('custom_dpi_index',$this->custom_dpi_index);
		$criteria->compare('custom_dpi_name',$this->custom_dpi_name,true);
		$criteria->compare('custom_dpi_value',$this->custom_dpi_value);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdUserDpi the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
