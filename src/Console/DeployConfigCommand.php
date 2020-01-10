<?php

namespace Rd7\Autodeploy\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Rd7\Autodeploy\Git\GitRepository;
use Rd7\Autodeploy\Config\GetConfig;

class DeployConfigCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'deploy:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configura um novo branch para deploy';

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
        $config         = [];
        $co             = (new \Rd7\Autodeploy\Config\GetConfig())->getConfig();
        $branchAtual    = (new GitRepository($co->get('folder_name_git')))->getCurrentBranchName();
        $branches       = (new GitRepository($co->get('folder_name_git')))->getLocalBranches();

        $config['repository_server'] = (new GitRepository($co->get('folder_name_git')))->getRepositoryServer();
        $config['origem']            = $this->anticipate('Qual o branch de origem?', $branches, $branchAtual);
        $config['destino']           = $this->ask('Qual o branch de destino?');
        $config['url_recept']        = $this->ask('(Criando hooks) Informe o endereco que vai receber as atualizacoes');
        // $config['git_project_id'] = $this->ask('(Criando hooks) Informe o id do projeto no ' . $config['repositoryServer']);

        $returnGitApi   = (new GitRepository($co->get('folder_name_git')))->configInit($config);
        $success        = ($returnGitApi['success'] == true) ? 'info' : 'false';

        $this->$success($returnGitApi['msg']);

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['origem', InputArgument::OPTIONAL, 'Branch de trabalho a mesclar com destino.'],
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
            // ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
