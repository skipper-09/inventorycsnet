<?php

use App\Models\Setting;
use Illuminate\Support\Carbon;

function Setting($key)
{
    return Setting::first()->{$key};
}

function formatDate($date, $format = 'd M Y')
{
    return Carbon::parse($date)->format($format);
}