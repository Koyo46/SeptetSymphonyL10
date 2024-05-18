<?php

namespace App\Models;

class Card
{
    public $id;
    public $suit;

    public function __construct($attributes = [])
    {
        $this->id = $attributes['id'] ?? null;
        $this->suit = $attributes['suit'] ?? null;
    }

    public static function all()
    {
        $cards = require database_path('data/cards.php');
        return array_map(function ($card) {
            return new static($card);
        }, $cards);
    }

    public static function find($id)
    {
        return static::all()[$id];
    }

    public static function findById($id)
    {
        return array_filter(static::all(), function ($card) use ($id) {
            return $card->id == $id;
        });
    }
}
