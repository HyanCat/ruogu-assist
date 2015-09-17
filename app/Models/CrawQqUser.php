<?php

/**
 * CrawQqUser.php
 * ruogu-assist
 *
 * Created by HyanCat on 15/9/17.
 * Copyright (C) 2015 HyanCat. All rights reserved.
 */
namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CrawQqUser extends BaseModel
{
	use SoftDeletes;
	protected $guarded = [];
}