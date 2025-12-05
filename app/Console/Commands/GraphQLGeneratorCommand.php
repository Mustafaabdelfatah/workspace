<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class GraphQLGeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'graphql:generate {type}';

    /**
     * The console command description.
     */
    protected $description = 'Generate GraphQL Mutation, Type, or Query within a specific module ';

    /**
     * The console command type arrgument.
     */
    protected string $type;

    /**
     * The console command namespace parameter.
     */
    protected ?string $namespace;

    /**
     * Filesystem instance
     */
    protected Filesystem $files;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->type = $this->argument('type');

        $moduleName = Str::studly($this->ask('Enter the name of the module'));
        $fileName = Str::studly($this->ask('Enter the file name'));
        $this->namespace = Str::studly($this->ask('Enter the file namespace ,it can be empty !'));

        // Check module exists or it exists but disabled
        $module = Module::find($moduleName);

        if (!$module || $module->isStatus(false)) {
            $this->error("Module '$moduleName' does not exist or it disabled.");
            return;
        }

        // Handle command argument to create expeceted file
        if(in_array($this->type,['mutation','type','query'])){
            $this->generateFile($moduleName,$fileName);
        }else{
            $this->error('Invalid type. Supported types: mutation, type, query');
            return;
        }
    }

    /**
     * Generate graphQL type file to exists module
     *
     * @param string $moduleName
     * @param string $fileName
     *
     * @return void
     */
    private function generateFile($moduleName,$fileName): void
    {
        $stubContent = $this->getStubFileContent();

        $fileFullName = "$fileName".ucfirst($this->type);
        $newFileContent = str_replace('DummyNamespace',$this->getFullDirectoryPath($moduleName),$stubContent);
        $newFileContent = str_replace('DummyClass',$fileFullName,$newFileContent);

        $finalFullName = $fileFullName.".php";
        $filePath = base_path(join(DIRECTORY_SEPARATOR,[$this->getFullDirectoryPath($moduleName,true),$finalFullName]));

        $this->makeDirectory($this->getFullDirectoryPath($moduleName,true));
        $this->files->put($filePath, $newFileContent);

        $moduleConfigGraphqlPath = $this->makeModuleConfigGraphqlFile($moduleName); // create module related graphql.php file if not exists
        //$this->setCreateClassToModuleConfigGraphql($moduleConfigGraphqlPath,$moduleName,$fileFullName);

        $this->info("$finalFullName generated successfully in module $moduleName ! , Don't forget to regsiter it to $moduleName graphql config file !");
    }

    /**
     * Get stub file depend on command type arrgument
     *
     * @return string
     */
    private function getStubFilePath(): string
    {
        return base_path("stubs/graphql-laravel/$this->type.stub");
    }

    /**
     * Get directory full path
     *
     * @param string $moduleName
     * @param bool $withAppDir
     *
     * @return string
     */
    private function getFullDirectoryPath($moduleName,$withAppDir = false): string
    {
        if(!empty($this->namespace) && !is_null($this->namespace)){
            $array = ($withAppDir)
            ? array_merge(["Modules",$moduleName,"app","GraphQL",ucfirst(Str::plural($this->type))],$this->pretteyNamespace())
            : array_merge(["Modules",$moduleName,"GraphQL",ucfirst(Str::plural($this->type))],$this->pretteyNamespace());
        }else{
            $array = ($withAppDir) ? ["Modules",$moduleName,"app","GraphQL",ucfirst(Str::plural($this->type))] : ["Modules",$moduleName,"GraphQL",ucfirst(Str::plural($this->type))];
        }
        return join(DIRECTORY_SEPARATOR,$array);
    }

    /**
     * Prettey inserted namespace
     *
     * @return array
     */
    private function pretteyNamespace(): array
    {
        $namespaceArr = [];
        if(str_contains($this->namespace,"\\") && !str_contains($this->namespace,"/")){
            $namespaceArr = explode("\\",$this->namespace);
        }elseif(str_contains($this->namespace,"/") && !str_contains($this->namespace,"\\")){
            $namespaceArr = explode("/",$this->namespace);
        }else{
            $namespaceArr = [$this->namespace];
        }
        return array_map(function($singleNamespace) { return ucfirst($singleNamespace); }, $namespaceArr);
    }

    /**
     * Get stub file content depend on command type arrgument
     *
     * @return string
     */
    private function getStubFileContent(): string
    {
        return $this->files->get($this->getStubFilePath());
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $dirPath
     *
     * @return void
     */
    protected function makeDirectory($dirPath): void
    {
        if (! $this->files->isDirectory(base_path($dirPath))) {
            $this->files->makeDirectory(base_path($dirPath), 0777, true, true);
        }
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $dirPath
     *
     * @return string
     */
    protected function makeModuleConfigGraphqlFile($moduleName): string
    {
        $moduleConfigGraphqlFile = join(DIRECTORY_SEPARATOR,["Modules",$moduleName,"config","graphql.php"]);
        if(!$this->files->exists($moduleConfigGraphqlFile)){
            $newFileContent = $this->files->get(base_path("stubs/graphql-laravel/graphql.stub"));
            $this->files->put($moduleConfigGraphqlFile, $newFileContent);
        }
        return $moduleConfigGraphqlFile;
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $moduleConfigGraphqlPath
     * @param  string  $moduleName
     * @param  string  $fileFullName
     *
     * @return void
     */
    protected function setCreateClassToModuleConfigGraphql($moduleConfigGraphqlPath,$moduleName,$fileFullName): void
    {
        $moduleConfigGraphqlContent = include_once base_path($moduleConfigGraphqlPath);

        array_push($moduleConfigGraphqlContent[$this->type],join(DIRECTORY_SEPARATOR,[$this->getFullDirectoryPath($moduleName),$fileFullName."::class"]));

        $newFileContent = '<?php return '.var_export($moduleConfigGraphqlContent,true).';'.PHP_EOL;


        $this->files->put(base_path($moduleConfigGraphqlPath),$newFileContent);
    }
}
