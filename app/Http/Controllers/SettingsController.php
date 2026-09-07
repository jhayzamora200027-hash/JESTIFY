<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Http\Resources\UserSettingResource;

class SettingsController extends ApiController
{
    public function index()
    {
        return $this->success(UserSettingResource::collection(request()->user()->settings()->latest()->get()), 'Settings retrieved');
    }

    public function update(UpdateSettingsRequest $request)
    {
        foreach ($request->validated('settings') as $key => $value) {
            request()->user()->settings()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return $this->success(UserSettingResource::collection(request()->user()->settings()->latest()->get()), 'Settings updated');
    }
}
