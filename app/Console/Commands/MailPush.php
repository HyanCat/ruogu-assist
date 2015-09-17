<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MailPush extends Command
{
	protected $signature = 'mail:push';

	protected $description = '推送订阅内容邮件';

	public function __construct()
	{
		parent::__construct();
	}

	public function handle()
	{
		//
	}
}
