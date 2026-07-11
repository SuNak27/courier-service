<?php

namespace App\Http\Requests;

use App\Models\Courier;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * All fields are optional here; only the fields present in the request
     * are validated and updated (partial update via PATCH/PUT).
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $courierId = $this->route('courier')?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['sometimes', 'required', 'string', 'max:32', Rule::unique('couriers', 'phone')->ignore($courierId)],
            'email' => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('couriers', 'email')->ignore($courierId)],
            'vehicle_type' => ['sometimes', 'nullable', 'string', 'max:255'],
            'vehicle_plate' => ['sometimes', 'nullable', 'string', 'max:32'],
            'level' => ['sometimes', 'required', 'integer', 'between:'.Courier::MIN_LEVEL.','.Courier::MAX_LEVEL],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
