<?php

namespace Inquirer;

use Inquirer\Factory;

class Chat
{
    private $id;

    /** @var Storage */
    private $storage;

    public function __construct($id, $storage)
    {
        $this->id = $id;
        $this->storage = $storage;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        if (!isset($this->storage->get()->email)) {
            return null;
        }
        return $this->storage->get()->email;
    }

    public function getBotUsername()
    {
        return $this->storage->get()->botUsername;
    }

    public function getCurrentConversationItem()
    {
        foreach ($this->storage->get()->conversation as $item) {
            if (isset($item->current) && $item->current) {
                if (isset($item->action)) {
                    switch ($item->action) {
                        case "summarize":
                            if (!isset($item->runId)) {
                                break;
                            }
                            Registry::getInstance()->getLog()->info("Summarize result for run '{$item->runId}'");
                            $item->message = $this->getSummarizedMessage($item->dialogName, $item->runId);
                            break;
                    }
                }
                return $item;
            }
        }
        return null;
    }

    protected function getSummarizedMessage($dialogName, $runId)
    {
        $runItems = $this->getConversationItemsByRunId($runId);
        $maxValue = $this->getTotalMaxValue($runItems);
        $value = $this->getOverallValue($runItems);
        $time = $this->getOverallTime($runItems);
        $runsCount = $this->getRunsCount($dialogName);

        $timeRepresentation = $this->getTimeRepresentation($time);

        if (1 < $runsCount) {
            return "Попытка <b>#{$runsCount}</b>: вы набрали <b>{$value}</b> баллов из <b>{$maxValue}</b> возможных за <b>{$timeRepresentation}</b>.";
        }

        if ($maxValue > 0) {
            $percentage = ($value / $maxValue) * 100;
        } else {
            $percentage = 0;
        }
        if ($percentage < 80) {
            return "Вы ответили на все вопросы квиза за <b>{$timeRepresentation}</b> и набрали <b>{$value}</b> баллов из <b>{$maxValue}</b> возможных.\n\n<i>К сожалению, этого недостаточно, однако, крутой стикерпак ждет вас на стенде Plesk. Спасибо за игру!</i>";
        }
        return "Вы ответили на все вопросы квиза за <b>{$timeRepresentation}</b> и набрали <b>{$value}</b> баллов из <b>{$maxValue}</b> возможных. Отличный результат!\n\n<b>Подходите на стенд Plesk за своим подарком и крутым стикерпаком. Спасибо за игру!</b>";
    }

    protected function getOverallValue($conversationItems)
    {
        $overallValue = 0;
        foreach ($conversationItems as $item) {
            if (!isset($item->options) || !isset($item->answer) || !isset($item->displayDate) || !isset($item->answerDate)) {
                continue;
            }
            $value = 0;
            foreach ($item->options as $option) {
                if (!isset($option->code) || !isset($option->value)) {
                    continue;
                }
                if ($option->code == $item->answer) {
                    $value += $option->value;
                    break;
                }
            }
            if (0 < $value) {
                $value = $this->decreaseValue($value, $item->answerDate - $item->displayDate);
            }
            $overallValue += $value;
        }
        return $overallValue;
    }

    protected function getTimeRepresentation($time)
    {
        $minutes = floor($time / 60);
        $seconds = $time - round($minutes * 60);
        if (59 < $minutes) {
            return "больше часа?! \xF0\x9F\x98\xA8";
        }
        return ($minutes < 10 ? '0' . $minutes : $minutes) . ':' . ($seconds < 10 ? '0' . $seconds : $seconds);
    }

    protected function decreaseValue($value, $time)
    {
        $min = 20;
        $max = 120;
        if ($time <= $min) {
            return $value;
        }
        if ($time > $max) {
            return round($value / 2);
        }
        $factor = ($time - $min) / ($max - $min);
        $decrease = floor($value / 2 * $factor);

        return $value - $decrease;
    }

    protected function getTotalMaxValue($conversationItems)
    {
        $totalMaxValue = 0;
        foreach ($conversationItems as $item) {
            if (!isset($item->options) || !isset($item->answer)) {
                continue;
            }
            $maxValue = 0;
            foreach ($item->options as $option) {
                if (!isset($option->code) || !isset($option->value)) {
                    continue;
                }
                if ($option->value > $maxValue) {
                    $maxValue = $option->value;
                }
            }
            $totalMaxValue += $maxValue;
        }
        return $totalMaxValue;
    }

    protected function getOverallTime($conversationItems)
    {
        $first = $conversationItems[0];
        if (!isset($first->runDate)) {
            return 0;
        }
        $lastAnswerDate = $first->runDate;
        foreach ($conversationItems as $item) {
            if (isset($item->answerDate)) {
                $lastAnswerDate = $item->answerDate;
            }
        }
        return $lastAnswerDate - $first->runDate;
    }

    protected function getConversationItemsByRunId($runId)
    {
        $items = [];
        foreach ($this->storage->get()->conversation as $item) {
            if (isset($item->runId) && $runId == $item->runId) {
                $items[] = $item;
            }
        }
        return $items;
    }

    protected function getRunsCount($dialogName)
    {
        $runIds = [];
        foreach ($this->storage->get()->conversation as $item) {
            if (!isset($item->dialogName) || $item->dialogName != $dialogName) {
                continue;
            }
            if (!isset($item->runId)) {
                continue;
            }
            $runIds[] = $item->runId;
        }
        $runIds = array_unique($runIds);
        return sizeof($runIds);
    }

    public function goToNextConversationItem()
    {
        $data = $this->storage->get();
        $length = sizeof($data->conversation);
        $nextConversation = null;
        for ($i = 0; $i < $length; $i++) {
            if (isset($data->conversation[$i]->current) && $data->conversation[$i]->current) {
                $data->conversation[$i]->current = false;
                if ($i < $length - 1) {
                    $data->conversation[$i + 1]->current = true;
                    $data->conversation[$i + 1]->displayDate = time();
                    $nextConversation = $data->conversation[$i + 1];
                    break;
                }
            }
        }
        $this->storage->set($data);
        if (is_null($nextConversation)) {
            $this->addDispatcher();
        }
        return $this->getCurrentConversationItem();
    }

    public function pickUp($message) {
        if (!filter_var($message, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        $this->addEmail($message);
        return true;
    }

    public function addEmail($email)
    {
        $data = $this->storage->get();
        $data->email = $email;
        $this->storage->set($data);
    }

    public function addAnswer($answer)
    {
        $data = $this->storage->get();
        $length = sizeof($data->conversation);
        for ($i = 0; $i < $length; $i++) {
            if (isset($data->conversation[$i]->current) && $data->conversation[$i]->current) {
                $data->conversation[$i]->answer = $answer;
                $data->conversation[$i]->answerDate = time();
                break;
            }
        }
        $this->storage->set($data);
    }

    public function addMessageId($messageId)
    {
        $data = $this->storage->get();
        $length = sizeof($data->conversation);
        for ($i = 0; $i < $length; $i++) {
            if (isset($data->conversation[$i]->current) && $data->conversation[$i]->current) {
                $data->conversation[$i]->messageId = $messageId;
                break;
            }
        }
        $this->storage->set($data);
    }

    public function addDialog($dialogName)
    {
        $runId = $this->generateRandomString(8);
        $runDate = time();
        $dialog = json_decode(file_get_contents($this->getDialogStoragePath($dialogName)));
        Registry::getInstance()->getLog()->info(json_last_error() . ' : ' . json_last_error_msg());
        $data = $this->storage->get();
        foreach ($dialog as $item) {
            $item->dialogName = $dialogName;
            $item->runId = $runId;
            $item->runDate = $runDate;
            $item->code = $this->generateRandomString(4);
            if (isset($item->options)) {
                for ($i = 0; $i < sizeof($item->options); $i++) {
                    $item->options[$i]->code = $this->generateRandomString(4);
                }
            }
            $data->conversation[] = $item;
        }
        $this->storage->set($data);
    }

    public function addButler()
    {
        $butler = new \stdClass();
        $butler->type = "butler";
        $butler->message = "<b>Вас приветствует Plesk Quiz Bot.</b>\nBuild, Secure and Run your websites on Plesk!\n\nОтвечая на вопросы, имейте в виду, что затраченное <b>время имеет значение</b> при финальном подстчете баллов! Обращаем внимание, что мы регистрируем прохождение квизов до 12 мая 18:00! Вы можете проходить каждый квиз по нескольку раз, однако подарки вручаются только по результатам <b>первой попытки</b>. Желаем удачи! \xF0\x9F\x98\x8A\n\nЧтобы начать, пожалуйста, укажите свой email.";
        $butler->current = true;
        $data = $this->storage->get();
        $data->conversation[] = $butler;
        $this->storage->set($data);
    }

    public function addDispatcher()
    {
        $options = [];
        $dialogFactory = new Factory\Dialog();
        foreach ($dialogFactory->getList() as $dialog) {
            $option = new \stdClass();
            $option->code = $dialog->getCode();
            $option->message = $dialog->getName();
            $options[] = $option;
        }
        $dispatcher = new \stdClass();
        $dispatcher->type = "dispatcher";
        $dispatcher->message = "Пожалуйста, выберите квиз, который вам хотелось бы пройти.";
        $dispatcher->options = $options;
        $dispatcher->current = true;

        $data = $this->storage->get();
        $length = sizeof($data->conversation);
        for ($i = 0; $i < $length; $i++) {
            $data->conversation[$i]->current = false;
        }
        $data->conversation[] = $dispatcher;
        $this->storage->set($data);
    }

    protected function getDialogStoragePath($dialogName)
    {
        $baseStoragePath = Registry::getInstance()->getApp()['baseStoragePath'];
        return $baseStoragePath . DIRECTORY_SEPARATOR . 'dialogs' . DIRECTORY_SEPARATOR . $dialogName . '.json';
    }

    protected function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


}
