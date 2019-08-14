<?php


namespace KikCMS\Services;


use Phalcon\Di\Injectable;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class VendorCleanUpService extends Injectable
{
    /**
     * Clean up the vendor folder to keep only necessary files
     */
    public function clean()
    {
        $vendorFolder = $this->config->application->path . 'vendor/';

        // remove unused google files
        $unusedGoogleFiles = glob($vendorFolder . 'google/apiclient-services/src/Google/Service/*');

        $unusedGoogleFiles = array_filter($unusedGoogleFiles, function ($v) {
            return ! in_array(basename($v), ['AnalyticsReporting', 'AnalyticsReporting.php']);
        });

        foreach ($unusedGoogleFiles as $unusedGoogleFile){
            $this->removeRecursively($unusedGoogleFile);
        }

        // remove tests folders
        $this->removeRecursively($vendorFolder . 'twig/twig/test');
        $this->removeRecursively($vendorFolder . 'google/apiclient-services/tests');
        $this->removeRecursively($vendorFolder . 'google/auth/tests');
        $this->removeRecursively($vendorFolder . 'martijnc/php-csp/tests');
        $this->removeRecursively($vendorFolder . 'monolog/monolog/tests');
        $this->removeRecursively($vendorFolder . 'ralouphie/getallheaders/tests');
        $this->removeRecursively($vendorFolder . 'swiftmailer/swiftmailer/tests');
    }

    /**
     * @param string $folder
     */
    private function removeRecursively(string $folder)
    {
        if ( ! file_exists($folder)) {
            return;
        }

        if ( ! is_dir($folder)) {
            unlink($folder);
            return;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

        rmdir($folder);
    }
}