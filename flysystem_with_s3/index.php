<?php

include_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

// load .env and .env.local files
(new Dotenv())
    ->usePutenv(true) // Aws client uses getenv
    ->load(__DIR__.'/.env', __DIR__.'/.env.local');


//Create a S3Client
$s3Client = new Aws\S3\S3Client([
    'version' => 'latest',
    'region' => getenv('AWS_S3_BUCKET_REGION')
]);

// The adapter and filesystem
$adapter = new League\Flysystem\AwsS3V3\AwsS3V3Adapter($s3Client, getenv('AWS_S3_BUCKET_NAME'));
$filesystem = new League\Flysystem\Filesystem($adapter);

$filePath = __DIR__."/test_file.txt";
$fileName = basename($filePath);
$location = "/$fileName";

if (!$filesystem->fileExists($location)) {
    $filesystem->write($location, file_get_contents($filePath));
    echo "Created $location file in your bucket! Check it out!";
} else {
    $filesystem->delete($location);
    echo "Deleted $location file from your bucket!";
}
