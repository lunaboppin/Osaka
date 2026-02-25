<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'max:102400'],
            'remove_avatar' => ['nullable', 'boolean'],
            'default_sticker_type_id' => ['nullable', 'exists:sticker_types,id'],
            'banner' => ['nullable', 'image', 'max:102400'],
            'remove_banner' => ['nullable', 'boolean'],
            'accent_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'clear_accent_color' => ['nullable', 'boolean'],
            'profile_theme' => ['nullable', 'string', 'max:30'],
            'avatar_frame' => ['nullable', 'string', 'max:30'],
            'displayed_badges' => ['nullable', 'array', 'max:' . config('osaka.profile.max_displayed_badges', 5)],
            'displayed_badges.*' => ['string', 'max:50'],
        ];
    }
}
