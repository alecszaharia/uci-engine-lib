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

    public function __construct($engine_path)
    {
        $this->engine_path = $engine_path;
    }

    public function sendCommand($command)
    {
        // TODO: Implement sendCommand() method.
    }

    public function sendCommands($commands)
    {
        // TODO: Implement sendCommands() method.
    }

    public function setOption($name, $value)
    {
        // TODO: Implement setOption() method.
    }

    public function setOptions($options)
    {
        // TODO: Implement setOptions() method.
    }

    public function getOption($name)
    {
        // TODO: Implement getOption() method.
    }

    public function getOptions($name)
    {
        // TODO: Implement getOptions() method.
    }

    public function setPosition($position)
    {
        // TODO: Implement setPosition() method.
    }

    public function setStartPosition()
    {
        // TODO: Implement setStartPosition() method.
    }

    public function setMovesPosition($moves)
    {
        // TODO: Implement setMovesPosition() method.
    }

}