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
    private $T_USEC = 100000;
    private $T_SEC = 0;


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

	/**
	 * UCIProcess constructor.
	 *
	 * @param $engine_path
	 *
	 * @throws \Exception
	 */
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

        $this->throwErrorsIfFound();
    }

	/**
	 * @param string $command
	 *
	 * @return bool|int|mixed
	 * @throws \Exception
	 */
    public function write($command)
    {
        $result = fputs($this->process_pipes[0], trim($command)."\n");
        fflush($this->process_pipes[0]);
        $this->throwErrorsIfFound();

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
    function getProcessDescriptors()
    {
        return array(
            array('pipe', 'r'), // stdin
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
        $read =  array($stream);
	    $write = $except = array();


        // wait until something can be read
        while (($num_changes = stream_select($read, $write, $except, $this->T_SEC, $this->T_USEC)) > 0 && !feof($stream)) {
            $line = trim(fgets($stream));

            $lines[] = $line;

            if (trim($line) == "\n") {
                break;
            }

            if (preg_match('/^bestmove/i', $line) || preg_match('/^bestmove \(none\)/i', $line)) {
                break;
            }
        }

        return $lines;
    }

    /**
     * @throws \Exception
     */
    private function throwErrorsIfFound()
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

    /**
     * @param int $T_USEC
     * @return UCIProcess
     */
    public function setTUSEC($T_USEC)
    {
        $this->T_USEC = $T_USEC;

        return $this;
    }

    /**
     * @param int $T_SEC
     * @return UCIProcess
     */
    public function setTSEC($T_SEC)
    {
        $this->T_SEC = $T_SEC;

        return $this;
    }

}