<pre><?php
require('../../models/model.php');
require('../../models/validator.php');

class Test extends Model {
	public $table = 'test';
	public $validation = array(
		'id' => array(
			'blank' => array(
				'rule' => 'blank',
				'on' => array('create'),
				'fix' => true
			)
		),
		'name' => array(
			'lengthhhh' => array(
				'rule' => 'length',
				'params' => array('min' => 1, 'max' => 100)
			),
			'nieco' => array(
				'rule' => 'isSomething',
				'params' => array('eefefef' => 10)
			)
		)
	);
	
	function isSomething($value, array $params) {
//		echo "ceking '$value'";
//		print_r($params);
		return !false;
	}
}


$config = array(
	'service' => 'mysql',
	'host' => 'localhost',
	'db' => 'yamp',
	'user' => 'root',
	'password' => 'heslo'
);
$dsn = "{$config['service']}:dbname={$config['db']};host={$config['host']}";
$pdo = new PDO($dsn, $config['user'], $config['password']);

$m = new Test($pdo);

//print_r($m->
//	set('name', 'jano')->
//	insert()
//);
//
//
//print_r($m->
////	offsetLimit(1,2)->
//	filter('Test.name', 'LIKE', 'd%')->
//	filter('Test.id', '=', NULL)->
//	fetchAll()
//);


//print_r($m->
//	set('name', 'fedor')->
//	filter('Test.id', '=', 7)->
//	update()
//);


$row = $m->filter('Test.id', '=', 6)->fetch();
$row['Test']['name'] = 'zmen22ene';
print_r($row);

print_r($m->save($row));


$data = array(
	'name' => date(DATE_RFC822),
	'age'=> round(rand(10, 100))
);


//print_r($m->create($data));
print_r($m->invalidFields);




?>