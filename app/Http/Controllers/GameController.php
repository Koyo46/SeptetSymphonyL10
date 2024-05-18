<?php

namespace App\Http\Controllers;

use App\Events\DealedCard;
use App\Events\GameStarted;
use App\Events\HandCards;
use App\Events\PlayerState;
use App\Events\TurnAdvanced;
use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Game;
use App\Models\OrderdFruit;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function createGame(Request $request)
    {
        $game = Game::create(['player_count' => $request->playerCount]);

        return response()->json(['gameId' => $game->id, 'playerCount' => $request->playerCount]);
    }

    public static function joinGame($gameId)
    {
        $game = Game::find($gameId);
        if (!$game) {
            return response()->json(['error' => '指定されたゲームが見つかりません。']);
        }
        return response()->json(['game' => $game]);
    }

    public static function getPlayers($gameId)
    {
        $players = Player::where('game_id', $gameId)->where('is_ready', true)->get();
        return response()->json(['players' => $players]);
    }

    public static function beReady($name, $gameId)
    {
        $player = new Player;
        $player->game_id = $gameId;
        $player->name = $name;
        $player->is_ready = true;
        $player->session_id = Str::random(10);
        $player->save();
        PlayerState::dispatch($player);

        return response()->json(['player' => $player]);
    }

    public static function startGame($gameId)
    {
        $game = Game::find($gameId);
        GameStarted::dispatch($game);
        return response()->json(['gameStarted' => true]);
    }

    public static function dealCard(int $gameId, int $playerCount)
    {
        $game = Game::find($gameId);
        $game->current_round += 1;
        $game->current_turn = 0;
        $game->save();
        $players = $game->players->where('is_ready', true);

        // プレイヤーの数だけランダムな順番を生成
        $order = range(0, $players->count() - 1);
        shuffle($order);
        // 各プレイヤーにランダムな順番を割り当てる
        foreach ($players as $index => $player) {
            $player->order = $order[$index];
            $player->save();
        }

        $cards = Card::all();
        shuffle($cards); // 配列をシャッフル
        $shuffledCards = $cards; // シャッフルされた配列を$shuffledCardsに代入

        foreach ($shuffledCards as $card) {
            $card;
            Deck::create([
                'game_id' => $gameId,
                'card_id' => $card->id
            ]);
        }

        foreach ($players as $player) {
            $cards=[];
            for ($j = 0; $j < 8; $j++) {
                $card = array_shift($shuffledCards);
                array_push($cards, $card->id);
            }
            $player->hand_card_id = $cards;
            $player->save();
        }

        $currentPlayer = $game->players->sortBy('order')->first();
        DealedCard::dispatch($game, $currentPlayer, $game->current_round);

        // 必要なデータを返す
        return response()->json([
            'currentPlayer' => $currentPlayer,
            'currentRound' => $game->current_round,
        ]);
    }

    public static function getMyHandCards($gameId)
    {
        $sessionId = request()->header('X-Session-ID');
        $player = Player::where('game_id', $gameId)->where('session_id', $sessionId)->first();
        if (!$player) {
            return response()->json(['error' => '不正なアクセスです。'], 403);
        }
        $handCardIds = json_decode($player->hand_card_id);
        $handCards = [];
        foreach ($handCardIds as $id) {
            $card = Card::findById($id);
            array_push($handCards, $card);
        }
        return response()->json(['handCards' => $handCards]);
    }
}
