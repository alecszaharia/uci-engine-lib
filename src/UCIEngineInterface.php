<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 8/21/16
 * Time: 4:48 PM
 */

namespace UCIEngine;


interface UCIEngineInterface
{
    /**
     * Add one command
     *
     * @param string $command
     * @return self
     */
    public function sendCommand($command);

    /**
     * Send an array of commands
     *
     * ex: [ 'debug'=>true ]
     *
     * @param string[] $commands
     * @return self
     */
    public function sendCommands($commands);

    /**
     * @param string $name
     * @param string $value
     * @return self
     */
    public function setOption($name,$value);

    /**
     * Set an array of options
     *
     * ex: [ 'debug'=>true ]
     *
     * @param $options
     * @return mixed
     */
    public function setOptions($options);

    /**
     * @param string $name
     * @return self
     */
    public function getOption($name);

    /**
     * Return all options provided by the engine
     * ex: [
     *      'option1'=>'value1',
     *      'option2'=>'value2'
     *      ...
     *  ];
     * @param $name
     * @return string[]
     */
    public function getOptions($name);

    /**
     * @param string $position
     * @return self
     */
    public function setPosition($position);

    /**
     * @return self
     */
    public function setStartPosition();

    /**
     * @param $moves
     * @return self
     */
    public function setMovesPosition($moves);

}