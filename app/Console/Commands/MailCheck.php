<?php

namespace App\Console\Commands;

use App\Models\CrawQqUser;
use Carbon\Carbon;
use Hyancat\Sendcloud\SendCloudApiInterface;
use Illuminate\Console\Command;

class MailCheck extends Command
{
	protected $signature = 'mail:check {--clear}';

	protected $description = '检查无效邮件，并删除之';

	protected $api;

	public function __construct(SendCloudApiInterface $api)
	{
		parent::__construct();

		$this->api = $api;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$clear    = $this->option('clear');
		$response = $this->api->bounces('2015-09-01', Carbon::now()->toDateString(), 200);
		if (property_exists($response, 'message') && property_exists($response, 'bounces') && $response->message === 'success') {
			$bounces = $response->bounces;
			foreach ($bounces as $bounce) {
				$qq   = explode('@', $bounce->email)[0];
				$user = CrawQqUser::where('qq', $qq)->first();
				$this->info('Email: ' . $bounce->email);
				if (! is_null($user)) {
					$this->info('找到无效用户并删除：' . $qq);
					$user->delete();
				}
				// 如果 --clear 则清除远程记录
				if ($clear) {
					$this->api->deleteBounce($bounce->email);
					$this->info('清除无效用户：' . $bounce->email);
				}
			}
		}
	}
}
