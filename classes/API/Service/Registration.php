<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\API\Service;

use QU\LERQ\Model\ProviderModel;
use QU\LERQ\Model\RoutinesModel;

class Registration
{
	const DB_PROVIDER_REG = 'lerq_provider_register';

	/**
	 * @return array
	 */
	public function load()
	{
		return $this->_load();
	}

	/**
	 * @param string $name
	 * @return bool|ProviderModel
	 */
	public function loadByName(string $name)
	{
		$providers = $this->_load();
		/** @var ProviderModel $provider */
		foreach ($providers as $provider) {
			if ($provider->getName() === $name) {
				return $provider;
			}
		}
		return false;
	}

	/**
	 * @param string $namespace
	 * @return bool|ProviderModel
	 */
	public function loadByNamespace(string $namespace)
	{
		$providers = $this->_load();
		/** @var ProviderModel $provider */
		foreach ($providers as $provider) {
			if ($provider->getNamespace() === $namespace) {
				return $provider;
			}
		}
		return false;
	}

	/**
	 * @param string $name
	 * @param string $namespace
	 * @param string $path
	 * @param bool $hasOverrides
	 * @return bool
	 */
	public function create(string $name, string $namespace, string $path, bool $hasOverrides = false)
	{
		$provider = new ProviderModel();
		$provider->setName($name);
		$provider->setNamespace($namespace);
		$provider->setPath($path);
		$provider->setHasOverrides($hasOverrides);

		$routines = new RoutinesModel();
		try {
			// @Todo how do we get the routines overrides object?
		} catch(\Exception $e) {
			global $DIC;

			$DIC->logger()->root()->error($e->getMessage());
			return false;
		}

		$provider->setActiveOverrides($routines);

		return $this->_save($provider);
	}

	/**
	 * @return array
	 */
	public function _load(): array
	{
		global $DIC;

		$query = 'SELECT * FROM ' . self::DB_PROVIDER_REG . ' ';
		$providers = [];

		$res = $DIC->database->query($query);
		while ($row = $DIC->database->fetchAssoc($res)) {
			$provider = new ProviderModel();
			$provider->setName($row['name']);
			$provider->setNamespace($row['namespace']);
			$provider->setPath($row['path']);
			$provider->setHasOverrides($row['has_overrides']);

			$overrides = json_decode($row['active_overrides'], true);
			$routines = new RoutinesModel();
			$routines->setCollectUserData($overrides['collectUserData']);
			$routines->setCollectUDFData($overrides['collectUDFData']);
			$routines->setCollectMemberData($overrides['collectMemberData']);
			$routines->setCollectLpPeriod($overrides['collectLpPeriod']);

			$provider->setActiveOverrides($routines);
			$providers[] = $provider;
			$provider = null;
		}

		return $providers;
	}

	/**
	 * @param ProviderModel $provider
	 * @return bool
	 */
	public function _save(ProviderModel $provider): bool
	{
		global $DIC;

		$res = $DIC->database()->insert(self::DB_PROVIDER_REG,
			array(
				'name'             => array('text', $provider->getName()),
				'namespace'        => array('text', $provider->getNamespace()),
				'path'             => array('text', $provider->getPath()),
				'has_overrides'     => array('text', $provider->getHasOverrides()),
				'active_overrides'  => array('text', $provider->getActiveOverrides()),
			)
		);

		return ($res === false);
	}
}