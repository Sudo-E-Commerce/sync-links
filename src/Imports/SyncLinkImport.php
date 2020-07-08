<?php

namespace Sudo\SyncLink\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class SyncLinkImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        if (isset($row[0]) && !empty($row[0])) {
            $check_exists = \Sudo\SyncLink\Models\SyncLink::where('old', $row[0])->first();
            if ($check_exists == null) {
                \Sudo\SyncLink\Models\SyncLink::create([
                   'old'        => $row[0] ?? '',
                   'new'        => $row[1] ?? '',
                   'redirect'   => $row[2] ?? '301',
                   'status'     => 1,
                ]);
            } else {
                \Sudo\SyncLink\Models\SyncLink::where('id', $check_exists->id)->update([
                   'new'        => $row[1] ?? '',
                   'redirect'   => $row[2] ?? '301',
                   'status'     => 1,
                ]);
            }
        }
        return null;
    }
}