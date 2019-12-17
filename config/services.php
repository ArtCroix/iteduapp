<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
     */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'firebase' => [
        'database_url' => env('FIREBASE_DATABASE_URL', ''),
        'project_id' => env('FIREBASE_PROJECT_ID', 'miptmobiledev'),
        'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID', '62b78c73e8a87619cd71f4c90ed37b868bf6783d'),
        // replacement needed to get a multiline private key from .env
        'private_key' => str_replace("\\n", "\n", env('FIREBASE_PRIVATE_KEY', "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDfbpQETrVn59Rm\ngQ4qmgrscZuPmd701QqKI5/FJNwH8xl/WYRYIgmFeiXmsmk7dx/8u0K4ojKXZXn4\n387lXXDVh8zuBLMeQiivPXJnRL50T808UEGIrp0Jng/d65cp1fX5iC+Bf+DlwtxD\nCfm1tMrO+qZXi98DzcVlMnUTHoB9B5eCIF8Njs0HBPowCOC0J0C+LKDxPGlvvVVQ\njgxKRiPeo2niTWUNFB6atej4/L5+hNTaUo5mTs0I4C0UTw4YTDpLgSC2AdT6/XH9\nWAtv5+1Qnt4nXbllTSYlyFEsaJhStIMZ8PFgR7dcPj3iH6MQ2PxSDLa/mx/nstkX\nTHftbqDPAgMBAAECggEAY00a/dsZZsezL6d161sxNaq8T5M8cyXoHEL+9DiQjfvu\nPhpD5oOiJa+G/sQWXvZvwfxcCAEWaRERp5mYs3nW9x/25WYzUWRn/pJZsSntV3ZM\nKd0lcyDf7HFSbfgR4+cS/kcXjf+Ora7wA9+AGtDyPhFKMqiYgnFGpNZLECdDRc1w\nMcVEjO2tz3Z4rzXQev8VhCZQhEjHosvSK2v/ZBZS1mJaDNiboK0wYzi4i1OAAu5n\nq0tv+KRNBdMWmm9QSnu3ttqTZYAynWn6DaUHqdcu5etm0GmokdAV8YykpB5EF2nf\ne4A44BIqcvF4NYQcoG2bKUxWRJklX5qX0T/nhDch8QKBgQD5lYooJo8lRcMfpsoO\nO2FH11d48bEbg8F6Xioui2EEKoteGS8DRcI9mv2SSRDaiP0Y8s+qa3nrYVhStch/\nsUta+IoutxUCQEoiUAhXRRLLtUYxSYWn4XI0vyD3vAVUxjhiczNxlRO2/DbeXx/m\nDv+KHmu9ql6kTqe1YEe1OUgGmQKBgQDlLO+8xHAa880E10DGq3AAahBqToL219pO\nvbTwPvjkJHF65UgtwOEI/pXX7jEaBFzPwvTdfiWlvPCfwkqXNfgOHvl972O4lcxx\npnJCggNPvWkIu1n4roVCnY3VPEusDc7Vd1NeGAK76Ddwd0xBQsN8WpqjzYx7PVsg\nGF57UU/LpwKBgFS1N03HQIAy/DWq4xsPdkXuxWZsCWNARZNlkEV50rSaR/Do9TbU\nH0fPac6Tc9/n7JbiGi9KCPglctenkOQd5Wh7wFJ4mu3HqiGZLiuIaweuf6NMw8sl\nziAu2l6adui92fc4CDa9lRJe6bRxbp8jagGTr5eQ0kxL0cnSYqQUS+jJAoGBALAM\nHP29SQDrWArn7/Ktr5Qh5gpD1YcZubHTOTN0rG1VMXdeulurVzM5npIJf96ki0du\nU/vx86mlwpJ3BiWyZ6MxWCbNxqT9LdgALLW6eiC/N/hlM3A9YFEUBuhPMTIySTji\nOUCG4VLHyA61Ffbr+o+EalZ6h/gD4gR9s9a3V7zxAoGAWyoeLaQAejfa0uAMGYHD\n/CPOzRhurgDuS/siJrTX5qWTJVnTKqFKPHTBzIaAL1nKhNML5nz32jVJ1I/BTQ69\nM0Sp5gl3B3ZUk2MP8oZutJg1G06bwccHkO6S3FJs/YhObEjfDCBL32xRUz0Tt4aR\n91cT9XcuS0uxKFSEX6ZWdlw=\n-----END PRIVATE KEY-----\n")),
        'client_email' => env('FIREBASE_CLIENT_EMAIL', 'firebase-adminsdk-5a9sz@miptmobiledev.iam.gserviceaccount.com'),
        'client_id' => env('FIREBASE_CLIENT_ID', '102072817350132872055'),
        'client_x509_cert_url' => env('FIREBASE_CLIENT_x509_CERT_URL', 'https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-5a9sz%40miptmobiledev.iam.gserviceaccount.com'),
    ],

];
