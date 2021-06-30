<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Message;
use EasyWeChat\Kernel\ServerResponse;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Kernel\Traits\InteractWithXmlMessage;
use EasyWeChat\OpenWork\Contracts\Account as AccountInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Server implements ServerInterface
{
    use InteractWithHandlers;
    use InteractWithXmlMessage;

    /**
     * @throws \Throwable
     */
    public function __construct(
        protected AccountInterface $account,
        protected ServerRequestInterface $request,
        protected Encryptor $encryptor,
    ) {
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleSuiteTicketRefreshed(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return $message->InfoType === 'suite_ticket' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function handleAuthCreated(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return
                    $message->InfoType === 'create_auth' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function handleAuthChanged(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return
                    $message->InfoType === 'change_auth' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function handleAuthCancelled(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return
                    $message->InfoType === 'cancel_auth' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function handleUserCreated(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return
                    $message->InfoType === 'change_contact' && $message->ChangeType === 'create_user' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function handleUserUpdated(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return
                    $message->InfoType === 'change_contact' && $message->ChangeType === 'update_user' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function handleUserDeleted(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return
                    $message->InfoType === 'change_contact' && $message->ChangeType === 'delete_user' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function handlePartyCreated(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return
                    $message->InfoType === 'change_contact' && $message->ChangeType === 'create_party' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function handlePartyUpdated(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return
                    $message->InfoType === 'change_contact' && $message->ChangeType === 'update_party' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function handlePartyDeleted(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return
                    $message->InfoType === 'change_contact' && $message->ChangeType === 'delete_party' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function handleUserTagUpdated(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return
                    $message->InfoType === 'change_contact' && $message->ChangeType === 'update_tag' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function handleShareAgentChanged(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return
                    $message->InfoType === 'share_agent_change' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function transformResponse(array $response, Message $message): ResponseInterface
    {
        return ServerResponse::success();
    }
}
