<?php

namespace App\Command;

use App\Service\BeveragePreparationService;
use App\Service\Exception\CannotPrepareException;
use App\Service\Exception\InvalidParameterException;
use App\Service\Exception\InvalidOptionException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'beverage:prepare',
    description: 'This command will allow you to prepare a new hot beverage.',
)]
class PrepareCommand extends Command
{
    use LockableTrait;

    protected BeveragePreparationService $beveragePreparationService;

    public function __construct(BeveragePreparationService $beveragePreparationService, string $name = null)
    {
        parent::__construct($name);
        $this->beveragePreparationService = $beveragePreparationService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'Beverage id')
            ->addArgument('amount', InputArgument::REQUIRED, 'Amount paid')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);

        if ($this->lock(null, true)) {
            $io->warning("Please wait for the other orders to be finished");
        }

        $id = $input->getArgument('id');
        $amount = $input->getArgument('amount');


        $io->success($this->beveragePreparationService->displayMenu());
        $io->success(sprintf('Your selection is "%s". Amount entered is %s', $id, $amount));
        try {
            $validOrder = $this->beveragePreparationService->validateParams($id, $amount);
            if ($validOrder) {
                $this->beveragePreparationService->prepare($id, $amount);
            }

            $this->release();
        } catch (InvalidParameterException $e) {
            $io->error("Please review your selection: {$e->getMessage()}");
        } catch (InvalidOptionException $e) {
            $io->error("Your selected option is invalid: {$e->getMessage()}");
        } catch (CannotPrepareException $e) {
            $io->warning($e->getMessage());
        }



//        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
