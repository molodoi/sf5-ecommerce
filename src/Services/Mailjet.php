<?php

namespace App\Services;


use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Mailjet
{
    private $container; 

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function send($to_email, $to_name, $subject, $title, $content)
    {
        $mj = new Client(
            $this->container->getParameter('app.mailjet_api_key'), 
            $this->container->getParameter('app.mailjet_api_key_secret'),
            true,
            ['version' => 'v3.1']
        );

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "mr_stat12@hotmail.com",
                        'Name' => "Sf5 Ecommerce"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 3183095,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'email_title' => $title,
                        'email_content' => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}
