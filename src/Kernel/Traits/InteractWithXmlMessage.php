<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Message;
use EasyWeChat\Kernel\ServerResponse;
use Psr\Http\Message\ResponseInterface;

trait InteractWithXmlMessage
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException|\EasyWeChat\Kernel\Exceptions\BadRequestException|\Throwable
     */
    public function serve(): ResponseInterface
    {
        if (!!($str = $this->request->getQueryParams()['echostr'] ?? '')) {
            return ServerResponse::success($str);
        }

        $this->withMessageValidationHandler();

        $messageClass = \sprintf('%s\Message', (new \ReflectionClass($this))->getNamespaceName());

        if (!\class_exists($messageClass)) {
            throw new RuntimeException($messageClass . ' not found.');
        }

        $message = \call_user_func_array(
            [
                $messageClass,
                'createFromRequest',
            ],
            [
                $this->request,
                $this->encryptor,
            ]
        );

        $response = $this->handle(ServerResponse::success(), $message);

        if ($response instanceof ResponseInterface) {
            return $response;
        }

        $response = $this->normalizeResponse($response);

        return $this->transformResponse($response, $message);
    }

    /**
     * @throws \Throwable
     */
    public function withMessageValidationHandler(): static
    {
        return $this->withHandler(
            function ($message, \Closure $next) {
                $query = $this->request->getQueryParams();
                $signature = $query['signature'] ?? $query['msg_signature'] ?? null;

                if (!isset($signature) || 'aes' !== ($query['encrypt_type'] ?? '')) {
                    return $next($message);
                }

                $params = [$this->account->getToken(), $query['timestamp'], $query['nonce']];

                sort($params, SORT_STRING);

                if ($signature !== sha1(implode($params))) {
                    throw new BadRequestException('Invalid request signature.');
                }
            }
        );
    }

    /**
     * @throws \Throwable
     */
    public function addMessageListener(string $type, $handler): static
    {
        $this->withHandler(
            function ($message, \Closure $next) use ($type, $handler) {
                return $message->MsgType === $type ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function addEventListener(string $event, $handler): static
    {
        $this->withHandler(
            function ($message, \Closure $next) use ($event, $handler) {
                return $message->Event === $event ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function transformResponse(array $response, Message $message): ServerResponse
    {
        $currentTime = \time();

        return ServerResponse::xml(
            attributes: \array_merge(
                [
                    'ToUserName' => $message->FromUserName,
                    'FromUserName' => $message->ToUserName,
                    'CreateTime' => $currentTime,
                ],
                $response
            ),
            encryptor: $this->encryptor
        );
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function normalizeResponse($response): array
    {
        if (\is_array($response)) {
            if (!isset($response['MsgType'])) {
                throw new InvalidArgumentException('MsgType cannot be empty.');
            }

            return $response;
        }

        if (is_string($response) || is_numeric($response)) {
            return [
                'MsgType' => 'text',
                'Content' => $response,
            ];
        }

        throw new InvalidArgumentException(
            sprintf('Invalid Response type "%s".', gettype($response))
        );
    }
}
