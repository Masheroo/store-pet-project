<?php

namespace App\Command;

use App\Entity\CategoryField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(
    name: 'app:category-field:fill-slug',
    description: 'Add a short description for your command',
)]
class FillCategoryFieldSlugCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SluggerInterface $slugger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $batchSize = 100;
        $i = 0;
        $totalCount = 0;
        $query = $this->entityManager->createQuery('select field from App\Entity\CategoryField field');

        /** @var CategoryField $field */
        foreach ($query->toIterable() as $field) {
            $field->setSlug($this->slugger->slug($field->getName())->toString());
            ++$i;
            ++$totalCount;
            if ($i === $batchSize) {
                $this->entityManager->flush();
                $this->entityManager->clear();
                $i = 0;
            }
        }
        $this->entityManager->flush();
        $output->write('Всего сгенерировано: '.$totalCount);

        return Command::SUCCESS;
    }
}
