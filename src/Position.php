<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 8/18/17
 * Time: 12:57 AM
 */

namespace UCIEngine;

class Position
{
    const START_POS = 'startpos';

    public static function startPosition()
    {
        return self::START_POS;
    }

    /**
     * Return the moves position string
     * Make sure you pass the moves(algebraic format) space separated
     *
     * @param string $moves
     * @return string
     */
    public static function moves($moves)
    {
        return "moves {$moves}";
    }
}