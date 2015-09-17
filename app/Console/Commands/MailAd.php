<?php

namespace App\Console\Commands;

use App\Models\CrawQqUser;
use Carbon\Carbon;
use Hyancat\Sendcloud\SendCloudFacade as SendCloud;
use Hyancat\Sendcloud\SendCloudMessage;
use Illuminate\Console\Command;

class MailAd extends Command
{
	protected $signature = 'mail:ad
	 						{count? : 发送数量}
	 						{--everytime= : 每次发送人数}';

	protected $description = '发送广告邮件';

	public function __construct()
	{
		parent::__construct();
	}

	public function handle()
	{
		$count     = intval($this->argument('count'));
		$everytime = intval($this->option('everytime'));
		$this->info('[' . Carbon::now() . ']');
		$this->info('Begin count: ' . $count . ' everytime: ' . $everytime);

		$index = 0;
		while ($index < $count) {
			if ($index + $everytime > $count) {
				$everytime = $count - $index;
			}
			$this->pushEmails($everytime);
			$index += $everytime;
		}
	}

	protected function pushEmails($count)
	{
		$qqUsers = CrawQqUser::where('status', 0)->take($count)->get();
		$mails   = [];
		foreach ($qqUsers as $user) {
			$user->status = 1;
			$user->save();
			$email   = $user->qq . '@qq.com';
			$mails[] = $email;
		}
		if (empty($mails))
			return;
		$this->info('Push to: ' . implode("\t", $mails));
		SendCloud::sendTemplate('ruogu_invite_to_register', [], function (SendCloudMessage $message) use ($mails) {
			$message->to($mails)->subject('若古社区诚邀您入驻');
		})->success(function ($response) use ($qqUsers) {
			foreach ($qqUsers as $user) {
				$user->status = 2;
				$user->count++;
				$user->save();
			}
		})->failure(function ($response, $error) use ($qqUsers) {
			foreach ($qqUsers as $user) {
				$user->status = 3;
				$user->save();
			}
			$this->error($error->message);
		});
	}
}
