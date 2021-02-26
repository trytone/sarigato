<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class EmailValidationCommand extends Command
{
    protected static $defaultName = 'email:check';

    protected function configure()
    {
        $this
            ->setName('email:check')
            ->setDescription('E-mail validation command. Pass path to CSV file as argument.')
            ->addArgument('path', InputArgument::REQUIRED, 'File path to CSV file.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        if (file_exists($path)) {
            $data = $serializer->decode(file_get_contents($path), 'csv', [
                CsvEncoder::KEY_SEPARATOR_KEY => "\n",
                CsvEncoder::NO_HEADERS_KEY => true,
            ] );

            foreach($data as $emailArray){
              $email = $emailArray[0]; // first column
              if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                  $this->write('success.csv', "$email\n");
                  $this->write('summary.csv', "SUCCESS: $email\n");
              } else {
                  $this->write('error.csv', "$email\n");
                  $this->write('summary.csv', "ERROR: $email\n");
              }
            }
            $output->writeln("Done. Check out success.csv, error.csv and summary.csv files.");
        } else {
            $output->writeln("File not exists.");
        }
        return Command::SUCCESS;
    }

    private function write(string $path, string $content): void
    {
      file_put_contents($path, $content, FILE_APPEND);
    }
}
