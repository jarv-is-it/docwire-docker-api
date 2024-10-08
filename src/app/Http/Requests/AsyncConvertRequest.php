<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsyncConvertRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'filename' => ['required', 'regex:#\.(txt|docx|xlsx|pptx|doc|xls|xlsb|ppt|odt|ods|odp|pdf|html|htm|css|rtf|eml|pst|ost|pg|jpeg|jfif|bmp|pnm|png|tiff|webp|pages|numbers|keynote|fodp|fods|fodt|xml|xsd|xsl|csv|json|yml|yaml|rss|conf|md|log)$#si'],
            'content' => 'required|string',
            'output' => 'nullable|string|in:plain_text,html,csv,metadata',
            'language' => 'nullable|string|min:3|max:3',
            'identifier' => 'required|string',
            'webhook' => 'required|url',
        ];
    }
}
