<?php

namespace Puzzle\OAuthServerBundle\Command;

use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Create a New OAuth Client
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class CreateClientCommand extends Command
{
    /**
     * @var ClientManagerInterface $clientManager
     */
    private $clientManager;
    
    /**
     * @var string $name
     */
    private $name;
    
    /**
     * @var string $host
     */
    private $host;
    
    /**
     * @var array $redirectUris
     */
    private $redirectUris;
    
    /**
     * @var array $redirectUris
     */
    private $grantTypes;
    
    public function __construct(ClientManagerInterface $clientManager) {
        parent::__construct();
        $this->clientManager = $clientManager;
    }

	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure()
	{
		$this
    		->setName('puzzle:oauth-client:create')
    		->setDescription('Creates a new oauth client')
    		->setHelp(
				<<<EOT
                    The <info>%command.name%</info>command creates a new oauth client.
	 <info>php %command.full_name% [--redirect-uri=...] [--grant-type=...] name</info>
EOT
		);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Console\Command\Command::interact()
	 */
	protected function interact(InputInterface $input, OutputInterface $output)
	{
	    $dialog = $this->getHelper('question');
		
		while(!$this->name){
		    $this->name = $dialog->ask($input, $output, new Question('Set name for client: '));
		}
		
		while(!$this->host){
		    $this->host = $dialog->ask($input, $output, new Question('Set host for client: '));
		}
		
		while(!$this->redirectUris){
		    $this->redirectUris[] = $dialog->ask($input, $output, new Question('Set redirect uri for client: '));
		}
		
		while ($dialog->ask($input, $output, new ConfirmationQuestion('Add another redirect-uri ? [Enter or y to continue|n to Cancel]',true))){
		    $this->redirectUris[] = $dialog->ask($input, $output, new Question('Set another redirect uri for client: '));
		}
		
		$question = new ChoiceQuestion(
		    'Please select grant types',
		    ['authorization_code', 'password', 'token', 'client_credentials']
		    );
		$question->setMultiselect(true);
		
		while(!$this->grantTypes){
		    $this->grantTypes = $dialog->ask($input, $output, $question);
		}
		
		if(in_array('authorization_code', $this->grantTypes)){
		    $this->grantTypes[] = 'refresh_token';
		}
	}

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Client Credentials');

        $client = $this->clientManager->createClient();
       
        $client->setName($this->name);
        $client->setHost($this->host);
        $client->setRedirectUris($this->redirectUris);
        $client->setAllowedGrantTypes($this->grantTypes);
        $client->setInterne(true);

        // Save the client
        $this->clientManager->updateClient($client);

        // Give the credentials back to the user
        $headers = ['Client ID', 'Client Secret'];
        $rows = [
            [$client->getPublicId(), $client->getSecret()],
        ];

        $io->table($headers, $rows);

        return 0;
    }
}
