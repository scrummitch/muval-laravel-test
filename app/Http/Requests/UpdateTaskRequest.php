<?php

namespace App\Http\Requests;

use App\Models\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('task'));
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => TaskStatus::fromName($this->input('status'))?->value,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'min:3',
                'max:255',
                Rule::unique('tasks')
                    ->ignore($this->route('task'))
            ],
            'description' => [
                'min:3',
                'max:255',
            ],
            'status' => [
                'required',
                Rule::enum(TaskStatus::class),
            ],
        ];
    }
}
