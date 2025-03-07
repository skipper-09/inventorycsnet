<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificatioinController extends Controller
{
    public function markAsRead(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->notification_id == "all") {
                Auth::user()->unreadNotifications->markAsRead();
            } else {
                $notification = Auth::user()->notifications()->find($request->notification_id);

                if ($notification && !$notification->read_at) {
                    $notification->markAsRead();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil ditandai sebagai dibaca.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

}
