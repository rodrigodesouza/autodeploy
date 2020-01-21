<?php
namespace Rd7\Autodeploy\Git;

use Illuminate\Support\Str;

class GitRepository extends \Cz\Git\GitRepository
{
    public function __construct($folderGit)
    {
        $this->repository = $folderGit;
    }

    public function configInit($config)
    {
        $this->verificaBranchDestino($config['destino']);

        return $this->makeHookGit($config);

    }

    private function getIdProject() {
        $r = $this->extractFromCommand('git remote -v  2>&1', function($value) {
            $urlgit = explode(" ", $value);
            $urlgit = trim(substr($value, 6));
            if (Str::contains($urlgit, "git@") and Str::contains($urlgit, ":")) {
                $projeto = explode(':', $urlgit);
                $projetoName = explode(" ", str_replace("/", "%2F", str_replace(".git", "", $projeto[1])));
                return trim($projetoName[0]);
            }
            if (Str::contains($urlgit, "http://") || Str::contains($urlgit, "https://")) {
                $projeto = explode('/', $urlgit);
                $projetoNome = end($projeto);
                $grupo = $projeto[count($projeto) - 2];
                $projetoName = $grupo . "%2F" . str_replace(".git", "", $projetoNome);

                return trim($projetoName);
            }
        });

        return $r[0];
    }

    private function makeHookGit($config)
    {

        $rm = $this->getIdProject();
        print_r($rm);
        exit();

        $urlRecept          = $this->getUriGitServer($config);
        $git_access_token   = env('GIT_ACCESS_TOKEN');
        $git_project_id     = $this->getIdProject();

        $data["url"]                       = $urlRecept;
        $data["push_events"]               = true;
        $data["project_id"]                = $git_project_id;
        $data["push_events_branch_filter"] = $config['destino'];
        $data["enable_ssl_verification"]   = false;
        // $data["tag_push_events"] = false;
        // $data["merge_requests_events"] = false;
        // $data["repository_update_events"] = false;
        // $data["issues_events"] = false;
        // $data["confidential_issues_events"] = false;
        // $data["note_events"] = false;
        // $data["confidential_note_events"] = null;
        // $data["pipeline_events"] = false;
        // $data["wiki_page_events"] = false;
        // $data["job_events"] =  false;

        // $dataHeader["Authorization"]             = "Bearer " . $git_access_token;
        // $dataHeader["PRIVATE-TOKEN"]             = $git_access_token;
        // $dataHeader["Content-Type"]             = "application/json";

        $dataHeader = [
            "PRIVATE-TOKEN: $git_access_token",
            "Content-Type: application/json",
            "Accept: application/json"
        ];

        $urlServer = $this->getUrlGitServer($config, $git_project_id);

        // echo $urlServer . "<br>";
        // echo $git_access_token . "<br>";
        $ch_git = curl_init();

        curl_setopt($ch_git, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch_git, CURLOPT_HTTPHEADER, $dataHeader);
        curl_setopt($ch_git, CURLOPT_URL, $urlServer);
        curl_setopt($ch_git, CURLOPT_POST, true);

        $payload = json_encode($data, JSON_PRETTY_PRINT);
        curl_setopt($ch_git, CURLOPT_POSTFIELDS, $payload);

        $res    = curl_exec($ch_git);
        $resp   = json_decode($res);

        if (isset($resp->error)) {
            return ['success' => false , 'msg' => 'GIT api response: ' . $resp->error];
        }
        if (isset($resp->message)) {
            return ['success' => false , 'msg' => 'GIT api response: ' .$resp->message];
        }
        if (isset($resp->id) and isset($resp->url)) {
            return ['success' => true , 'msg' => 'Git Webhook criado!! Verifique se o link está acessível: ' . $resp->url];
        }

        // print_r($resp);
        // return json_decode(json_encode($res), true);
        // echo '<pre>';
        // print_r(json_decode($res));
        // echo '</pre>';
    }

    private function getUriGitServer($server)
    {
        if ($server['repository_server'] == 'gitlab') {
            return $server['url_recept'] . '/autodeploy/' . $server['repository_server'];
        }
    }
    private function getUrlGitServer($config, $git_project_id)
    {
        if ($config['repository_server'] == 'gitlab') {
            return 'https://gitlab.com/api/v4/projects/' . $git_project_id . '/hooks';
        }
    }

    private function verificaBranchDestino($destino)
    {
        $branch     =  $destino;
        // $co         = (new \Rd7\Autodeploy\Config\GetConfig())->getConfig();
        $branches   = $this->getLocalBranches();

        if (!in_array($branch, $branches)) {
            $this->createBranch($branch);
            echo "branch \"{$branch}\" created";
        }
    }

    public function getRepositoryServer()
    {
        $remote = shell_exec('git remote -v  2>&1');

        if (Str::contains($remote, "gitlab")) {
            return 'gitlab';
        }
        if (Str::contains($remote, "github")) {
            return 'github';
        }
    }

    public function getLog()
    {
        $git_logs = $this->execute('log');
        // return $repo->getLocalBranches();
        $last_hash = null;

        foreach ($git_logs as $line) {
            // Clean Line
            $line = trim($line);
            // Proceed If There Are Any Lines
            if (!empty($line)) {
                // Commit
                if (strpos($line, 'commit') !== false) {
                    $hash = explode(' ', $line);
                    $hash = trim(end($hash));
                    $git_history[$hash] = [
                        'message' => ''
                    ];
                    $last_hash = $hash;
                }
                // Author
                else if (strpos($line, 'Author') !== false) {
                    $author = explode(':', $line);
                    $author = trim(end($author));
                    $git_history[$last_hash]['author'] = $author;
                }
                // Date
                else if (strpos($line, 'Date') !== false) {
                    $date = explode(':', $line, 2);
                    $date = trim(end($date));
                    $git_history[$last_hash]['date'] = date('d/m/Y H:i:s A', strtotime($date));
                }
                // Message
                else {
                    $git_history[$last_hash]['message'] .= $line ." ";
                }
            }
        }
        dd($git_history);

    }

    /*

     // Change To Repo Directory
     chdir(__DIR__ . "/../../../.git");
     // Load Last 10 Git Logs
     $git_history = [];
     $git_logs = [];
     exec("git log -10", $git_logs);
     // Parse Logs
     $last_hash = null;

     dd($git_logs);
     foreach ($git_logs as $line)
     {
         // Clean Line
         $line = trim($line);
         // Proceed If There Are Any Lines
         if (!empty($line))
         {
             // Commit
             if (strpos($line, 'commit') !== false)
             {
                 $hash = explode(' ', $line);
                 $hash = trim(end($hash));
                 $git_history[$hash] = [
                     'message' => ''
                 ];
                 $last_hash = $hash;
             }
             // Author
             else if (strpos($line, 'Author') !== false) {
                 $author = explode(':', $line);
                 $author = trim(end($author));
                 $git_history[$last_hash]['author'] = $author;
             }
             // Date
             else if (strpos($line, 'Date') !== false) {
                 $date = explode(':', $line, 2);
                 $date = trim(end($date));
                 $git_history[$last_hash]['date'] = date('d/m/Y H:i:s A', strtotime($date));
             }
             // Message
             else {
                 $git_history[$last_hash]['message'] .= $line ." ";
             }
         }
     }
     dd($git_history);
     echo "<pre>";
     print_r($git_history);
     echo "</pre>";

     */

}
