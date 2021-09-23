<?php

namespace App\Api;

use Symfony\Component\HttpFoundation\Response;

class ApiProblem
{
    public const TYPE_VALIDATION_ERROR = 'validation_error';

    private static array $titles = [
        self::TYPE_VALIDATION_ERROR => 'There was a validation error.',
    ];

    private int $statusCode;
    private string $type;
    private string $title;
    private array $extraData = [];

    public function __construct($statusCode, $type = null)
    {
        $this->statusCode = $statusCode;
        if ($type === null) {
            $type = 'about:blank';
            $title = Response::$statusTexts[$statusCode] ?? 'Unknown status code.';
        } else {
            if (!isset(self::$titles[$type])) {
                throw new \InvalidArgumentException('No title for type ' . $type);
            }
            $title = self::$titles[$type];
        }
        $this->type = $type;
        $this->title = $title;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->extraData,
            [
                'status' => $this->statusCode,
                'type' => $this->type,
                'title' => $this->title,
            ]
        );
    }

    public function set($name, $value): void
    {
        $this->extraData[$name] = $value;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}