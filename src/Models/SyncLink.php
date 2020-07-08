<?php

namespace Sudo\SyncLink\Models;

use Sudo\Base\Models\BaseModel;

class SyncLink extends BaseModel
{
	public $timestamps = false;

	protected $fillable = [
        'old', 'new', 'redirect', 'status',
    ];
}
