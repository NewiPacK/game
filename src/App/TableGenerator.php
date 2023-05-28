<?php
namespace Console\App;

use Symfony\Component\Console\Helper\Table;

class TableGenerator
{
    public function generateTable($output, $moves)
    {
        $table = new Table($output);
        $table->setHeaders(['Move']);

        foreach ($moves as $index => $move) {
            $table->addRow([$index + 1 . ' - ' . $move]);
        }

        $table->addRow(['0 - exit']);
        $table->addRow(['? - help']);
        $table->render();

        return $table;
    }

    public function generateRulesTable($output, array $moves, $winningMoves, Rules $rules)
    {
        $table = new Table($output);

        $headers = ['Moves'];
        foreach ($moves as $move) {
            $headers[] = $move;
        }
        $table->setHeaders($headers);

        foreach ($moves as $index => $move) {
            $row = [$move];
            foreach ($moves as $innerMove) {
                $result = $rules->determineWinnersTable($move, $innerMove, $winningMoves);
                $row[] = $result;
            }
            $table->addRow($row);
        }
        $table->render();

        return $table;
    }
}
