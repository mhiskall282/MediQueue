<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display settings page.
     */
    public function index(): View
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $input = $request->except(['_token', '_method']);

        foreach ($input as $key => $value) {
            Setting::set($key, $value);
        }

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'settings.updated',
            'entity_type' => 'Setting',
            'entity_id'   => null,
            'metadata'    => ['updated_keys' => array_keys($input)],
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Clinic and system settings updated successfully.');
    }
}
