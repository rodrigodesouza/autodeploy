<?php

namespace Rd7\Autodeploy\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;
use Rd7\Autodeploy\Git\GitRepository;
use Rd7\Autodeploy\Config\GetConfig;

class DeployCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'deploy:push {commit?} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Faz o commit no branch de trabalho e no branch de produção. Executa comandos Shell.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $commit = $this->argument('commit');

        $co         = (new \Rd7\Autodeploy\Config\GetConfig())->getConfig();

        $config['repository_server'] = (new GitRepository($co->get('folder_name_git')))->getRepositoryServer();

        $branch = (new GitRepository($co->get('folder_name_git')))->getHooksList($config);

        print_r($branch);
        exit;

        if (!isset($branch) || !$branch) {
            $branch = $this->ask('Qual a descrição do seu commit?');
        }

        if (!isset($commit) || !$commit) {
            $commit = $this->ask('Qual a descrição do seu commit?');
        }

        if (count(config('autodeploy.commands.local')) > 0) {
            $errors = 0;
            $msg    = "";

            foreach(config('autodeploy.commands.local') as $command) {
                $success = true;
                // $prefixo = "cd " . config('autodeploy.folder_git');
                $command = str_replace("{para}", $this->option('to'), $command);
                $command = str_replace("{de}", config('autodeploy.deploy_de'), $command);
                $command = str_replace("{commit}", $commit, $command);

                $prefixo = $command;
                // $prefixo .= " && " . $command;

                $this->info($command);

                $this->verificaBranch($command);

                $shell =  shell_exec($prefixo . " 2>&1");
                $needles = config('autodeploy.errors_log');

                $t = preg_match_all( '/\\b(' . join( $needles, '|' ) . ')\\b/i', $shell, $m, PREG_OFFSET_CAPTURE );

                if ($t != 0) {
                    $this->error($shell);

                    $errors += 1;
                    $msg = "Aconteceu algum erro.";
                    $success = false;

                    $this->task('command: ' . $command, function () use ($success) {
                        return $success;
                    });

                    break;
                }

                echo $shell;

                $this->task('command: ' . $command, function () use ($success) {
                    return $success;
                });

            }

            if (config('autodeploy.desktop_notification')) {
                $notifier = NotifierFactory::create();
                $notification = (new Notification())->setTitle('Laravel Autodeploy');

                if ($errors == 0) {
                    $notification
                        ->setBody("Todos os processos foram concluidos! 😎")
                        ->setIcon(__DIR__ . "/../Resources/icons/icon-success.png");

                } else {
                    $notification
                        ->setBody($msg . " 😱")
                        ->setIcon(__DIR__ . "/../Resources/icons/error.png");
                }

                $notifier->send($notification);
            }

        }

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['commit', InputArgument::OPTIONAL, 'A descricão do commit.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['to', null, InputOption::VALUE_OPTIONAL, 'An example option.', config('autodeploy.deploy_para')],
        ];
    }

    private function verificaBranch($command)
    {
        $arrCommand = explode(" ", $command);

        if (in_array('git', $arrCommand) and in_array('checkout', $arrCommand)) {
            $branch     =  end($arrCommand);
            $co         = (new \Rd7\Autodeploy\Config\GetConfig())->getConfig();
            $branches   = (new GitRepository($co->get('folder_name_git')))->getLocalBranches();

            if (!in_array($branch, $branches)) {
                (new GitRepository($co->get('folder_name_git')))->createBranch($branch);
                $this->info("branch \"{$branch}\" created");
            }

        }
    }
}
