<?php

namespace App\Console\Commands;

use App\Models\CrawQqUser as QQUser;
use App\Services\QQGroupApi;
use Illuminate\Console\Command;

class CrawQQUser extends Command
{
	protected $signature = 'craw:qq';

	protected $description = '抓取 QQ 用户';

	public function __construct()
	{
		parent::__construct();
	}

	public function handle()
	{
		$qqUsers = config('craw.users');

		foreach ($qqUsers as $user) {
			$groupApi = new QQGroupApi($user['uin'], $user['skey']);
			if (empty($user['groups'])) {
				$this->crawAllGroupMembers($groupApi);
			}
			else {
				$this->crawSomeGroupMembers($groupApi, $user['groups']);
			}
		}
	}

	private function crawAllGroupMembers($groupApi)
	{
		$groups = $groupApi->getUsersGroups();
		$groups = $this->fetch($groups, 'gid');
		$this->crawSomeGroupMembers($groupApi, $groups);
	}

	private function crawSomeGroupMembers($groupApi, $groups)
	{
		foreach ($groups as $groupId) {
			if (empty($groupId))
				continue;
			$members = $groupApi->getGroupMembers($groupId);
			if (count($members) > 0) {
				foreach ($members as $aMember) {
					if (false === $this->checkUser($aMember['qq'])) {
						$this->saveUser($groupApi->getUin(), $groupId, $aMember['qq'], $aMember['name']);
						$this->info('Save: ' . $aMember['qq']);
					}
					else {
						$this->info('Exist: ' . $aMember['qq']);
					}
				}
			}
			else {
				$this->error('Error: ' . $groupId);
			}
		}
	}

	private function checkUser($qq)
	{
		$findUser = QQUser::withTrashed()->where('qq', $qq)->first();

		return ! is_null($findUser);
	}

	function saveUser($uin, $gid, $qq, $name)
	{
		return QQUser::create(compact('uin', 'gid', 'qq', 'name'));
	}


	private function fetch(array $array, $column)
	{
		$result = [];
		foreach ($array as $item) {
			if (is_array($item) && array_key_exists($column, $item)) {
				$result[] = $item[$column];
			}
		}

		return $result;
	}
}
