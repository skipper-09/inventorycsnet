<?php

use App\Models\Setting;

function Setting($key)
{

    return Setting::first()->{$key};
}