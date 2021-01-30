<?php

namespace App\Http\Requests;

use App\Rules\SensitiveWordRule;
use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    /**
     * 检查用户权限
     * true表示用户有权提交表单,false表示用户无权提交表单
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 定义请求字段验证规则
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'name' => [
                'required',
                'string',
                new SensitiveWordRule()
            ],
            'status' => 'required|boolean'
        ];
    }

    /**
     * 自定义错误提示消息
     * @return array
     */
    public function messages(): array
    {
        return [
            'id.required' => 'id字段不能为空',
            'id.integer' => 'id字段必须为整形',
            'name.required' => '名称字段不能为空',
            'name.string' => '名称字段必须为字符串',
            'status.required' => '状态字段不能为空',
            'status.boolean' => '状态字段必须为布尔型'
        ];
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'id' => 'id',
            'name' => '名称',
            'status' => '状态'
        ];
    }
}
