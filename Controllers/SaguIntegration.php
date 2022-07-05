<?php

namespace SaguIntegration\Controllers;

require PLUGINS_PATH . 'SaguIntegration/vendor/autoload.php';

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use MapasCulturais\App;

class SaguIntegration extends \MapasCulturais\Controller {
    private $infos = [];

    public function GET_selectedStudentData()
    {
        $app = App::i();
        $opportunity_id = intval($this->data["id"]);
        $registrations = $app->repo('Registration')->findBy(['opportunity' => $opportunity_id, 'status' => 10]);
        $agents = [];

        foreach ($registrations as $registration) {
            $agent_metas = $app->repo('AgentMeta')->findBy(['owner' => $registration->owner->id]);

            $this->mountDataSelectedStudents($agent_metas);

            $students["data"] = $this->infos;
            $students["registration_number"] = $registration->id;

            try {
                $client = new Client(['base_uri' => env('BASE_URI_SAGU')]);

                $options = [
                    'headers' => [
                        'x-api-key' => env('X_API_KEY_SAGU')
                    ],
                    'json' => $this->infos
                ];

                $response = $client->request('POST', 'person', $options);
                $students["status"] = $response->getStatusCode();
            } catch (ClientException $e) {
                $students["status"] = $e->getResponse()->getStatusCode();
            }

            array_push($agents, $students);
        }

        $this->json($agents);
    }

    private function mountDataSelectedStudents($agent_metas)
    {
        foreach ($agent_metas as $agent_meta) {
            switch($agent_meta->key) {
                case 'nomeCompleto':
                    $this->infos["nome"] = $agent_meta->value;
                    break;
                case 'emailPrivado':
                    $this->infos["email"] = $agent_meta->value;
                    break;
                case 'rg':
                    $this->infos["rg"] = $agent_meta->value;
                    break;
                case 'documento':
                    $this->infos["cpf"] = $agent_meta->value;
                    break;
                case 'dataDeNascimento':
                    $date = new DateTime($agent_meta->value);
                    $this->infos["dataNascimento"] = $date->format('Y-m-d');
                    break;
                case 'En_CEP':
                    $this->infos["endereco"]["cep"] = str_replace("-", "" ,$agent_meta->value);
                    break;
                case 'En_Nome_Logradouro':
                    $this->infos["endereco"]["logradouro"] = $agent_meta->value;
                    break;
                case 'En_Num':
                    $this->infos["endereco"]["numero"] = $agent_meta->value;
                    break;
                case 'En_Complemento':
                    $this->infos["endereco"]["complemento"] = $agent_meta->value;
                    break;
                case 'En_Bairro':
                    $this->infos["endereco"]["bairro"] = $agent_meta->value;
                    break;
                case 'En_Municipio':
                    $this->infos["endereco"]["cidade"] = $agent_meta->value;
                    break;
                case 'telefone1':
                    $this->infos["celular"] = $agent_meta->value;
                    break;
                case 'telefone2':
                    $this->infos["telefoneResidencial"] = $agent_meta->value;
                    break;
                case 'genero':
                    $this->infos["sexo"] = $agent_meta->value[0];
                    break;
                default:
                    $this->infos["estadoCivil"] = "N";
            }
        }
    }
}
