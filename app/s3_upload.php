<?php
require __DIR__ . '/../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Configuration
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();


$folderPath = __DIR__ . '/../storage';
$bucketName = $_ENV['S3_BUCKET'];
$transferredFilesRecord = __DIR__ . '/../storage/transferred_files.txt';

// Initialize S3 client
$s3Client = new S3Client([
    'region'  => $_ENV['S3_REGION'],
    'version' => 'latest',
    'endpoint' => "https://{$_ENV['S3_REGION']}.digitaloceanspaces.com",
    'credentials' => [
        'key'    => $_ENV['S3_ACCESS_KEY_ID'],
        'secret' => $_ENV['S3_SECRET_ACCESS_KEY'],
    ],
]);

// Get today's and yesterday's date
$yesterday = (new DateTime())->modify('-1 day');

// Function to upload file to S3
function uploadToS3($filePath, $bucketName, $s3Client)
{
    try
    {
        $folder = substr(basename($filePath), 0, 7);

        $s3Client->putObject([
            'Bucket' => $bucketName,
            'Key'    => $folder . "/" . basename($filePath),
            'SourceFile' => $filePath,
            'ACL'    => 'public-read',
        ]);
        echo "Uploaded {$filePath} to {$bucketName}\n";
    }
    catch (AwsException $e) {
        echo "Failed to upload {$filePath}: " . $e->getMessage() . "\n";
    }
}

// Load transferred files record
if (file_exists($transferredFilesRecord))
{
    $transferredFiles = file($transferredFilesRecord, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $transferredFiles = array_flip($transferredFiles); // Convert to associative array for fast look-up
}
else
{
    $transferredFiles = [];
}


// List and process files in the folder
$files = scandir($folderPath);

foreach ($files as $fileName)
{
    $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;

    if( !is_file($filePath) || !str_contains($fileName, '.log') )
        continue;

    try
    {
        $fileDate = DateTime::createFromFormat('Y-m-d', substr($fileName, 0, strpos($fileName, '.log')));

        if ($fileDate && $fileDate < $yesterday) {
            if (!isset($transferredFiles[$fileName]))
            {
                uploadToS3($filePath, $bucketName, $s3Client);
                $transferredFiles[$fileName] = true;
            } else {
                echo "{$fileName} has already been transferred.\n";
            }
        }
    } catch (Exception $e) {
        echo "Skipping non-date file: {$fileName}\n";
    }
}

// Update transferred files record
file_put_contents(
    $transferredFilesRecord,
    implode(PHP_EOL, array_keys($transferredFiles))
);