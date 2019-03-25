<#1>
<?php
if(!$ilDB->tableExists('lerq_queue'))
{
	$ilDB->createTable('lerq_queue', [
		'id' => [
			'type'     => 'integer',
			'length'   => 4,
			'notnull' => true,
			'default' => 0
		],
		'timestamp' => [
			'type'     => 'timestamp',
			'notnull' => true,
			'default' => ''
		],
		'event' => [
			'type' => 'text',
			'notnull' => true,
		],
		'event_type' => [
			'type' => 'text',
			'notnull' => true,
		],
		'progress' => [
			'type' => 'text',
			'notnull' => false,
		],
		'assignment' => [
			'type' => 'text',
			'notnull' => false,
		],
		'course_start' => [
			'type'     => 'timestamp',
			'notnull' => false,
			'default' => ''
		],
		'course_end' => [
			'type'     => 'timestamp',
			'notnull' => false,
			'default' => ''
		],
		'user_data' => [
			'type' => 'text',
			'notnull' => true,
		],
		'obj_data' => [
			'type' => 'text',
			'notnull' => true,
		],
		'mem_data' => [
			'type' => 'text',
			'notnull' => true,
		],
	]);
	$ilDB->addPrimaryKey('lerq_queue', array('id'));
	$ilDB->createSequence('lerq_queue');
}

if(!$ilDB->tableExists('lerq_provider_register'))
{
	$ilDB->createTable('lerq_provider_register', [
		'id' => [
			'type'     => 'integer',
			'length'   => 4,
			'notnull' => true,
			'default' => 0
		],
		'name' => [
			'type' => 'text',
			'notnull' => true,
		],
		'namespace' => [
			'type' => 'text',
			'notnull' => true,
		],
		'path' => [
			'type' => 'text',
			'notnull' => true,
		],
		'has_overrides' => [
			'type'     => 'integer',
			'length'   => 1,
			'notnull' => true,
			'default' => 0
		],
		'active_overrides' => [
			'type' => 'text',
			'notnull' => true,
		],
		'created_at' => [
			'type'     => 'timestamp',
			'notnull' => true,
			'default' => ''
		],
		'updated_at' => [
			'type'     => 'timestamp',
			'notnull' => false,
			'default' => ''
		],
	]);
	$ilDB->addPrimaryKey('lerq_provider_register', array('id'));
	$ilDB->createSequence('lerq_provider_register');
}
?>