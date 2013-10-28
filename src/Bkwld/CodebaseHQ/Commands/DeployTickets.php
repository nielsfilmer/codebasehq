<?php namespace Bkwld\CodebaseHQ\Commands;

// Dependencies
use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;
use SimpleXMLElement;
use Symfony\Component\Console\Input\InputOption;
use Bkwld\CodebaseHQ\Exception;

class DeployTickets extends Command {
	
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'codebasehq:deploy-tickets';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Pass git logs via STDIN and update all referenced tickets';

	/**
	 * The arguments
	 */
	protected function getArguments() {
		return array(
			array('env', InputOption::VALUE_REQUIRED, 'The enviornment name being deployed to'),
		);
	}

	/**
	 * Inject dependencies
	 * @param Bkwld\CodebaseHQ\Request $request
	 */
	public function __construct($request) {
		$this->request = $request;
		parent::__construct();
		
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire() {
		
		// Require enviornment to be passed in
		$enviornment = $this->argument('env');
		
		// Get info of person running the deploy
		$name = trim(`git config --get user.name`);
		$email = trim(`git config --get user.email`);
		
		// Figure out the Codebase repo name
		preg_match('#/([\w-]+)\.git$#', trim(`git config --get remote.origin.url`), $matches);
		$repo = $matches[1];
		
		// Loop through STDIN and find ticket references
		$commit= null;
		$deployed = array();
		while ($line = fgets(STDIN)) {
			
			// Check for a commit hash
			if (preg_match('#^commit (\w+)$#', $line, $match)) {
				$commit = $match[1];
			}
			
			// Check for a ticket and add to the deployed array
			if (preg_match_all('#\[(?:touch|complete):(\d+)\]#', $line, $matches)) {
				foreach($matches[1] as $ticket) {
					if (empty($deployed[$ticket])) $deployed[$ticket] = array();
					if (in_array($commit, $deployed[$ticket])) continue;
					$deployed[$ticket][] = $commit;
				}
			}
		}
		
		// Loop through those and creat ticket comments in codebase
		foreach($deployed as $ticket => $commits) {

			// Prepare message
			$date = new DateTime();
			$date->setTimezone(new DateTimeZone('America/Los_Angeles'));
			$date = $date->format('l, F jS \a\t g:i A T');
			$enviornment = empty($enviornment) ? '' : ", to **{$enviornment}**,";

			// Singular commits
			if (count($commits) === 1) {
				$message = "Note: [{$name}](mailto:{$email}) deployed{$enviornment} a commit that references this ticket on {$date}.\n\nThe commits was: {commit:{$repo}/{$commit}}";
			
			// Plural commits
			} else {
				$message = "Note: [{$name}](mailto:{$email}) deployed{$enviornment} commits that reference this ticket on {$date}.\n\nThe commits were:\n\n";
				foreach($commits as $commit) { $message .= "- {commit:{$repo}/{$commit}}\n"; }
			}

			// Create XML request
			$xml = new SimpleXMLElement('<ticket-note/>');
			$xml->addChild('content', $message);

			// Submit it.  Will throw exception on failure
			$this->request->call('POST', 'tickets/'.$ticket.'/notes', $xml->asXML());
		}
		
		// Ouptut status
		$this->info(count($deployed).' ticket(s) found and updated');
		
	}
	
}