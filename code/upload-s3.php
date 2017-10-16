<?php
require 'aws.phar';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2',
    'http'    => [ 'verify'  => false ],
    'credentials' => [ 'key' => S3_KEY, 'secret' => S3_SECRET ]
]);

try {
  // Upload data.
  $result = $s3->putObject(array(
    'Bucket' => $bucket,
    'Key'    => $keyFull,
    'SourceFile' => $filepath,
    'ACL'    => 'public-read'
  ));

  // Print the URL to the object.
  $rpta_s3 = $result['ObjectURL'];
} catch (S3Exception $e) {
  $rpta_s3 = $e->getMessage();
}
?>