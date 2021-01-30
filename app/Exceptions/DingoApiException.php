<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * dingo api 异常(需要继承HttpException,dingo会自动处理响应)
 * Class DingoApiException
 * @package App\Exceptions
 */
class DingoApiException extends HttpException
{
    /**
     * 通常项目中statuscode可以定义好,这样在手动抛出异常的时候就只需要填写异常提示即可
     * DingoApiException constructor.
     * @param $statusCode
     * @param null $message
     * @param Exception|null $previous
     * @param array $headers
     * @param int $code
     */
    public function __construct($statusCode, $message = null, Exception $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
