<?php
class class_suppliers {

public function getOne($nameOrUID = null) {
	global $db;
	
	$supplier = $db->where("name", $nameOrUID);
	$supplier = $db->orWhere ("uid", $nameOrUID);
	$supplier = $db->getOne("suppliers");
	
	return $supplier;
}

public function update($name = null, $data = null) {
	global $db;
	
	$supplier = $this->getOne($name);
	if (isset($supplier)) {
		$db->where ('name', $name);
		$id = $db->escape($db->update('suppliers', $data));
	
		$log = new class_logs;
		$log->insert("update", $db->getLastQuery());
	
		if (!$id) {
			echo 'Log failed: ' . $db->getLastError();
		}
	} else {
		$id = $db->escape($db->insert('suppliers', $data));
		
		$log = new class_logs;
		$log->insert("create", $db->getLastQuery());
	
		if (!$id) {
			echo 'Log failed: ' . $db->getLastError();
		}
	}
	
}
} //end CLASS
?>