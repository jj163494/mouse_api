<?php

/**
 * This is the model class for table "td_soft_version".
 *
 * The followings are the available columns in table 'td_soft_version':
 * @property integer $id
 * @property string $version
 * @property string $desc
 * @property string $descEN
 * @property string $descZHT
 * @property integer $device_type
 * @property integer $soft_type
 * @property string $download_path
 * @property integer $size
 * @property integer $created_time
 */
class TdSoftVersion extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_soft_version';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('device_type, soft_type, size, created_time', 'numerical', 'integerOnly'=>true),
			array('version', 'length', 'max'=>24),
			array('download_path', 'length', 'max'=>128),
			array('desc, descEN, descZHT', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, version, desc, descEN, descZHT, device_type, soft_type, download_path, size, created_time', 'safe', 'on'=>'search'),
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
			'version' => 'Version',
			'desc' => 'Desc',
			'descEN' => 'DescEN',
			'descZHT' => 'DescZHT',
			'device_type' => 'Device Type',
			'soft_type' => 'Soft Type',
			'download_path' => 'Download Path',
			'size' => 'Size',
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
		$criteria->compare('version',$this->version,true);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('descEN',$this->descEN,true);
		$criteria->compare('descZHT',$this->descZHT,true);
		$criteria->compare('device_type',$this->device_type);
		$criteria->compare('soft_type',$this->soft_type);
		$criteria->compare('download_path',$this->download_path,true);
		$criteria->compare('size',$this->size);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdSoftVersion the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
