<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;

class FetchPostsCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:fetch-posts')
            ->setDescription('Fetch posts from external API and save them to the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://jsonplaceholder.typicode.com/posts');
        $postsData = $response->toArray();

        foreach ($postsData as $postData) {
            $userResponse = $httpClient->request('GET', 'https://jsonplaceholder.typicode.com/users/' . $postData['userId']);
            $userData = $userResponse->toArray();

            $post = new Post();
            $post->setTitle($postData['title']);
            $post->setBody($postData['body']);
            $post->setName($userData['name']);

            $this->entityManager->persist($post);
        }

        $this->entityManager->flush();

        $output->writeln('Posts fetched and saved successfully.');

        return Command::SUCCESS;
    }
}
