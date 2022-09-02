<?php

namespace SaguIntegration\Controllers\EnsinoPesquisaExtensao;

require PLUGINS_PATH . 'SaguIntegration/vendor/autoload.php';

use GuzzleHttp\Exception\ClientException;
use MapasCulturais\App;
use SaguIntegration\Controllers\SaguIntegration;

class StudentEnrollment extends SaguIntegration
{
    public function GET_coursesOffered()
    {
        $app = App::i();
        $opportunity_id = intval($this->data["id"]);
        $opportunity = $app->repo("Opportunity")->find($opportunity_id);

        if ($app->user->is('guest')) $app->auth->requireAuthentication();
        $opportunity->checkPermission('@control');

        $response = $this->http_client->request('GET', 'ensino-pesquisa-extensao/ofertas', $this->http_client_opts);

        $this->json(json_decode($response->getBody()->getContents()));
    }

    public function POST_activeClassesByCourses()
    {
        $app = App::i();
        $opportunity_id = intval($this->data["opportunityId"]);
        $opportunity = $app->repo("Opportunity")->find($opportunity_id);

        if ($app->user->is('guest')) $app->auth->requireAuthentication();
        $opportunity->checkPermission('@control');

        $courseId = $this->data["courseId"];
        $response = $this->http_client->request('GET', "ensino-pesquisa-extensao/ofertas/{$courseId}/turmas", $this->http_client_opts);

        $this->json(json_decode($response->getBody()->getContents()));
    }

    public function POST_enrolledStudents()
    {
        $opportunity_id = intval($this->data["opportunityId"]);
        $classId = intval($this->data["classId"]);

        $this->registerIndividual($opportunity_id);

        foreach ($this->students as $student) {
            $aluno["cpf"] = $student["data"]["cpf"];
            $this->http_client_opts["json"] = $aluno;

            try {
                $response = $this->http_client->request('POST', "ensino-pesquisa-extensao/turma/{$classId}/inscricao", $this->http_client_opts);
                dump($response);
                die;
            } catch (ClientException $e) {
                dump($e->getResponse());
                die;
            }
        }
    }
}
