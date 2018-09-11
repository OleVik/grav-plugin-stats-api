# Advanced usage

If you want to get the statistics of several sites at once, this should be easy for any task runner to accomplish. For example, using PHP (7.1) with Symfony's libraries, here's how to do it:

## 1. Create a directory to hold the files in

For example "StatsAPICommand", with the following files and folders beneath it:

```
StatsAPICommand
|   composer.json
|   composer.lock
|   config.yaml
+---bin
|       console.php
+---data
+---src
|   \---Command
|           DownloadStatsCommand.php
\---vendor
```

## 2. Fill necessary files

"Config.yaml" needs your list of target sites, like this:

```yaml
targets: 
  - 
    token: NVrzcU3h2hXuhZCJYZ6KUP29
    url: "http://grav.local/stats-api/daily"
  - 
    token: sD9VtL2qXSvbpMdw3qXWFLc7
    url: "http://grav.remote/stats-api/daily"
  - 
    token: xrLprnt8tfZLB9tyXcwXyRbh
    url: "http://grav.somewhere/stats-api/daily"
```

"Composer.json" needs our [Composer](https://getcomposer.org/ "The package manager for PHP") setup:

```json
{
    "name": "olevik/stats-api-command",
    "description": "Query several Grav sites for stats and save them locally.",
    "type": "project",
    "license": "MIT",
    "authors": [
        {
            "name": "Ole Vik",
            "email": "OleVik@users.noreply.github.com"
        }
    ],
    "minimum-stability": "stable",
    "require": {
        "symfony/console": "^4.1",
        "symfony/yaml": "^4.1",
        "symfony/filesystem": "^4.1"
    },
    "autoload": {
        "psr-4": {"App\\": "src/"}
    }
}
```

"bin/console.php" needs some simple instructions:

```php
#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$application = new Symfony\Component\Console\Application;
$application->add(new App\Command\DownloadStatsCommand);
$application->run();
```

"src/Command/DownloadStatsCommand.php" needs the script we're using:

```php
<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

final class DownloadStatsCommand extends Command
{
    protected function configure()
    {
        $this->setName('download');
        $this->setDescription('Downloads files defined in config.yaml');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileSystem = new Filesystem();
        $dataDir = 'data';
        $configFile = 'config.yaml';

        $output->writeln('<info>Looking for data directory ...</info>');
        if (!$fileSystem->exists($dataDir)) {
            try {
                $fileSystem->mkdir($dataDir);
            } catch (IOExceptionInterface $exception) {
                throw new IOExceptionInterface('Error creating ' . $exception->getPath());
            }
        }
        $output->writeln('Found or created data directory.');

        $output->writeln('<info>Looking for config file ...</info>');
        if ($fileSystem->exists($configFile)) {
            $output->writeln('Found config file.');
            try {
                $config = file_get_contents($configFile);
                $yaml = Yaml::parse($config);
                $output->writeln('Parsed config file.');
            } catch (ParseException $exception) {
                throw new IOExceptionInterface('YAML error ' . $exception->getMessage());
            }
        } else {
            $output->writeln('<error>Could not find config file.</error>');
            exit();
        }
        foreach ($yaml['targets'] as $target) {
            $time = date('Y-m-d');
            $name = explode('/', $target['url']);
            $host = parse_url($target['url'], PHP_URL_HOST);
            $name = end($name);
            $file = $dataDir . '/' . $host . '/' . $time . '/' . $name . '.json';
            $target = $target['url'] . '?AUTH_TOKEN=' . $target['token'];
            $output->writeln('<info>Querying ' . $target . '</info>');

            $output->writeln($dataDir . '/' . $host . '/' . $time . '/' . $name . '.json');

            try {
                $data = file_get_contents($target);
                $fileSystem->dumpFile($file, $data);
                $output->writeln('Saved data to ' . $file);
            } catch (IOExceptionInterface $exception) {
                throw new IOExceptionInterface('Error creating ' . $exception->getPath());
            }
        }
        return 0;
    }
}
```

## 3. Install dependencies

[Install Composer](https://getcomposer.org/download/) if you haven't already (I assume you have PHP installed and in environment paths), and run `composer install` from a command line terminal window, opened or navigated to the folder the script resides in. This installs the needed Symfony-components needed to run the script.

Finally, run `bin/console download` in the same window. Or, if you're Windows: `php bin/console.php download`. Files are downloaded into the data-folder, where each site gets its own folder, and in this folder a dated-folder named in the format "year-month-day" (numericals), wherein the statistics-file is saved. For example:

```
StatsAPICommand
+---data
|   \---grav.local
|       \---2018-09-08
|               daily.json
|   \---grav.remote
|       \---2018-09-08
|               daily.json
|   \---grav.somewhere
|       \---2018-09-08
|               daily.json
```

Example command output:

```
λ php bin/console.php download
Looking for data directory ...
Found or created data directory.
Looking for config file ...
Found config file.
Parsed config file.
Querying http://grav.local/stats-api/daily?AUTH_TOKEN=NVrzcU3h2hXuhZCJYZ6KUP29
data/grav.local/2018-09-11/daily.json
Saved data to data/grav.local/2018-09-11/daily.json
```