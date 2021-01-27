<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * 实现 ShouldQueue 接口,会将通知异步处理(异步处理需要queue:work进行消费)
 * Class UserRegistered
 * @package App\Mail
 */
class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * 邮件内容
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //若要添加激活链接,需要新增路由,同时在数据库中新增字段用来验证链接参数是否合法&记录激活时间
        return $this->view('emails.userRegistered')
            ->with([
                'name' => $this->user->name,
                'id' => $this->user->id
            ]);
    }
}
