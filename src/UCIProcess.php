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
    const T_USEC = 500;
    const T_SEC = 2;

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

        $this->throwIfError();
    }

    /**
     * @param string $command
     * @return bool|int
     */
    public function write($command)
    {
        $result = fputs( $this->process_pipes[0], trim($command)."\n" );
        fflush($this->process_pipes[0]);
        $this->throwIfError();

        return $result;
    }

    /**
     * @return array
     */
    public function read()
    {
        return $this->readFromStream($this->process_pipes[1]);
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

    /**
     * @param $stream
     * @return array
     */
    private function readFromStream($stream)
    {
        $lines = [];
        $read = $write = $except = array($stream);
        // wait until something can be read
        while (($num_changes = stream_select($read, $write, $except, self::T_SEC, self::T_USEC)) >= 1 && !feof($stream)) {
            $lines[] = trim(fgets($stream));
        }

        return $lines;
    }

    /**
     * @throws \Exception
     */
    private function throwIfError()
    {
        $errors = $this->readFromStream($this->process_pipes[2]);

        if (count($errors)) {
            throw new \Exception($errors[0]);
        }
    }


    public function __destruct()
    {
//        if ($this->process !== false) {
//
//            @fclose($this->process_pipes[0]);
//            @fclose($this->process_pipes[1]);
//            @fclose($this->process_pipes[2]);
//
//            @proc_close($this->process);
//        }
    }

}