<?php

/**
 * This is the model class for table "td_upload_test_data".
 *
 * The followings are the available columns in table 'td_upload_test_data':
 * @property integer $id
 * @property integer $userId
 * @property string $game_name
 * @property integer $game_type
 * @property integer $click_num
 * @property string $move_distance
 * @property integer $max_apm
 * @property integer $avg_apm
 * @property string $apm_detail
 * @property integer $min_heart
 * @property integer $max_heart
 * @property integer $avg_heart
 * @property string $heart_detail
 * @property integer $heart_num
 * @property integer $min_g
 * @property integer $max_g
 * @property integer $avg_g
 * @property string $g_detail
 * @property integer $final_score
 * @property integer $time_long
 * @property integer $created_time
 * @property string $move_distance_detail
 * @property integer $move_num
 * @property string $move_num_detail
 * @property string $mouse_spec
 * @property string $grade
 * @property integer $apm_score
 * @property integer $mental_score
 * @property integer $agi_score
 * @property integer $mc_score
 * @property integer $md_score
 */
class TdUploadTestData extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_upload_test_data';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId', 'required'),
			array('userId, game_type, click_num, max_apm, avg_apm, min_heart, max_heart, avg_heart, heart_num, min_g, max_g, avg_g, final_score, time_long, created_time, move_num, apm_score, mental_score, agi_score, mc_score, md_score', 'numerical', 'integerOnly'=>true),
			array('game_name', 'length', 'max'=>32),
			array('move_distance', 'length', 'max'=>10),
			array('mouse_spec, grade', 'length', 'max'=>16),
			array('apm_detail, heart_detail, g_detail, move_distance_detail, move_num_detail', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userId, game_name, game_type, click_num, move_distance, max_apm, avg_apm, apm_detail, min_heart, max_heart, avg_heart, heart_detail, heart_num, min_g, max_g, avg_g, g_detail, final_score, time_long, created_time, move_distance_detail, move_num, move_num_detail, mouse_spec, grade, apm_score, mental_score, agi_score, mc_score, md_score', 'safe', 'on'=>'search'),
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
			'userId' => '用户id',
			'game_name' => '游戏名称',
			'game_type' => '游戏类型：1-RTS ，2-MOBA，3-FPS，4-其他',
			'click_num' => '点击次数',
			'move_distance' => '移动距离(毫米)',
			'max_apm' => '最高手速',
			'avg_apm' => '平均手速',
			'apm_detail' => '手速apm明细',
			'min_heart' => '最低心率',
			'max_heart' => '最高心率',
			'avg_heart' => '平均心率',
			'heart_detail' => '心率明细',
			'heart_num' => '心跳次数',
			'min_g' => '最小加速度',
			'max_g' => '最大加速度',
			'avg_g' => '平均加速度',
			'g_detail' => '加速度明细',
			'final_score' => '职业指数',
			'time_long' => '游戏时长(秒)',
			'created_time' => '创建时间',
			'move_distance_detail' => '移动距离明细',
			'move_num' => '移动次数',
			'move_num_detail' => '移动次数明细',
			'mouse_spec' => '鼠标规格',
			'grade' => '职业指数评价',
			'apm_score' => '手速得分',
			'mental_score' => '心态得分',
			'agi_score' => '敏捷得分',
			'mc_score' => '移动次数得分',
			'md_score' => '移动距离得分',
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
		$criteria->compare('game_name',$this->game_name,true);
		$criteria->compare('game_type',$this->game_type);
		$criteria->compare('click_num',$this->click_num);
		$criteria->compare('move_distance',$this->move_distance,true);
		$criteria->compare('max_apm',$this->max_apm);
		$criteria->compare('avg_apm',$this->avg_apm);
		$criteria->compare('apm_detail',$this->apm_detail,true);
		$criteria->compare('min_heart',$this->min_heart);
		$criteria->compare('max_heart',$this->max_heart);
		$criteria->compare('avg_heart',$this->avg_heart);
		$criteria->compare('heart_detail',$this->heart_detail,true);
		$criteria->compare('heart_num',$this->heart_num);
		$criteria->compare('min_g',$this->min_g);
		$criteria->compare('max_g',$this->max_g);
		$criteria->compare('avg_g',$this->avg_g);
		$criteria->compare('g_detail',$this->g_detail,true);
		$criteria->compare('final_score',$this->final_score);
		$criteria->compare('time_long',$this->time_long);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('move_distance_detail',$this->move_distance_detail,true);
		$criteria->compare('move_num',$this->move_num);
		$criteria->compare('move_num_detail',$this->move_num_detail,true);
		$criteria->compare('mouse_spec',$this->mouse_spec,true);
		$criteria->compare('grade',$this->grade,true);
		$criteria->compare('apm_score',$this->apm_score);
		$criteria->compare('mental_score',$this->mental_score);
		$criteria->compare('agi_score',$this->agi_score);
		$criteria->compare('mc_score',$this->mc_score);
		$criteria->compare('md_score',$this->md_score);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdUploadTestData the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * 获取测试数据列表
	 * @param string $condition
	 * @param string $field
	 * @param int $page_size
	 * @param int $offset
	 * @param string $order
	 * @param string $group
	 * @return array|mixed|null
	 */
	public function getUploadTestDataList($condition = "", $field = '*', $page_size = 1000, $offset = 0, $order = 'id desc', $group = ''){
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


	/**
	 * 获取测试数据列表明细
	 * @param string $condition
	 * @param string $field
	 * @param string $order
	 * @return array|mixed|null
	 */
	public function getUploadTestDataInfo($condition = "", $field = "*", $order = "id DESC"){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
			'order' => $order
		);
		return $this->find($criteria);
	}
}
