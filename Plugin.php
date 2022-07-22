<?php

namespace SaguIntegration;

use MapasCulturais\App;

class Plugin extends \MapasCulturais\Plugin
{
    public function _init()
    {
        $app = App::i();

        // Insere botão de exportação dos dados do Mapa da Saúde para o Sagu
        $app->hook('template(opportunity.single.opportunity-registrations--tables):end', function () use ($app) {
            $entity = $this->controller->requestedEntity;

            if ($entity->publishedRegistrations && $entity->canUser('@control')) {
                $app->view->enqueueScript('app', 'sagu_integration', 'js/sagu-integration.js');
                $app->view->enqueueStyle('app', 'sagu_integration', 'css/sagu-integration.css');

                $this->part('singles/sagu-mapadasaude-export');
            }
        });

        // Insere botão de importação do formulário do Sagu
        $app->hook('template(opportunity.edit.registration-config):begin', function () use ($app) {
            $app->view->enqueueScript('app', 'import_sagu_default_form', 'js/import-sagu-form.js');
            $app->view->enqueueStyle('app', 'import_sagu_default_form', 'css/import-sagu-form.css');
        });
    }

    public function register()
    {
        $app = App::i();

        $app->registerController('sagu-integration', Controllers\SaguIntegration::class);
        $app->registerController('student-enrollment', Controllers\EnsinoPesquisaExtensao\StudentEnrollment::class);
    }
}
