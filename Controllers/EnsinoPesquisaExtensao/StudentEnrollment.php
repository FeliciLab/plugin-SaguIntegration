<?php

namespace SaguIntegration\Controllers\EnsinoPesquisaExtensao;

require PLUGINS_PATH . 'SaguIntegration/vendor/autoload.php';

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
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
        $app = App::i();
        $opportunity_id = intval($this->data["opportunityId"]);
        $class_id = intval($this->data["classId"]);
        $opportunity = $app->repo("Opportunity")->find($opportunity_id);

        if ($app->user->is('guest')) $app->auth->requireAuthentication();
        $opportunity->checkPermission('@control');

        $this->registerIndividual($opportunity_id);

        foreach ($this->students as $index => $student) {
            $aluno["cpf"] = $student["data"]["cpf"];
            $this->http_client_opts["json"] = $aluno;

            try {
                $response = $this->http_client->request('POST', "ensino-pesquisa-extensao/turma/{$class_id}/inscricao", $this->http_client_opts);
                $this->students[$index]["registration_status"] = $response->getStatusCode();
            } catch (ClientException | ServerException $e) {
                $this->students[$index]["registration_status"] = $e->getResponse()->getStatusCode();
            }
        }

        $this->json($this->students);
    }
}
