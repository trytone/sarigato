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
	
	private $files = [
		'success' => ".\\output\\success.csv",
		'error'   => ".\\output\\error.csv",
		'summary' => ".\\output\\summary.csv"
	];

    protected function configure()
    {
        $this
            ->setName('email:check')
            ->setDescription('E-mail validation command. Pass absolute path to CSV file as argument.')
            ->addArgument('path', InputArgument::REQUIRED, 'Absolute path to CSV file.');
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
		
			$this->eraseFiles();

            foreach($data as $row){
			  if(count($row) > 0){ // check column numbers more than 0
				  $email = $row[0]; // get first column
				  if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					  $this->write('success', "$email\n");
					  $this->write('summary', "SUCCESS: $email\n");
				  } else {
					  $this->write('error', "$email\n");
					  $this->write('summary', "ERROR: $email\n");
				  }
			  }
            }
            $output->writeln("Done! Check out your output directory.");
        } else {
            $output->writeln("File not exists.");
        }
        return Command::SUCCESS;
    }

    private function write(string $key, string $content){
      file_put_contents($this->files[$key], $content, FILE_APPEND);
    }
	
	private function eraseFiles(){
		foreach($this->files as $key => $path){
			file_put_contents($path, '');
		}
    }	
}
