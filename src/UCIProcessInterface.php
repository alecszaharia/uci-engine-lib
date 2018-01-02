<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 8/21/16
 * Time: 4:48 PM
 */

namespace UCIEngine;


interface UCIProcessInterface
{
    /**
     * @param string $command
     * @return mixed
     */
    public function write($command);

    public function read();

    function getProcessDescriptors();
}