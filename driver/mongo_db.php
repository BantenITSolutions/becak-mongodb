<?php defined('SYS') or exit('Access Denied!');
/* 
* Becak MVC Framework version 1.0 
*
* File		: mongo_db.php
* Directory	: system/database/driver
* Author	: Gede Lumbung
* Description 	: driver mongodb
*/
class mongo_db {
	private $conn;
	private $db;
	private $config;

	private $where_data = array();
	private $update_data = array();

	private $select_data = array();
	private $sort_data = array();
	private $limit_data = 500000;
	private $offset_data = 0;

	public function __construct($config){
		$this->config 	= $config;
	}
	public function connect() {
		extract($this->config);
		if(isset($this->conn)) return $this->conn;
		try {
			$this->conn = new MongoClient($server);
			$this->db = $this->conn->selectDB($database);
			
		} catch (MongoConnectionException $e) {
			show_error('Unable to connect to MongoDB server.');
		} catch (MongoException $e) {
			show_error('MongoDB Error: ' . $e->getMessage());
		}
	}
	public function disconnect(){
		if(isset($this->conn)) {
			$this->conn->close();
		}
	}
	public function select($fields,$sort_data=array(),$limit_data=500000,$offset_data=0,$where_data=array())
	{
		$this->sort_data = $sort_data;
		$this->limit_data = $limit_data;
		$this->offset_data = $offset_data;
		$this->where_data = $where_data;

		if (!is_array($fields))
	 	{
	 		$fields = array();
	 	}

	 	if (!empty($fields))
	 	{
	 		foreach ($fields as $col)
	 		{
	 			$this->select_data[$col] = 1;
	 		}
	 	}

	 	foreach ($sort_data as $coloumn => $vall)
		{
			if ($vall == -1 || $vall === FALSE || strtolower($vall) == 'asc')
			{
				$this->sort_data[$coloumn] = 1; 
			}
			else
			{
				$this->sort_data[$coloumn] = -1;
			}
		}

		if ($limit_data !== NULL && is_numeric($limit_data) && $limit_data >= 1)
		{
			$this->limit_data = (int) $limit_data;
		}

		if ($offset_data !== NULL && is_numeric($offset_data) && $offset_data >= 1)
		{
			$this->offset_data = (int) $offset_data;
		}

		if (is_array($where_data))
		{
			foreach ($where_data as $key => $vall)
			{
				$this->where_data[$key] = $vall;
			}
		}

	 	return ($this);
	}

	public function results($collection, $type = 'object')
	{
		if (empty($collection))
	 	{
	 		show_error("In order to retreive documents from MongoDB, a collection name must be passed", 500);
	 	}

		$documents = $this->db->{$collection}->find($this->where_data, $this->select_data)->limit((int) $this->limit_data)->skip((int) $this->offset_data)->sort($this->sort_data);

	 	$returns = array();

	 	while ($documents->hasNext())
		{
			if ($type == 'object')
			{
				$returns[] = (object) $documents->getNext();	
			}
			else 
			{
				$returns[] = (array) $documents->getNext();
			}
		}

	 	if ($type == 'object')
		{
			return (object)$returns;
		}

		else
		{
			return $returns;
		}
	}

	public function insert($collection="",$value=array())
	{
		if (empty($collection))
		{
			echo "No Mongo collection selected to insert into";
			return false;
		}

		if (count($value) == 0 || !is_array($value))
		{
			echo "Nothing to insert into Mongo collection or insert is not an array";
			return false;
		}

		try
		{
			$this->db->$collection->insert($value);
			if (isset($value['_id']))
			{
				return ($value['_id']);
			}
			else
			{
				return (FALSE);
			}
		}
		catch (MongoCursorException $e)
		{
			echo "Insert of data into MongoDB failed: {$e->getMessage()}";
		}
	}

	public function delete($collection="", $arr_keys = array())
	{
		if (empty($collection))
		{
			echo "No Mongo collection specified to remove index from";
			return false;
		}

		if (empty($arr_keys) || ! is_array($arr_keys))
		{
			echo "Index could not be removed from MongoDB Collection because no keys were specified";
			return false;
		}

		if ($this->db->$collection->remove($arr_keys) == TRUE)
		{
			return true;
		}
		else
		{
			echo "An error occured when trying to remove an index from MongoDB Collection";
		}
	}

	public function update($collection = "", $data = array(), $keys = array())
	{
		if (empty($collection))
		{
			echo "No Mongo collection selected to update";
		}

		if (is_array($data) && count($data) > 0)
		{
			$this->update_data = $data;
		}

		if (count($this->update_data) == 0)
		{
			echo "Nothing to update in Mongo collection or update is not an array";	
		}

		try
		{
			$this->db->$collection->update($keys, $this->update_data);
			return (TRUE);
		}
		catch (MongoCursorException $e)
		{
			echo "Update of data into MongoDB failed: {$e->getMessage()}";
		}
	}

}//end class
?>
