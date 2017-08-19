<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 8/18/17
 * Time: 1:12 AM
 */

namespace UCIEngine;


class UCIProcess implements UCIProcessInterface
{
    /**
     * UCI engine path
     *
     * @var string
     */
    private $engine_path;

    /**
     * @var array
     */
    private $process_pipes;

    /**
     * @var resource
     */
    protected $process;

    public function __construct($engine_path)
    {
        if (empty($engine_path)) {
            throw new \InvalidArgumentException(sprintf('The $engine_path parameter of the %s constructor should not be empty', __CLASS__));
        }

        $this->engine_path = $engine_path;

        $this->process = proc_open($this->engine_path, $this->getProcessDescriptors(), $this->process_pipes);

        if (!is_resource($this->process)) {
            throw new \Exception('The process cannot be started.');
        }
    }

    public function write($command)
    {
        // TODO: Implement write() method.
    }

    public function read()
    {
        $lines = [];
        $read = array($this->process_pipes[1]);
        $write = null;
        $except = null;

        // wait until something can be read
        while (($num_changes = stream_select($read, $write, $except, 0, 200)) !== false && $num_changes >= 0);

        $lines[] = fgets($this->process_pipes[1]);

        return $lines;
    }

    /**
     * @return mixed
     */
    public function getProcessDescriptors()
    {
        return array(
            array('pipe', 'r'),
            array('pipe', 'w'), // stdout
            array('pipe', 'w'), // stderr
        );
    }


    public function __destruct()
    {
        if ($this->process !== false) {

            @fclose($this->process_pipes[0]);
            @fclose($this->process_pipes[1]);
            @fclose($this->process_pipes[2]);

            @proc_close($this->process);
        }
    }

}