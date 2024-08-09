<?php

declare(strict_types=1);

namespace App\Command;

use App\Parser;
use LogicException;
use PhpParser\Error;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FindCandidatesCommand extends Command
{
    public function __construct(private readonly Parser $parser)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:find-candidates')
            ->addArgument('path', InputArgument::REQUIRED, 'path')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');

        $finder = new Finder();

        $phpParser = (new ParserFactory())->createForNewestSupportedVersion();

        $files = $finder->in($path)->files()->name(['*.php']);
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $content = file_get_contents($file->getRealPath());
            if (!$content) {
                throw new LogicException();
            }
            try {
                $ast = $phpParser->parse($content);
                if (null === $ast) {
                    throw new LogicException('AST is null');
                }
                $testabilityScoreResults = $this->parser->calculateTestabilityScore($ast);
                if ($testabilityScoreResults) {
                    foreach ($testabilityScoreResults as $methodName => $score) {
                        $output->writeln(sprintf('%s:%s - %s', $file->getRealPath(), $methodName, $score));
                    }
                }
            } catch (Error $error) {
                throw new LogicException($error->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
