<?php

/**
 * This is the model class for table "td_computer".
 *
 * The followings are the available columns in table 'td_computer':
 * @property integer $pc_id
 * @property string $mac_address
 * @property integer $created_time
 * @property integer $update_time
 *
 * The followings are the available model relations:
 * @property TdComputerHistory[] $tdComputerHistories
 * @property TdGameHistory[] $tdGameHistories
 */
class TdComputer extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_computer';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('mac_address, created_time', 'required'),
			array('created_time, update_time', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('pc_id, mac_address, created_time, update_time', 'safe', 'on'=>'search'),
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
			'tdComputerHistories' => array(self::HAS_MANY, 'TdComputerHistory', 'pc_id'),
			'tdGameHistories' => array(self::HAS_MANY, 'TdGameHistory', 'pc_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'pc_id' => '主机id',
			'mac_address' => '主机标识',
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

		$criteria->compare('pc_id',$this->pc_id);
		$criteria->compare('mac_address',$this->mac_address,true);
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
	 * @return TdComputer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * 获取主机信息明细
	 * @param string $condition
	 * @param string $field
	 * @return array|mixed|null
	 */
	public function getComputerInfo($condition = "", $field = "*"){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
		);
		return $this->find($criteria);
	}

}
