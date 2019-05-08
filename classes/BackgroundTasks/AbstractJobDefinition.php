<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\BackgroundTasks;

class AbstractJobDefinition
{
	// task was not started yet
	const JOB_STATE_INIT = 'not started';
	// task was started but is not running yet
	const JOB_STATE_STARTED = 'started';
	// task is currently running
	const JOB_STATE_RUNNING = 'running';
	// task was stopped manually or by timeout
	const JOB_STATE_STOPPED = 'stopped';
	// task has finished
	const JOB_STATE_FINISHED = 'finished';
	// task has stopped because something failed
	const JOB_STATE_FAILED = 'failed';

	// job has not started yet
	const JOB_RETURN_INIT = 100;
	// job has run successful
	const JOB_RETURN_SUCCESS = 200;
	// job is already running
	const JOB_RETURN_ALREADY_RUNNING = 201;
	// job is locked
	const JOB_RETURN_LOCKED = 202;
	// job has stopped i.e. by timeout
	const JOB_RETURN_STOPPED = 203;
	// job has failed
	const JOB_RETURN_FAILED = 400;
}