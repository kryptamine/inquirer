<?php

namespace Inquirer\Services;

/**
 * Class StatisticCalculator
 * @package Inquirer\Services
 */
class StatisticCalculator
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $filterBy;

    /**
     * @var bool
     */
    private $hideEmails = false;

    /**
     * StatisticCalculator constructor.
     * @param string $path
     * @param bool $hideEmails
     * @throws \Exception
     */
    public function __construct(string $path, bool $hideEmails = false)
    {
        if (!is_dir($path)) {
            throw new \Exception("directory: '{$path}' does not exist");
        }
        $this->hideEmails = $hideEmails;
        $this->path = $path;
    }

    /**
     * @return array
     */
    private function getChats(): array
    {
        $result = [];

        foreach (scandir($this->path) as $file) {
            $filePath = implode('/', [$this->path, $file]);

            if (is_file($filePath) && ($data = json_decode(file_get_contents($filePath), true))) {
                $result[] = $data;
            }
        }

        return $result;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function filterBy(string $field)
    {
        $this->filterBy = $field;

        return $this;
    }

    /**
     * @return array
     */
    public function collect(): array
    {
        $result = [];
        $data = $this->getChats();

        foreach ($data as $chat) {
            if (!isset($chat['results'])) {
                continue;
            }
            $email = $chat['email'];
            if ($this->hideEmails) {
                $emailParts = explode("@", $email);
                if (count($emailParts) != 2) {
                    continue;
                }
                $email = "{$emailParts[0]}@***";

            }
            $result[] = array_merge(
                [
                    'email' => $email,
                ],
                $chat['results']
            );
        }

        if ($this->filterBy) {
            uasort($result, function($a, $b) {
                return $b[$this->filterBy] <=> $a[$this->filterBy];
            });
        }

        return $result;
    }

}
