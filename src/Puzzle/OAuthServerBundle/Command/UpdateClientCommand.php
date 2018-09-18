<?php

namespace Puzzle\OAuthServerBundle\Command;

use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Update OAuth Client
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class UpdateClientCommand extends Command
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
	
	/**
	 * @var Client $client
	 */
	private $client;
	
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
		->setName('puzzle:oauth-client:update')
		->setDescription('Update a client')
		->addArgument('clientId', InputArgument::REQUIRED, 'Get client ID')
		->setHelp(
				<<<EOT
                    The <info>%command.name%</info>command update an oauth client.
	
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
	    $this->client = $this->clientManager->findClientByPublicId($input->getArgument("clientId"));
	    dump($input->getArgument("clientId"));
	    while(!$this->name){
	        $question = new Question('Update name: ', $this->client->getName());
	        $question->setAutocompleterValues([$this->client->getName()]);
	        $this->name = $dialog->ask($input, $output, $question);
	    }
	    
	    while(!$this->host){
	        $question = new Question('Update host: ', $this->client->getHost());
	        $question->setAutocompleterValues([$this->client->getHost()]);
	        $this->host = $dialog->ask($input, $output, $question);
	    }
	    
	    $redirectUrisToRemove = [];
	    if (count($this->client->getRedirectUris()) > 0){
	        $question = new ChoiceQuestion('Select redirect uris to remove', $this->client->getRedirectUris());
	        $question->setMultiselect(true);
	        
	        while (!$redirectUrisToRemove){
	            $redirectUrisToRemove = $dialog->ask($input, $output, $question);
	        }
	    }
	    
	    while(!$this->redirectUris){
	        $this->redirectUris[] = $dialog->ask($input, $output, new Question('Set redirect uri to add: ', $this->client->getRedirectUris()));
	    }
	    
	    while ($dialog->ask($input, $output, new ConfirmationQuestion('Add another redirect-uri ? [Enter or y to continue|n to Cancel]',true))){
	        $question = new Question('Update host: ', $this->client->getRedirectUris());
	        $question->setAutocompleterValues($this->client->getRedirectUris());
	        
	        $this->redirectUris[] = $dialog->ask($input, $output, new Question('Set another redirect uri to add: '));
	    }
	    
	    $this->redirectUris = array_diff($this->redirectUris, $redirectUrisToRemove);
	    
	    $fullAllowedGrantTypes = ['authorization_code', 'password', 'token', 'client_credentials'];
	    $question = new ChoiceQuestion(
	        'Please update grant types',
	        $fullAllowedGrantTypes,
	        array_keys(array_intersect($fullAllowedGrantTypes, $this->client->getAllowedGrantTypes()))
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
        $io->title('Client Details Updated');

        $client = $this->client;
       
        $client->setName($this->name);
        $client->setHost($this->host);
        $client->setRedirectUris($this->redirectUris);
        $client->setAllowedGrantTypes($this->grantTypes);
        $client->setInterne(true);

        // Save the client
        $this->clientManager->updateClient($client);
    
        // Give the credentials back to the user
        $headers = ['Property', 'Value'];
        $rows = [
            ['Client Name', $client->getName()],
            ['Client Host', $client->getHost()],
            ['Client Redirect Uris', implode(',', $client->getRedirectUris())],
            ['Client Allowed Grant Types', implode(',', $client->getAllowedGrantTypes())],
        ];

        $io->table($headers, $rows);

        return 0;
    }
}