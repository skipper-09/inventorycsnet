<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\ExampleNotification;
use Illuminate\Support\Facades\Notification; 

class homecontroller extends Controller
{
    public function test (){
        $user = User::all();
        $data = "Ini adalah contoh data";
        //dibawah ini merupakan
        Notification::send($user, notification: new ExampleNotification($data));
        
        return response()->json([
            'message' => 'Notifikasi berhasil dikirim'
        ]);
    }
}
