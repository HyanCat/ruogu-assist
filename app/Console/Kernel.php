<?php

namespace App\Console;

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
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('mail:ad 800 --everytime=20')->dailyAt('18:00:00')->sendOutputTo(storage_path('schedule') . '/admail_' . date('Ymd') . '.log');
		$schedule->command('mail:check --clear')->dailyAt('20:00:00')->sendOutputTo(storage_path('schedule') . '/checkmail_' . date('Ymd') . '.log');
	}
}
