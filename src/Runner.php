<?php

namespace Kojirock\CronDocGen;


class Runner
{
    /**
     * @var array
     */
    private $markdownList = [];

    /**
     * @param $cronFilePath
     * @return int
     */
    public function run($cronFilePath)
    {
        if (!file_exists($cronFilePath)) {
            return 1;
        }

        $text     = file_get_contents($cronFilePath);
        $explodes = explode("\n", $text);

        $parameters = [];
        $group      = [];
        foreach ($explodes as $val) {
            if (strlen($val) === 0) {
                $parameters[] = $group;
                $group        = [];
                continue;
            }
            $group[] = $val;
        }

        if (count($parameters) === 0) {
            return 1;
        }

        $this->markdownList[] = "# Cron Document";
        $this->markdownList[] = "";
        $this->markdownList[] = "## 変数";
        $this->markdownList[] = "";

        if (count($parameters[0]) >= 1) {
            $this->markdownList[] = "| Key | Value |";
            $this->markdownList[] = "|------|------|";
            $this->setVariables($parameters[0]);
            unset($parameters[0]);
        }

        $this->markdownList[] = "";
        $this->markdownList[] = "## バッチ一覧";
        $this->markdownList[] = "";
        $this->setBatches($parameters);

        echo implode("\n", $this->markdownList);
        return 0;
    }

    /**
     * @param array $params
     */
    protected function setVariables(array $params)
    {
        foreach ($params as $val) {
            $explodes = explode("=", $val);
            if (!isset($explodes[1])) {
                continue;
            }

            $this->markdownList[] = "|{$explodes[0]}|{$explodes[1]}|";
        }
    }

    /**
     * @param array $params
     */
    protected function setBatches(array $params)
    {
        foreach ($params as $val) {
            $title       = substr($val[0], 2);
            $description = substr($val[1], 2);
            $explodes    = explode(" ", $val[2]);
            if (!isset($explodes[1])) {
                continue;
            }

            $this->markdownList[] = "### {$title}";
            $this->markdownList[] = "#### {$description}";

            $minute  = array_shift($explodes);
            $hour    = array_shift($explodes);
            $day     = array_shift($explodes);
            $month   = array_shift($explodes);
            $week    = array_shift($explodes);
            $command = implode(" ", $explodes);

            $this->markdownList[] = "| 分 | 時 | 日 | 月 | 曜日| コマンド |";
            $this->markdownList[] = "|:------:|:------:|:------:|:------:|:------:|----|";
            $this->markdownList[] = "|{$minute}|{$hour}|{$day}|{$month}|{$week}|`{$command}`|";
            $this->markdownList[] = "";
        }
    }
}