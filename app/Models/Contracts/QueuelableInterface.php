<?php

namespace App\Models\Contracts;

interface QueuelableInterface
{
    public static function getQueueConnection(): string;
}
