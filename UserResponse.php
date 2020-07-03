<?php

namespace App;

use Exception;
use Psr\Http\Message\ResponseInterface;

/**
 * This class is used for returning a response to end-user
 * that uses proxy as a matter.
 * Class UserResponse
 * @package App
 */
class UserResponse
{
    //
    private const SUCCESS_CODES = [
        200, 201, 202, 203, 204, 205, 206, 207, 208, 226
    ];

    // Tells whether user request is success or not.
    private $success = false;

    // Status code of the response
    private $statusCode = 500;

    private $content_type = "application/json";

    // The body of returned response
    private $body = "";

    /**
     * Creates a response from ResponseInterface
     * @param ResponseInterface $r
     * @return UserResponse
     */
    public static function createFromResponse(ResponseInterface $r) : UserResponse
    {
        $response = new UserResponse();

        $response->setStatusCode($r->getStatusCode());
        $response->setSuccess(in_array($r->getStatusCode(), UserResponse::SUCCESS_CODES));
        $response->setBody($r->getBody()->getContents());
        $response->setContentType($r->getHeaderLine('Content-Type'));

        return $response;
    }

    /**
     * Creates an error response from a message.
     * @param Exception $exception
     * @return UserResponse
     */
    public static function createFromException(Exception $exception) : UserResponse
    {
        $response = new UserResponse();

        $response->setSuccess(false);
        $response->setBody($exception->getMessage());

        return $response;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     */
    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->content_type;
    }

    /**
     * @param string $content_type
     */
    public function setContentType(string $content_type): void
    {
        $this->content_type = $content_type;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function __toString()
    {
        return json_encode([
            'success' => $this->isSuccess(),
            'statusCode' => $this->getStatusCode(),
            'content-type' => $this->getContentType(),
            'body' => $this->getBody()
        ]);
    }
}