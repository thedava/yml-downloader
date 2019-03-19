<?php

use DavaHome\Console\Command\AbstractCommand;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use function GuzzleHttp\Promise\settle;

class DownloadCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('download')
            ->setDescription('Download files defined in a yml file')
            ->addArgument('file', InputArgument::REQUIRED, 'The yml file')
            ->addArgument('path', InputArgument::OPTIONAL, 'The local path', '.')
            ->addOption('force-override', 'f', InputOption::VALUE_NONE, 'Force override of existing files')
            ->addOption('username', 'u', InputOption::VALUE_REQUIRED)
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED)
            ->addOption('phar-compatibility', 'c', InputOption::VALUE_NONE, 'Enable phar compatibility mode');
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected function getFileNameByUrl($url)
    {
        list($fileName) = explode('?', basename($url));

        return $fileName;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $compatibilityMode = $input->getOption('phar-compatibility');
        $file = $input->getArgument('file');

        // Check for file existence
        if (!$compatibilityMode && !file_exists($file)) {
            $output->writeln('<error>File not found!</error>');

            return 1;
        }

        // Read YML file
        $content = Yaml::parse(rtrim(file_get_contents($file)));
        if (!is_array($content)) {
            $output->writeln('<error>YAML has invalid structure!</error> Expecting top-level key with urls as children');

            return 1;
        }

        // HTTP Basic Auth
        $options = [];
        $user = $input->getOption('username');
        $password = $input->getOption('password');
        if (!empty($user) && !empty($password)) {
            $options['auth'] = [$user, $password];
        }

        $stats = [];
        $path = $input->getArgument('path');
        $force = $input->getOption('force-override');
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        foreach ($content as $key => $urls) {
            $key = utf8_decode($key);

            $output->write('<question>Downloading ' . $key . '...</question> ');
            if (!is_dir($path . '/' . $key)) {
                @mkdir($path . '/' . $key, 0777, true);
            }

            $promise = [];
            foreach ($urls as $url) {
                $fileName = $this->getFileNameByUrl($url);
                if (file_exists($path . '/' . $key . '/' . $fileName)) {
                    if (!$force) {
                        $output->writeln('<comment>Skipping file ' . $fileName . '</comment>', OutputInterface::VERBOSITY_VERBOSE);
                        continue;
                    }
                }
                $promise[$url] = $client->getAsync($url, $options);
            }
            $output->write(count($promise) . ' files ');

            $stats[$key] = ['count' => 0, 'size' => 0];
            foreach (settle($promise)->wait() as $url => $fulfilledPromise) {
                /** @var Response $response */
                $response = (isset($fulfilledPromise['value'])) ? $fulfilledPromise['value'] : null;
                if ($response === null) {
                    if (isset($fulfilledPromise['reason']) && $fulfilledPromise['reason'] instanceof \Throwable) {
                        throw $fulfilledPromise['reason'];
                    }
                    throw new \Exception('Request failed due to unknown error');
                }

                $stats[$key]['size'] += $response->getBody()->getSize();
                $fileName = $this->getFileNameByUrl($url);
                if (file_exists($path . '/' . $key . '/' . $fileName)) {
                    if (!$force) {
                        $output->writeln('<comment>Skipping file ' . $fileName . '</comment>', OutputInterface::VERBOSITY_VERBOSE);
                        continue;
                    }
                }
                file_put_contents($path . '/' . $key . '/' . $fileName, $response->getBody()->getContents());
                $stats[$key]['count']++;
            }
            unset($promise, $fulfilledPromise);
            $output->writeln('(total size: ' . ByteText::fromBytes($stats[$key]['size'])->toString() . ')');
        }

        return 0;
    }
}
