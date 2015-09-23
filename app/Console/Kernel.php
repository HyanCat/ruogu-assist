<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	protected $commands = [
		Commands\Inspire::class,
		Commands\MailAd::class,
		Commands\MailPush::class,
		Commands\MailCheck::class,
		Commands\CrawQQUser::class,
	];

	protected function schedule(Schedule $schedule)
	{
		if (Carbon::now()->isWeekend()) {
			// 周末下午 1 点到 5 点，每个小时 200
			if (Carbon::now()->hour >= 13 && Carbon::now()->hour <= 17) {
				$schedule->command('mail:ad 200 --everytime=20')->hourly()->sendOutputTo($this->storageFile());
			}
		}
		// 每天 18 点发 500
		$schedule->command('mail:ad 500 --everytime=20')->dailyAt('18:00')->sendOutputTo($this->storageFile());
		// 每天 19 点发 800
		$schedule->command('mail:ad 800 --everytime=20')->dailyAt('19:00')->sendOutputTo($this->storageFile());
		// 每天 20 点发剩下的
		$schedule->command('mail:ad 1000 --everytime=20')->dailyAt('20:00')->sendOutputTo($this->storageFile());

		// 每天 23 点开始，清除无效邮件
		if (Carbon::now()->hour == 23) {
			$schedule->command('mail:check --clear')->everyTenMinutes()->sendOutputTo($this->storageFile('checkmail'));
		}
	}

	private function storageFile($prefix = 'admail')
	{
		return storage_path('schedule') . '/' . $prefix . '_' . Carbon::now()->format('Ymd_Hi') . '.log';
	}
}
