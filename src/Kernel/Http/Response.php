<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Http;

use EasyWeChat\Kernel\Support\Collection;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

/**
 * Class Response.
 *
 * @author overtrue <i@overtrue.me>
 */
class Response extends GuzzleResponse
{
    /**
     * @return bool|string
     */
    public function getBodyContents()
    {
        $this->getBody()->rewind();
        $contents = $this->getBody()->getContents();
        $this->getBody()->rewind();

        return $contents;
    }

    /**
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return static
     */
    public static function buildFromGuzzleResponse(GuzzleResponse $response)
    {
        return new static(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }

    /**
     * Build to json.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->getBodyContents());
    }

    /**
     * Build to array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = json_decode($this->getBodyContents(), true);

        if (JSON_ERROR_NONE === json_last_error()) {
            return $array;
        }

        return [];
    }

    /**
     * Get collection data.
     *
     * @return \EasyWeChat\Kernel\Support\Collection
     */
    public function toCollection()
    {
        return new Collection($this->toArray());
    }

    /**
     * @return bool|string
     */
    public function __toString()
    {
        return $this->getBodyContents();
    }
}
