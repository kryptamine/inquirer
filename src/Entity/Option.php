<?php

namespace Inquirer\Entity;

/**
 * Class Option
 * @package Inquirer\Entity
 */
class Option implements Entity
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $value;

    /**
     * Option constructor.
     * @param string $code
     * @param string $message
     * @param string $value
     */
    public function __construct(string $code, string $message, string $value)
    {
        $this->code = $code;
        $this->message = $message;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->code;
    }

    /**
     * @param array $data
     * @return Option[]
     */
    public static function fill(array $data): array
    {
        $result = [];

        foreach ($data as $item) {
            $result[] = new self($item->code, $item->message, '');
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'value' => $this->value
        ];
    }
}
