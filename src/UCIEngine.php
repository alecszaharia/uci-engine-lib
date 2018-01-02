<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 8/18/17
 * Time: 1:12 AM
 */

namespace UCIEngine;


class UCIEngine implements UCIEngineInterface
{
    /**
     * @var string
     */
    private $engine_path;

    /**
     * @var UCIProcess
     */
    private $uci_process;

    public function __construct($engine_path)
    {
        $this->engine_path = $engine_path;

        $this->uci_process = new UCIProcess($this->engine_path);

    }

    public function sendCommand($command)
    {
        if ($this->isString($command)) {
            $this->uci_process->write($command);
        }
    }

    public function sendCommands($commands)
    {
        if (is_array($commands)) {
            foreach ($commands as $command) {
                $this->sendCommand($command);
            }
        }
    }

    public function setOption($name, $value)
    {
        if ($this->isString($name) && $this->isString($value)) {
            $this->sendCommand("setoption name {$name} value {$value}");
        }
    }

    public function setOptions($options)
    {
        if (is_array($options)) {
            foreach ($options as $option) {
                $this->setOption(...$option);
            }
        }
    }

    public function setPosition($position, $moves = null)
    {
        if ($this->isString($position)) {

            $movesCommand = "";
            if($this->isString($moves)) {
                $movesCommand = " moves {$moves}";
            }

            if ($position == 'startpos') {
                $this->sendCommand("position {$position}{$movesCommand}");
            } else {
                $this->sendCommand("position fen {$position}{$movesCommand}");
            }
        }
    }

    public function newGame()
    {
        $this->sendCommand("ucinewgame");
    }

    private function isString($string)
    {
        return is_string($string) && !empty($string);

    }


}