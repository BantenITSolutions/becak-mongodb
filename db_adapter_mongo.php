<?php defined('SYS') or exit('Access Denied!');
/* 
* Becak MVC Framework version 1.0 
*
* File		: db_adapter_mongo.php
* Directory	: system/database
* Author	: Gede Lumbung
* Description 	: fungsi common (CRUD) untuk database MongoDB
*/
class db_adapter_mongo {
	private $driver = null;
	private $collection = '';
	private $fields = array();

	private $where_data = array();
	private $select_data = array();
	private $sort_data = array();
	private $limit_data = 500000;
	private $offset_data = 0;

	public function __construct($driver){
		$this->driver = $driver;
		$this->driver->connect();
	}
	public function __destruct(){
		$this->driver->disconnect();
	}
	public function select($collection,$fields=array(),$sort_data=array(),$limit_data=500000,$offset_data=0,$where_data=array())
	{
		$this->collection = $collection;
		$this->fields = $fields;
		$this->sort_data = $sort_data;
		$this->limit_data = $limit_data;
		$this->offset_data = $offset_data;
		$this->where_data = $where_data;
	}
	public function fetch_array(){
		return $this->driver->select($this->fields,$this->sort_data,$this->limit_data,$this->offset_data,$this->where_data)->results($this->collection, 'array');
	}
	public function fetch_object(){
		return $this->driver->select($this->fields,$this->sort_data,$this->limit_data,$this->offset_data,$this->where_data)->results($this->collection, 'object');
	}
	public function insert($collection,$value=array()){
		return $this->driver->insert($collection,$value);
	}
	public function delete($collection,$keys=array()){
		return $this->driver->delete($collection,$keys);
	}
	public function update($collection,$data=array(),$keys=array()){
		return $this->driver->update($collection,$data,$keys);
	}
}
?>
