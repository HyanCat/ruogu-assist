<?php

/**
 * QQGroupApi.php
 * ruogu-assist
 *
 * Created by HyanCat on 15/9/18.
 * Copyright (C) 2015 HyanCat. All rights reserved.
 */
namespace App\Services;

use App\Contracts\QQGroupApiInterface;
use Buzz\Browser;

class QQGroupApi implements QQGroupApiInterface
{
	const UserAgent = 'Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.59 QQ/7.6.15742.201 Safari/537.36';
	const GroupListUrl = 'http://qun.qzone.qq.com/cgi-bin/get_group_list?uin=%d&g_tk=%d&random=%lf';
	const MemberListUrl = 'http://qinfo.clt.qq.com/cgi-bin/qun_info/get_group_members_new?gc=%d&bkn=%d&src=qinfo_v3';

	protected $uin;
	protected $skey;
	protected $bkn;
	protected $gtk;

	public function __construct($uin, $skey)
	{
		$this->uin  = $uin;
		$this->skey = $skey;
		$this->bkn  = $this->getBkn($this->skey);
		$this->gtk  = $this->getBkn($this->skey);
	}

	public function getUsersGroups()
	{
		$groups   = [];
		$url      = sprintf(self::GroupListUrl, $this->uin, $this->gtk, rand(1, 96) / 97);
		$browser  = new Browser();
		$response = $browser->get($url, [
			'Cookie'     => $this->buildCookie(),
			'Referer'    => 'http://qun.qzone.qq.com/group',
			'User-Agent' => self::UserAgent,
		]);

		if ($response->isOk()) {
			$content = $response->getContent();
			preg_match('/_Callback\((.*)\);/', $content, $matches);
			if (count($matches) <= 1) {
				return [];
			}
			$content = $matches[1];
			$content = $this->safeDecode($content, true);
			if ($content['code'] != 0) {
				return [];
			}
			if (! array_key_exists('data', $content) || ! array_key_exists('group', $content['data'])) {
				return [];
			}
			foreach ($content['data']['group'] as $group) {
				$groups[] = [
					'gid'   => $group['groupid'],
					'gname' => $group['groupname'],
				];
			}
		}

		return $groups;
	}

	public function getGroupMembers($groupId)
	{
		$members  = [];
		$url      = sprintf(self::MemberListUrl, $groupId, $this->bkn);
		$browser  = new Browser();
		$response = $browser->get($url, [
			'Cookie'           => $this->buildCookie(),
			'Referer'          => 'http://qinfo.clt.qq.com/qinfo_v3/member.html',
			'X-Requested-With' => 'XMLHttpRequest',
			'User-Agent'       => self::UserAgent,
		]);

		if ($response->isOk()) {
			$content = str_replace('&nbsp;', ' ', $response->getContent());
			$content = $this->safeDecode($content, true);
			if (array_key_exists('mems', $content)) {
				foreach ($content['mems'] as $mem) {
					$members[] = ['qq' => $mem['u'], 'name' => $mem['n']];
				}
			}
		}

		return $members;
	}

	public function getUin()
	{
		return $this->uin;
	}

	private function buildCookie()
	{
		return sprintf('uin=%s; skey=%s', $this->uin, $this->skey);
	}

	private function getBkn($skey)
	{
		$len  = strlen($skey);
		$hash = 5381;

		for ($i = 0; $i < $len; $i++) {
			$hash += ($hash << 5) + ord($skey[$i]);
		}

		return $hash & 0x7fffffff;
	}

	private function safeDecode(string $content, $option = false)
	{
		$decoded = json_decode($content, $option);
		switch (json_last_error()) {
			case JSON_ERROR_NONE:
				return $decoded;
			case JSON_ERROR_DEPTH:
				trigger_error('Maximum stack depth exceeded');
			case JSON_ERROR_STATE_MISMATCH:
				trigger_error('Underflow or the modes mismatch');
			case JSON_ERROR_CTRL_CHAR:
				trigger_error('Unexpected control character found');
			case JSON_ERROR_SYNTAX:
				trigger_error('Syntax error, malformed JSON');
			case JSON_ERROR_UTF8:
				$clean = utf8_encode($content);
				$this->safeDecode($clean);
			default:
				trigger_error('Unknown error');
		}

		return null;
	}
}