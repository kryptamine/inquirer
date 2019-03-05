<?php

namespace Inquirer\Entity;

/**
 * Class ConversationItem
 * @package Inquirer\Entity
 */
class ConversationItem implements Entity
{
    /**
     * @var Option[]
     */
    private $options;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $messageId;

    /**
     * @var bool
     */
    private $isMulti;

    /**
     * ConversationItem constructor.
     * @param array $options
     * @param string $type
     * @param string $message
     * @param string $messageId
     * @param bool $isMulti
     */
    public function __construct(array $options, string $type, string $message, string $messageId, bool $isMulti)
    {
        $this->options = $options;
        $this->type = $type;
        $this->message = $message;
        $this->messageId = $messageId;
        $this->isMulti = $isMulti;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->messageId;
    }

    /**
     * @return bool
     */
    public function isMulti(): bool
    {
        return $this->isMulti;
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
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return Option[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function hasOptions(): bool
    {
        return !empty($this->options);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
            'options' => $this->options
        ];
    }

}