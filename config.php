<?php

use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\Core\ExponentialBackoff;

use Google\Cloud\Datastore\DatastoreClient;
use Google\Cloud\Storage\StorageClient;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Google\Cloud\Storage\StorageClient;


$projectId = 'xxxxxxxxx';
$bucketId = "xxxxxxxxxx";
//$projectId = getenv('GCLOUD_PROJECT');

/** Uncomment and populate these variables in your code */
// $projectId = 'The Google project ID';

$bigQuery = new BigQueryClient([
    'projectId' => $projectId,
]);

$backoff = new ExponentialBackoff(10);

$datastore = new DatastoreClient([
    'projectId' => $projectId
]);


$storage = new StorageClient();

$bucket = $storage->bucket($bucketId);
//echo "Bucket name:" . $bucket->name();
