<?php
namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Console\App\KeyGenerator;
use Console\App\HmacCalculator;
use Console\App\TableGenerator;
use Console\App\Rules;

class GameCommand extends Command
{
    protected function configure()
    {
        $this->setName('game')
            ->setDescription('Plays the rock-paper-scissors game')
            ->addArgument('moves', InputArgument::IS_ARRAY, 'List of moves')
            ->setHelp('Pass an odd number of unique moves to play the game, e.g. "php game.php rock paper scissors');
    }

    private function selectComputerMove(array $moves)
    {
        $randomIndex = array_rand($moves);
        $computerMove = $moves[$randomIndex];

        return $computerMove;
    }

    private function getUserMove(InputInterface $input, OutputInterface $output, array $moves): string
    {
        $helper = $this->getHelper('question');
        $question = new Question('Enter your move (0-' . count($moves) . '): ');

        $validator = function ($answer) use ($moves) {
            $index = $answer;
            if ($index === '0') {
                return 'exit';
            }
            if ($index == '?') {
                return 'help';
            }
            if (!isset($moves[$index - 1])) {
                throw new \RuntimeException('Invalid move. Please choose a valid move.');
            }
            return $moves[$index - 1];
        };

        $question->setValidator($validator);

        $userMove = $helper->ask($input, $output, $question);

        return $userMove;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $moves = $input->getArgument('moves');

        if (count($moves) < 3 || count($moves) % 2 === 0 || count(array_unique($moves)) !== count($moves)) {
            $output->writeln('Invalid arguments. Please provide an odd number of unique moves.');
            $output->writeln('Example: php bin/console rock paper scissors');
            return Command::FAILURE;
        }

        $move1 = isset($moves['0']) ? $moves['0'] : '';
        $move2 = isset($moves['1']) ? $moves['1'] : '';
        $move3 = isset($moves['2']) ? $moves['2'] : '';
        $move4 = isset($moves['3']) ? $moves['3'] : '';
        $move5 = isset($moves['4']) ? $moves['4'] : '';
        $move6 = isset($moves['5']) ? $moves['5'] : '';
        $move7 = isset($moves['6']) ? $moves['6'] : '';

        $winningMoves = [
            $move1 => [$move5, $move6, $move7],
            $move2 => [$move6, $move7, $move1],
            $move3 => [$move7, $move1, $move2],
            $move4 => [$move1, $move2, $move3],
            $move5 => [$move2, $move3, $move4],
            $move6 => [$move3, $move4, $move5],
            $move7 => [$move4, $move5, $move6]
        ];

        $keyGenerator = new KeyGenerator();
        $key = $keyGenerator->generateKey();

        $computerMove = $this->selectComputerMove($moves);

        $hmacCalculator = new HmacCalculator();
        $hmac = $hmacCalculator->calculateHmac($computerMove, $key);

        $output->writeln('HMAC: ' . $hmac);
        $output->writeln('Available moves:');

        $movesTable = new TableGenerator();
        $movesTable->generateTable($output, $moves);

        $userMove = $this->getUserMove($input, $output,$moves);

        if ($userMove == 'exit') {
            $output->writeln('Exit the game');
            return Command::SUCCESS;
        } if ($userMove == 'help') {
            $rulesTable = new TableGenerator();
            $rules = new Rules();

            $rulesTable->generateRulesTable($output, $moves, $winningMoves, $rules);

            $userMove = $this->getUserMove($input, $output,$moves);
        }

        $rules = new Rules();
        $winner = $rules->determineWinner($userMove, $computerMove, $winningMoves);

        $output->writeln('Your move: ' . $userMove);
        $output->writeln('Computer move: ' . $computerMove);

        if ($winner === 'tie') {
            $output->writeln('It\'s a tie!');
        } elseif ($winner === $userMove) {
            $output->writeln('You win!');
        } elseif ($winner === $computerMove) {
            $output->writeln('Computer wins!');
        } else {
            $output->writeln('Invalid move. Please choose a valid move.');
            return Command::FAILURE;
        }
        $output->writeln('HMAC key: ' . $key);

        return Command::SUCCESS;
    }
}
