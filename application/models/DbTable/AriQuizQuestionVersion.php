<?php

class Application_Model_DbTable_AriQuizQuestionVersion extends Zend_Db_Table_Abstract
{
//local
    protected $_name = 'quiz-de.quiz_ariquizquestionversion';
    //server
//    protected $_name = 's485825db2.toefl_ariquizquestionversion';

     /**
     * 获取一个页面
     *
     * 根据条件参数获取一个页面
     *
     * @param unknown_type $where            
     */
    public function getQuestions($where = array(), $order = null)
    {
    	$select = $this->select();
            print_r($where);
    	if (count($where) > 0){
    		foreach ($where as $key=>$value){
    			$select->where($key.'>= ?', $value);
    		}
    	}
    	if ($order){
    		$select->order($order);
    	}
    	$result = $this->fetchAll($select);
    	if ($result){
    		return $result;
    	}
    	else{
    		return null;
    	}
    }

    // gets news pages
    public function getQuestion($where = array(), $order = null, $limit = null, $paginator = TRUE) {
        $select = $this->select();
        if (is_string($where)) {
            $select->where($where);
        }
        if (is_array($where) & count($where) > 0) {
            foreach ($where as $key => $value) {
                $select->where($key . ' = ?', $value);
            }
        }
        if ($order) {
            $select->order($order);
        }
        if ($limit) {
            $select->limit($limit);
        }
        if ($paginator == FALSE) {
            $result = $this->fetchAll($select);
        } else {
            $result = new Zend_Paginator_Adapter_DbTableSelect($select);
        }
        if ($result->count() > 0) {
            return $result;
        } else {
            return null;
        }
    }

    /**
     * 创建页面
     * 
     * 提交数据是数组格式，循环后键名为数据表字段，键值为提交值
     * 返回刚创建页面的id
     * 
     * @param array $data
     * @throws Zend_Exception
     */
    public function createPage($data = array()) {
        $row = $this->createRow();
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $row->$key = $value;
            }
            $row->save();
            return $row->id;
        } else {
            throw new Zend_Exception('提交数据出错！');
        }
    }

    /**
     * 更新页面
     * 
     * 提交数据是数组格式，循环后键名为数据表字段，键值为提交值
     * 返回更新页面的id
     * 
     * @param int $id
     * @param array $data
     * @throws Zend_Exception
     */
    public function updateQuestion($id, $data = array()) {
        $row = $this->find($id)->current();
        if ($row) {
            if (count($data) > 0) {
                foreach ($data as $key => $value) {
                    $row->$key = $value;
                }
                $row->save();
                return $id;
            } else {
                throw new Zend_Exception('提交数据出错！');
            }
        } else {
            throw new Zend_Exception('更新数据出错！没有找到该页面！');
        }
    }

    /**
     * 删除页面
     * 
     * @param int $id
     */
    public function deletePage($id) {
        $row = $this->find($id)->current();
        if ($row) {
            $row->delete();
        } else {
            throw new Zend_Exception('删除数据出错！没有找到该页面。');
        }
    }

}

