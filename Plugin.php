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

                $this->part('singles/sagu-mapadasaude-export--button', ['opportunity_id' => $entity->id]);
            }
        });
    }

    public function register()
    {
        $app = App::i();

        $app->registerController('sagu-integration', Controllers\SaguIntegration::class);
    }
}
