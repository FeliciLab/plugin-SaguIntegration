<?php

namespace SaguIntegration\Controllers;

require PLUGINS_PATH . 'SaguIntegration/vendor/autoload.php';

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use MapasCulturais\App;

class SaguIntegration extends \MapasCulturais\Controller {
    protected $infos = [];

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
            $students["data"]["registration_number"] = $registration->id;

            try {
                $client = new Client(['base_uri' => 'http://10.17.40.112:8085/']);

                $options = [
                    'headers' => [
                        'x-api-key' => '00C3B942F5A3C303021898B25DF44A1941C079F499FE45AB2BA04E1928083967BF387B588DED1D6B3C6CB2D0BA55E139FB4C3D35E448D58BB401D7528FC19315'
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
                    $this->infos["sexo"] = $agent_meta->value;
                    break;
            }
        }
    }
}
