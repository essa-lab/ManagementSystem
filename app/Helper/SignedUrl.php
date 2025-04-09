<?php

namespace App\Helper;

use Aws\S3\S3Client;

class SignedUrl
{
    public static function generateUrl($file_name, $type, $size, $seconds = 60)
    {
        $client = new S3Client([
            'version' => 'latest',
            'region'  => config('filesystems.disks.s3.region'),
            'credentials' => [
                'key'    => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        $bucket = config('filesystems.disks.s3.bucket');
        $command = $client->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key' => $file_name,
            'ContentType' => $type,
            'ContentLength' => $size,


            // 'Metadata' => [
            //     'userId' => auth()->id(),
            //     'userType' => Auth::getDefaultDriver()
            // ]
        ]);

        $result = $client->createPresignedRequest($command, now()->addSeconds($seconds));

        return (string) $result->getUri();
    }
}
