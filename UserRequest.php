<?php

namespace App;

require('vendor/autoload.php');

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * This parses the request that user is trying to make
 * Class Request
 * @package App
 */
class UserRequest
{
    /**
     * Stores the information of the current request is valid.
     * @var bool
     */
    private $valid = false;

    /**
     * The target URL
     * @var string
     */
    private $request_url = "";

    /**
     * The request method
     * @var string
     */
    private $request_method = "";

    /**
     * Request headers
     * @var array
     */
    private $request_headers = [];

    /**
     * Request body
     * @var bool|string
     */
    private $request_body = "";

    /**
     * @var array
     */
    private $request_fields = [];

    /**
     * Stores if the content type is multipart/form-data
     * @var bool
     */
    private $multiPart = false;

    /**
     * Stores if the content type is application/x-www-url-encoded
     * @var bool
     */
    private $urlEncoded = false;


    /**
     * @var bool
     */
    private $hasPayload = false;


    /**
     * UserRequest constructor.
     * @throws Exception
     */
    public function __construct()
    {
        if (!isset($_SERVER['HTTP_X_PHP_URL'])) {
            $this->valid = false;
            throw new Exception('URL is not provided.');
        }

        $this->request_url = $_SERVER['HTTP_X_PHP_URL'];
        $this->request_method = $_SERVER['REQUEST_METHOD'];

        if ($this->getRequestMethod() === "POST" || $this->getRequestMethod() == "PUT") {
            // Try to read body first
            $input = file_get_contents('php://input');

            if ($input && mb_strlen($input) > 0) {
                $this->request_body = $input;
            } else if (count($_REQUEST) > 0) {
                $this->request_fields = $_POST;
            }
        }

        $this->hasPayload = mb_strlen($this->getRequestBody()) > 0;

        $this->parse_header();
    }

    /**
     * Parses the header and extract requred information out of it
     */
    private function parse_header()
    {
        if (isset($_SERVER['HTTP_CONTENT_TYPE'])) {
            $this->request_headers['Content-Type'] = $_SERVER['HTTP_CONTENT_TYPE'];
        }

        if (isset($_SERVER['CONTENT_TYPE'])) {
            $this->request_headers['Content-Type'] = $_SERVER['CONTENT_TYPE'];
        }

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $this->request_headers['Authorization'] = $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (isset($this->request_headers['Content-Type'])) {
            $contentType = $this->request_headers['Content-Type'];

            $this->multiPart = !(gettype(strpos($contentType, "multipart/form-data")) === "boolean");
            $this->urlEncoded = !(gettype(strpos($contentType, "application/x-www-form-urlencoded")) === "boolean");
        }
    }

    /**
     * Executes user response and returns it
     * @return UserResponse
     */
    public function execute(): UserResponse
    {
        $client = new Client();
        $request = new Request(
            $this->getRequestMethod(),
            $this->getRequestUrl(),
            $this->getRequestHeaders()
        );

        $response = null;
        $responseInterface = null;

        try {
            if ($this->multiPart) {
                $multiPartFields = [];

                foreach ($this->getRequestFields() as $fieldKey => $fieldValue) {
                    $multiPartFields[] = [
                        'name' => $fieldKey,
                        'contents' => $fieldValue
                    ];
                }

                $responseInterface = $client->send($request->withoutHeader('Content-Type'), [
                    'multipart' => $multiPartFields
                ]);

            } else if ($this->urlEncoded) {
                $responseInterface = $client->send($request, [
                    'form_params' => $this->getRequestFields()
                ]);
            } else if ($this->hasPayload) {
                $responseInterface = $client->send($request, ['body' => $this->getRequestBody()]);
            } else {
                $responseInterface = $client->send($request);
            }

            $response = UserResponse::createFromResponse($responseInterface);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            $response = UserResponse::createFromException($e);
        }

        return $response;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @return string
     */
    public function getRequestUrl(): string
    {
        return $this->request_url;
    }

    /**
     * @return array
     */
    public function getRequestHeaders(): array
    {
        return $this->request_headers;
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->request_method;
    }

    /**
     * @return string
     */
    public function getRequestBody(): string
    {
        return $this->request_body;
    }

    /**
     * @return array
     */
    public function getRequestFields(): array
    {
        return $this->request_fields;
    }
}