<?php

/**
 * QQGroupApiInterface.php
 * ruogu-assist
 *
 * Created by HyanCat on 15/9/18.
 * Copyright (C) 2015 HyanCat. All rights reserved.
 */

namespace App\Contracts;

interface QQGroupApiInterface
{
	public function getUsersGroups();

	public function getGroupMembers($groupId);
}