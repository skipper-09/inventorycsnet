<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Setting Aplikasi',
            'setting' => Setting::first(),
        ];

        return view('pages.settings.settings.index', $data);
    }

    public function update(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:225',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:5048',
        ]);

        // get data
        $setting = Setting::firstOrCreate([]);

        $oldSettingsData = $setting->toArray();

        if ($request->hasFile('logo')) {
            $logoPath = 'public/logo/' . $setting->logo;

            if (Storage::exists($logoPath)) {
                Storage::delete($logoPath);
            }
            $validated['logo'] = $request->file('logo')->store('logo', 'public');
        }

        $setting->update($validated);

        activity()
            ->causedBy(Auth::user())
            ->event('updated')
            ->withProperties([
                'old' => $oldSettingsData,
                'new' => $setting->toArray(),
            ])
            ->log("Setting Aplikasi berhasil diperbarui.");

        return redirect()->back()->with(['status' => 'Success!', 'message' => 'Berhasil Setting Aplication!']);
    }
}
