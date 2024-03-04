<?php

namespace App\Command;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(
    name: 'app:category:fill-slug',
    description: 'Add a short description for your command',
)]
class FillCategorySlugCommand extends Command
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
        $query = $this->entityManager->createQuery('select category from App\Entity\Category category');

        /** @var Category $category */
        foreach ($query->toIterable() as $category) {
            $category->setSlug($this->slugger->slug($category->getName())->toString());
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
