<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

use EasyWeChat\Kernel\Contracts\MessageInterface;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\Kernel\Traits\HasAttributes;

abstract class Message implements MessageInterface
{
    use HasAttributes;

    /**
     * @var string
     */
    protected string $type;

    /**
     * @var int
     */
    protected int $id;

    /**
     * @var string|null
     */
    protected string | null $from = null;

    /**
     * @var string|array|null
     */
    protected string | array | null $to = null;

    /**
     * @var array
     */
    protected array $properties = [];

    /**
     * @var array
     */
    protected array $jsonAliases = [];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setAttributes($attributes);
    }

    /**
     * Return type name message.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * Magic getter.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return $this->getAttribute($property);
    }

    /**
     * Magic setter.
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return Message
     */
    public function __set(string $property, mixed $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            $this->setAttribute($property, $value);
        }

        return $this;
    }

    /**
     * @param array $appends
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function transformForJsonRequestWithoutType(array $appends = []): array
    {
        return $this->transformForJsonRequest($appends, false);
    }

    /**
     * @param array $appends
     * @param bool  $withType
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function transformForJsonRequest(array $appends = [], $withType = true): array
    {
        if (!$withType) {
            return $this->propertiesToArray([], $this->jsonAliases);
        }

        $messageType = $this->getType();

        return array_merge(
            [
                'msgtype' => $messageType
            ],
            $appends,
            [
                $messageType => array_merge(
                    $data[$messageType] ?? [],
                    $this->propertiesToArray([], $this->jsonAliases)
                )
            ]
        );
    }

    /**
     * @param array $appends
     * @param bool  $returnAsArray
     *
     * @return array|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function transformToXml(array $appends = [], bool $returnAsArray = false): string | array
    {
        $data = array_merge(
            [
                'MsgType' => $this->getType()
            ],
            $this->toXmlArray(),
            $appends
        );

        return $returnAsArray ? $data : XML::build($data);
    }

    /**
     * @param array $data
     * @param array $aliases
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function propertiesToArray(array $data, array $aliases = []): array
    {
        $this->assertRequiredAttributesExists();

        foreach ($this->attributes as $property => $value) {
            if (is_null($value) && !$this->isRequired($property)) {
                continue;
            }
            $alias = array_search($property, $aliases, true);

            $data[$alias ?: $property] = $this->get($property);
        }

        return $data;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function toXmlArray()
    {
        throw new RuntimeException(sprintf('Class "%s" cannot support transform to XML message.', __CLASS__));
    }
}
