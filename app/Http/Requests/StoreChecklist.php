<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChecklist extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            // 'machine.id' => 'nullable',
            // 'machine.name' => 'required',
            // 'machine.manufacturer' => 'required',
            // 'machine.model' => 'required',
            // 'machine.is_active' => 'required',
            // 'code' => 'required',
            // 'title' => 'required',
            'grading_fields' => 'required|array',
            'grading_fields.*.label' => 'required|string',
            'grading_fields.*.type' => 'required|string',
        ];
    }

    /**
     * Transform the incoming request data before validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $gradingFields = $this->input('grading_fields');

        if ($gradingFields && is_array($gradingFields)) {
            $formattedData = [];

            foreach ($gradingFields as $key => $values) {
                foreach ($values as $index => $value) {
                    if (!isset($formattedData[$index])) {
                        $formattedData[$index] = [];
                    }
                    $formattedData[$index][$key] = $value;
                }
            }

            $this->merge(['grading_fields' => $formattedData]);
        }
    }
    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation()
    {
        $this->replace([
            'grading_fields' => json_encode($this->grading_fields)
        ]);
    }
}
