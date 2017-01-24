<?php

namespace App\Game;

use App\Game\Storage\Session;

class Logic
{
    /**
     * @var Session
     */
    private $storage;

    /**
     * @var array
     */
    private $symbols = ['X', 'O'];

    /**
     * @var null
     */
    private $gameSize = null;

    /**
     * storage game key
     * @var string
     */
    private $gameKey = 'game';

    /**
     * storage game size key
     * @var string
     */
    private $gameSizeKey = 'size';

    /**
     * GameRepository constructor.
     * @param $storage
     */
    public function __construct($storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param $size
     */
    public function newGame($size)
    {
        // delete previous game if exists
        $this->storage->delete($this->gameKey);

        // set new game size
        $this->saveGameSize($size);

        // init game
        $game = [];
        for ($x = 1; $x <= $size; $x++) {
            for ($y = 1; $y <= $size; $y++) {
                $game[$y][$x] = null;
            }
        }

        $this->saveGame($game);
    }

    /**
     * @return array
     */
    function getWholeGame()
    {
        //get the whole game
        return (array) $this->storage->getKey($this->gameKey);

    }

    /**
     * @return array
     */
    public function getSymbols()
    {
        return $this->symbols;
    }

    /**
     * @return int
     */
    public function getGameSize()
    {
        if (is_null($this->gameSize)) {
            $this->gameSize = $this->storage->getKey($this->gameSizeKey);
        }

        return $this->gameSize;
    }

    /**
     * @param int $size
     */
    public function saveGameSize($size)
    {
        $this->storage->setKey($this->gameSizeKey, $size);
        $this->gameSize = $size;
    }

    /**
     * @param int $x
     * @param int $y
     * @return bool
     */
    public function isSquareAvailable($x, $y)
    {
        $currentGame = (array) $this->getWholeGame();

        if (!empty($currentGame[$y][$x])) {
            return false;
        }

        return true;
    }

    /**
     * @param int $x
     * @param int $y
     * @param string $symbol X|O
     * @return bool;
     */
    public function saveSquare($x, $y, $symbol)
    {
        //get the whole game
        $game = (array) $this->getWholeGame();

        //set the square symbol into the whole game
        $game[$y][$x] = $symbol;

        // save the whole game to the persistent storage
        return $this->saveGame($game);
    }

    /**
     * @param $game
     * @return bool
     */
    public function saveGame($game) {
        try {
            $this->storage->setKey($this->gameKey, $game);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * check if there is a winner
     * @return bool
     */
    public function isGameWinned()
    {
        // check rows
        if ($this->checkFullRow()) {
            return true;
        }

        // check columns
        if ($this->checkFullLine()){
            return true;
        }

        //chek diagonal
        if ($this->checkFullDiagonal()){
            return true;
        }

        //check reverse diagonal
        if ($this->checkFullDiagonalReverse()){
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function checkFullRow()
    {
        $game = $this->getWholeGame();
        for ($y = 1; $y <= $this->getGameSize(); $y++) {

            $total = $this->resetTotal();

            //check lines
            for ($x = 1; $x <= $this->getGameSize(); $x++) {
                $symbol = $game[$y][$x];
                if (!is_null($symbol)) {
                    $total[$symbol]++;
                    // winner
                    if ($total[$symbol] == $this->getGameSize()) {
                        return true;
                    }
                }

            }
        }
        return false;
    }

    /**
     * @return bool
     */
    private function checkFullLine()
    {
        $game = $this->getWholeGame();
        for ($x = 1; $x <= $this->getGameSize(); $x++) {

            $total = $this->resetTotal();

            for ($y = 1; $y <= $this->getGameSize(); $y++) {
                $symbol = $game[$y][$x];
                if (!is_null($symbol)) {
                    $total[$symbol]++;

                    // winner
                    if ($total[$symbol] == $this->getGameSize()) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    private function checkFullDiagonal()
    {
        $game = $this->getWholeGame();
        $total = $this->resetTotal();

        for ($x = $y = 1; $x <= $this->getGameSize() && $y <= $this->getGameSize(); $x++, $y++) {
            $symbol = $game[$y][$x];
            if (!is_null($symbol)) {
                $total[$symbol]++;

                // winner
                if ($total[$symbol] == $this->getGameSize()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    private function checkFullDiagonalReverse()
    {
        $game = $this->getWholeGame();
        $total = $this->resetTotal();

        for ($x = $this->getGameSize(), $y = 1; $x >= 1 && $y <= $this->getGameSize(); $x--, $y++) {
            $symbol = $game[$y][$x];
            if (!is_null($symbol)) {
                $total[$symbol]++;

                // winner
                if ($total[$symbol] == $this->getGameSize()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * check if it remains any free square
     * @return bool
     */
    public function isSquareLeft()
    {
        $game = (array) $this->getWholeGame();

        for ($y = 1; $y <= $this->getGameSize(); $y++) {
            for ($x = 1; $x <= $this->getGameSize(); $x++) {
                if (empty($game[$y][$x])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array
     */
    private function resetTotal()
    {
        $total = [];
        foreach ($this->getSymbols() as $symbol) {
            $total[$symbol] = 0;
        }
        return $total;
    }

}