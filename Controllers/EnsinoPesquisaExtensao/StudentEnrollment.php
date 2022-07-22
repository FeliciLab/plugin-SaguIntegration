<?php

namespace SaguIntegration\Controllers\EnsinoPesquisaExtensao;

require PLUGINS_PATH . 'SaguIntegration/vendor/autoload.php';

use GuzzleHttp\Client;

class StudentEnrollment extends \MapasCulturais\Controller
{
    private $http_client;
    private $http_client_opts;

    function __construct()
    {
        $this->http_client = new Client(['base_uri' => env('BASE_URI_SAGU')]);
        $this->http_client_opts = [
            'headers' => [
                'x-api-key' => env('X_API_KEY_SAGU')
            ]
        ];
    }

    public function GET_coursesOffered()
    {
        $response = $this->http_client->request('GET', 'ensino-pesquisa-extensao/ofertas', $this->http_client_opts);

        $this->json(json_decode($response->getBody()->getContents()));
    }

    public function GET_activeClassesByCourses()
    {
        $courseId = intval($this->data["id"]);
        $response = $this->http_client->request('GET', "ensino-pesquisa-extensao/ofertas/{$courseId}/turmas", $this->http_client_opts);

        $this->json(json_decode($response->getBody()->getContents()));
    }
}
