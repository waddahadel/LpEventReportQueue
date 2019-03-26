<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\API;

interface DataCaptureRoutinesInterface
{
	public function getOverrides(): array;

	public function collectUserData();

	public function collectUDFData(): array;

	public function collectMemberData();

	public function collectLpPeriod(): array;

	public function collectObjectData();
}