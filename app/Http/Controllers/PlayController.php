<?php

namespace App\Http\Controllers;

use App\Game\Logic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\JsonResponse;

class PlayController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Logic $gameLogic)
    {
        //@todo créate unique id for game session

        $rows = Config::get('game.game.size');
        $symbols = Config::get('game.game.symbols');

        $gameLogic->newGame($rows);
        $gameLogic->setSymbols($symbols);

        return View('play', [
            'rows' => $rows,
            'symbols' => $symbols
        ]);
    }

    /**
     * @param Request $request
     * @param Logic $gameLogic
     * @return JsonResponse
     */
    public function play(Request $request, Logic $gameLogic)
    {
        // check for Xhr response only
        if(!$request->ajax()){
            //@todo à tester
            abort(403, 'Unauthorized action.');
        }

        $gameLogic->setSymbols(Config::get('game.game.symbols'));

        if (
            empty($request->get('x')) || !is_numeric($request->get('x')) ||
            empty($request->get('y')) || !is_numeric($request->get('y')) ||
            empty($request->get('symbol')) || !in_array($request->get('symbol'), $gameLogic->getSymbols())
        ) {
            return $this->SendJsonResponse(null, 'Wrong Parameter 0x01');
        }

        //check square not already played
        if (!$gameLogic->isSquareAvailable($request->get('x'), $request->get('y'))) {
            return $this->SendJsonResponse(null, 'Square already checked');
        }

        //save sur la couche de persistence
        if (!$gameLogic->saveSquare($request->get('x'), $request->get('y'), $request->get('symbol'))) {
            return $this->SendJsonResponse(null, 'Error during save 0x02');
        }

        //check si gagné (a reporter dans la vue en js)
        if ($winner = $gameLogic->isGameWinned($request->get('symbol'))) {
            return $this->SendJsonResponse(true, null, null, $request->get('symbol'));
        }

        // check si la grille est pleine
        if (!$gameLogic->isSquareLeft()) {
            return $this->SendJsonResponse(true, null, true);
        }

        // return success
        return $this->SendJsonResponse(true);
    }

    /**
     * Send Json preFormated response
     *
     * @param null|true $result
     * @param null|string $error
     * @param null|bool $gameover
     * @param null|string $winner
     * @return JsonResponse
     */
    private function SendJsonResponse($result = null, $error = null, $gameover = false, $winner = null) {
        $ret = New \StdClass();
        $ret->error = $error;
        $ret->result = $result;
        $ret->gameover = (bool) $gameover;
        $ret->winner = $winner;

        return New JsonResponse($ret);
    }
}
