<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SensitiveWordRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 判断字段值是否通过验证
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return strpos($value, '敏感词') === false;
    }

    /**
     * 自定义错误提示消息
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return ':attribute输入字段中包含敏感词';
    }
}
