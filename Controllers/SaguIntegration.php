<?php

namespace SaguIntegration\Controllers;

require PLUGINS_PATH . 'SaguIntegration/vendor/autoload.php';

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use MapasCulturais\App;
use MapasCulturais\Entities\Registration;

class SaguIntegration extends \MapasCulturais\Controller
{
    private $infos = [];
    private $registration_id;

    public function GET_selectedStudentData()
    {
        $app = App::i();
        $opportunity_id = intval($this->data["id"]);
        $registrations = $app->repo('Registration')->findBy(['opportunity' => $opportunity_id, 'status' => Registration::STATUS_APPROVED]);
        $agents = [];

        foreach ($registrations as $registration) {
            $this->registration_id = $registration->id;
            $agent_metas = $app->repo('AgentMeta')->findBy(['owner' => $registration->owner->id]);

            $this->mountDataSelectedStudents($agent_metas);

            $students["data"] = $this->infos;
            $students["registration_number"] = $registration->number;

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
            switch ($agent_meta->key) {
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
                    $this->infos["endereco"]["cep"] = str_replace("-", "", $agent_meta->value);
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
                    $this->setProfessionalCategory();
                    $this->infos["estadoCivil"] = "N";
            }
        }
    }

    private function setProfessionalCategory()
    {
        $app = App::i();
        $opportunity_id = intval($this->data["id"]);
        $rfc = $app->repo('RegistrationFieldConfiguration')->findOneBy(['owner' => $opportunity_id, 'title' => 'Formação Profissional']);
        $rm = $app->repo('RegistrationMeta')->findOneBy(['owner' => $this->registration_id, 'key' => "field_$rfc->id"]);

        $this->infos["categoriaProfissional"] = $rm->value;
    }

    public function POST_importForm()
    {
        $app = App::i();
        $opportunity_id = intval($this->data["opportunity_id"]);
        $opportunity = $app->repo("Opportunity")->find($opportunity_id);
        $json = file_get_contents(PLUGINS_PATH . 'SaguIntegration/assets/js/sagu-form-fields.json');
        $form_fields = json_decode($json);

        $opportunity->importFields($form_fields);
    }
}
