<?php
namespace Console\App;

use Symfony\Component\Console\Command\Command;
class Rules
{
    public function determineWinnersTable(string $userMove, string $computerMove, array $winningMoves): string
    {
        if (isset($winningMoves[$userMove]) && in_array($computerMove, $winningMoves[$userMove])) {
            return 'Win';
        } elseif (isset($winningMoves[$computerMove]) && in_array($userMove, $winningMoves[$computerMove])) {
            return 'Lose';
        } else {
            return 'Draw';
        }
    }

    public function determineWinner(string $userMove, string $computerMove, array $winningMoves): string
    {
        if (isset($winningMoves[$userMove]) && in_array($computerMove, $winningMoves[$userMove])) {
            return $userMove;
        } elseif (isset($winningMoves[$computerMove]) && in_array($userMove, $winningMoves[$computerMove])) {
            return $computerMove;
        } else {
            return 'tie';
        }
    }
}