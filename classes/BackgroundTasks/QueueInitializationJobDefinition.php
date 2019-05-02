<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\BackgroundTasks;

class QueueInitializationJobDefinition extends AbstractJobDefinition
{
	// table name for Job
	const JOB_TABLE = 'lerq_bgtask_init';
}