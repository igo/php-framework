<?php

class Framework_Models_Model {

	private $pdo;
	private $query;
	protected $modelFactory;
	public $table;
	public $model;
	public $validation = array();
	public $invalidFields = array();
	public $relations = array();
	public $primaryKeys = array('id');

	public function __construct(PDO $pdo, $modelFactory) {
		$this->pdo = $pdo;
		if (!$this->model)
			$this->model = substr(get_class($this), strrpos(get_class($this), '_') + 1);
		$this->reset();
		$this->modelFactory = $modelFactory;
	}

	private function buildWherePart() {
		$wheres = array();
		foreach ($this->query['where'] as $where) {
			$cond = $where['field'];
			if ($where['value'] === null) {
				$wheres[] = "{$where['field']} IS NULL";
			} else {
				$wheres[] = "{$where['field']} {$where['op']} " . $this->pdo->quote($where['value']);
			}
		}
		return " WHERE " . implode(' AND ', $wheres);
	}

	private function buildLimitPart() {
		return " LIMIT {$this->query['limit']['offset']}, {$this->query['limit']['count']}";
	}

	private function buildSetPart() {
		$sets = array();
		foreach ($this->query['set'] as $set) {
			$wheres[] = "{$set['field']} = " . $this->pdo->quote($set['value']);
		}
		return " SET " . implode(', ', $wheres);
	}

	private function buildFieldsListPart() {
		$fields = array();
		foreach ($this->query['set'] as $set) {
			$fields[] = $set['field'];
		}
		return '(' . implode(', ', $fields). ')';
	}

	private function buildValuesListPart() {
		$values = array();
		foreach ($this->query['set'] as $set) {
			$values[] = $this->pdo->quote($set['value']);
		}
		return '(' . implode(', ', $values). ')';
	}

	private function buildSelectQuery() {
		//print_r($this->query);
		// SELECT
		$q = "SELECT " . implode(', ', $this->query['select']);

		// FROM
		$tables = array();
		foreach ($this->query['from'] as $table) {
			$tables[] = "{$table['table']} AS {$table['alias']}";
		}
		$q .= " FROM " . implode(', ', $tables);

		// WHERE
		if (isset($this->query['where'])) {
			$q .= $this->buildWherePart();
		}

		// LIMIT
		if (isset($this->query['limit'])) {
			$q .= $this->buildLimitPart();
		}

		return $q;
	}

	private function buildInsertQuery() {
		//print_r($this->query);
		// INSERT
		$q = "INSERT INTO {$this->table} ";

		// fields list
		$q .= $this->buildFieldsListPart();

		// VALUES
		$q .= ' VALUES '. $this->buildValuesListPart();

		return $q;
	}

	private function buildUpdateQuery() {
		//print_r($this->query);
		// UPDATE
		$q = "UPDATE " . $this->table;

		// SET
		$q .= $this->buildSetPart();

		// WHERE
		if (isset($this->query['where'])) {
			$q .= $this->buildWherePart();
		}

		return $q;
	}

	private function buildDeleteQuery() {
		//print_r($this->query);
		// DELETE
		$q = "DELETE FROM " . $this->table;

		// WHERE
		if (isset($this->query['where'])) {
			$q .= $this->buildWherePart();
		}

		return $q;
	}

	public function fetch() {
		$this->limit(1);
		$result = $this->fetchAll();
		if (!empty($result))
			return $result[0];
		return $result;
	}

	public function fetchAll() {
		$this->query += array('select' => array('*'));
		$this->query += array('from' => array(
			array(
				'table' => $this->table,
				'alias' => $this->model
			)
		));
		$q = $this->buildSelectQuery();
		echo "Executing: $q\n";
		$q = $this->pdo->query($q);

		$out = array();

		// create model that are binded
		$relationObj = array();
		// models binded 1 to N
		if (!empty($this->query['1-to-N'])) {
			foreach ($this->query['1-to-N'] as $relation) {
				$relationObj[$relation['model']] = $this->modelFactory->getModel($relation['model']);
			}
		}
		// models binded N to 1
		if (!empty($this->query['N-to-1'])) {
			foreach ($this->query['N-to-1'] as $relation) {
				if (!isset($relationObj[$relation['model']]))
					$relationObj[$relation['model']] = $this->modelFactory->getModel($relation['model']);
			}
		}
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$out[] = array();
			$size = count($out);
			if (!empty($this->query['N-to-1'])) {
				foreach ($this->query['N-to-1'] as $relation) {
					$rel = $relationObj[$relation['model']];
					$rel->filter($rel->primaryKeys[0], '=', $row[$relation['fk']]);
					$out[$size - 1] += $rel->fetch();
				}
			}
			$out[$size - 1][$this->model] = $row;
		}
		$this->reset();
		return $out;
	}

	public function insert() {
		$q = $this->buildInsertQuery();
		echo "Executing: $q\n";
		$return = $this->pdo->exec($q);
		var_dump($return);
		$this->reset();
		if ($return === 1) {
			return array('status' => 'ok');
		}
		return array('status' => 'error');
	}

	public function update() {
		$q = $this->buildUpdateQuery();
		echo "Executing: $q\n";
		var_dump($this->pdo->exec($q));
		$this->reset();
	}

	public function delete() {
		$q = $this->buildDeleteQuery();
		echo "Executing: $q\n";
		var_dump($this->pdo->exec($q));
		$this->reset();
	}

	public function create($object) {
		if (!isset($object[$this->model])) { // a[Model][field]
			$object[$this->model] = $object;
		}
		$this->multiSet($object[$this->model]);
		if ($this->validate($object[$this->model], 'create')) {
			return $this->insert();
		} else {
			return false;
		}
	}

	public function save($object) {
		if (!isset($object[$this->model])) { // a[Model][field]
			$object[$this->model] = $object;
		}
		$this->multiSet($object[$this->model]);
		foreach ($this->primaryKeys as $pk) {
			$this->filter($pk, '=', $object[$this->model][$pk]);
		}
		if ($this->validate($object[$this->model], 'update')) {
			echo "saving";print_r($this->query);
			return $this->update();
		} else {
			echo "not valid";print_r($this->invalidFields);
			return false;
		}
	}

	public function set($field, $value) {
		$this->query['set'][] = compact('field', 'value');
		return $this;
	}

	public function multiSet(array $fields) {
		foreach ($fields as $field => $value) {
			$this->set($field, $value);
		}
		return $this;
	}

	public function limit($count) {
		$this->offsetLimit(0, $count);
		return $this;
	}

	public function offsetLimit($offset, $count) {
		$this->query['limit'] = compact('offset', 'count');
		return $this;
	}

	public function filter($field, $op, $value) {
		$this->query['where'][] = compact('field', 'op', 'value');
		return $this;
	}

	public function reset() {
		$this->query = array ();
		return $this;
	}

	/**
	 * Include relational data in result
	 * @param str $relation
	 */
	public function bind($relationName) {
		$relation = $this->relations[$relationName];
		if ($relation['type'] == '1-to-N') {
			$this->query['1-to-N'][] = array(
				'name' => $relationName,
				'model' => $relation['model'],
				'fk' => $fk
			);
		} else if ($relation['type'] == 'N-to-1') {
			$this->query['N-to-1'][] = array(
				'name' => $relationName,
				'model' => $relation['model'],
				'fk' => $relation['fk']
			);
		}
		return $this;
	}

	public function validate($data, $action = null) {
		$this->invalidateField = array();
		foreach ($this->validation as $field => $validations) {
			foreach ($validations as $validationName => $validation) {
				if (isset($validation['on']) && !in_array($action, $validation['on']))
					continue;
				if (!isset($validation['params']))
					$validation['params'] = array();
				if (method_exists('Framework_Models_Validator', $validation['rule'])) {
					$valid = @Framework_Models_Validator::validate($data[$field], $validation['rule'], $validation['params']);
				} else {
					$valid = $this->$validation['rule']($data[$field], $validation['params']);
				}
//				var_dump($valid);
				if ($valid === false) {
					$this->invalidateField($field, $validationName);
				}
			}
		}
		return empty($this->invalidateField);
	}

	public function invalidateField($field, $rule = 'anonymous') {
		$this->invalidFields[$field][] = $rule;
	}

}

?>