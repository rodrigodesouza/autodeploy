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
        // $origem = $this->argument('origem');
        // if (!isset($origem) || !$origem) {
        // }
        $config = [];
        $co         = (new \Rd7\Autodeploy\Config\GetConfig())->getConfig();
        $branchAtual   = (new GitRepository($co->get('folder_name_git')))->getCurrentBranchName();
        $branches   = (new GitRepository($co->get('folder_name_git')))->getLocalBranches();

        // print_r($co->get('folder_name_git'));

        $config['repositoryServer']   = (new GitRepository($co->get('folder_name_git')))->getRepositoryServer();

        // print_r($repositoryServer);



        $config['origem'] = $this->anticipate('Qual o branch de origem?', $branches, $branchAtual);
        // echo $origem;

        $config['destino'] = $this->ask('Qual o branch de destino?');
        // echo $destino;
        // $destino = $this->argument('destino');
        // if (!isset($destino) || !$destino) {
        // }

        $config['endereco'] = $this->ask('endereco');
        // if (!isset($endereco) || !$endereco) {
        // }
        // $endereco = $this->ask('(Criando hooks) Informe o endereco que vai receber as atualizacoes');
        // echo $endereco;
        print_r($config);
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
