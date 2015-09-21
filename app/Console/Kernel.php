<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		Commands\Inspire::class,
		Commands\MailAd::class,
		Commands\MailPush::class,
		Commands\MailCheck::class,
		Commands\CrawQQUser::class,
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		// 每天下午 1 点到 5 点，每个小时 100
		if (Carbon::now()->hour >= 13 && Carbon::now()->hour <= 17) {
			$schedule->command('mail:ad 100 --everytime=20')->hourly()->sendOutputTo(storage_path('schedule') . '/admail_' . Carbon::now()->format('Ymd_Hi') . '.log');
		}
		// 每天 18 点发送剩下的
		$schedule->command('mail:ad 800 --everytime=20')->dailyAt('18:00')->sendOutputTo(storage_path('schedule') . '/admail_' . Carbon::now()->format('Ymd_Hi') . '.log');
		// 每天 23 点开始，清除无效邮件
		if (Carbon::now()->hour >= 23) {
			$schedule->command('mail:check --clear')->everyTenMinutes()->sendOutputTo(storage_path('schedule') . '/checkmail_' . Carbon::now()->format('Ymd_Hi') . '.log');
		}
	}
}
