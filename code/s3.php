<?php
require 'aws.phar';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$folder   = 'profile';
$bucket   = 'staticprocter';
$keyname  = 'pampers.jpg';
$keyFull  = $folder. '/'. $keyname;
$filepath = 'pampers.jpg';

$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2',
    'http'    => [ 'verify'  => false ],
    'credentials' => [
      'key'    => 'AKIAJE5NLCPMLXGHWPUA',
      'secret' => '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'
    ]
]);

try {
  //$lista = $s3->listBuckets();
  //print_r($lista);

  // Upload data.
  $result = $s3->putObject(array(
    'Bucket' => $bucket,
    'Key'    => $keyFull,
    'SourceFile' => $filepath,
    'ACL'    => 'public-read'
  ));

  // Print the URL to the object.
  echo $result['ObjectURL'] . "\n";

} catch (S3Exception $e) {
  echo $e->getMessage() . "\n";
}

?>