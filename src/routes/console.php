<?php

use App\Models\Conversion;
use Illuminate\Support\Facades\Schedule;

// Purge old conversions deleting the files
Schedule::call(function () {
    Conversion::whereDate('created_at', '<', now()->subDay())
        ->get()
        ->each(function (Conversion $conversion) {
            $conversion->delete();
        });
})->hourly();
