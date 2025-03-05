<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\ExampleNotification; //harus diisi
use Illuminate\Support\Facades\Notification; 

class homecontroller extends Controller
{
    public function test (){
        $user = User::all();
        $data = "Ini adalah contoh data";
        //dibawah ini merupakan
        //contoh mengirimkan notifikasi ke semua user
        Notification::send($user, new ExampleNotification($data));
        
        return response()->json([
            'message' => 'Notifikasi berhasil dikirim'
        ]);
    }
}
