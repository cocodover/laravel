<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * 实现 ShouldQueue 接口,会将通知异步处理(异步处理需要queue:work进行消费)
 * Class SuccessfulRegistrationNotification
 * @package App\Notifications
 */
class SuccessfulRegistrationNotification extends Notification
{
    use Queueable;

    public $user;

    /**
     * SuccessfulRegistrationNotification constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * 指定发送频道
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
//        return ['database'];
        return ['mail'];
    }

    /**
     * 定义通知的邮件展示方式(邮件&md文档)
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $name = $this->user->name;
        $id = $this->user->id;
        return (new MailMessage)
            ->subject('注册成功通知')
            ->line("亲爱的{$name}，您好:")
            ->line("感谢成为laravel项目第{$id}位用户！");
//            ->action('跳转地址', url('/'))
    }

    /**
     * 格式化数据库通知(数据库&广播)
     * 将会把数组转化为json存入notifications表的data字段,但是需要额外提供路由给访问通知使用
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'name' => $this->user->name,
            'email' => $this->user->email
        ];
    }
}
