<?php
namespace Rd7\Autodeploy\Git;

use Illuminate\Support\Str;

class GitRepository extends \Cz\Git\GitRepository
{
    public function __construct($folderGit)
    {
        $this->repository = $folderGit;
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
