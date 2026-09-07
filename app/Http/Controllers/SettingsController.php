<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;

class SettingsController extends ApiController
{
    public function index()
    {
        return $this->success(['user' => request()->user()->only(['id', 'name', 'email'])], 'Settings retrieved');
    }

    public function update(UpdateSettingsRequest $request)
    {
        foreach ($request->validated('settings') as $key => $value) {
            request()->user()->settings()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return $this->success(['user' => request()->user()->only(['id', 'name', 'email']), 'settings' => request()->user()->settings()->pluck('value', 'key')], 'Settings updated');
    }
}
