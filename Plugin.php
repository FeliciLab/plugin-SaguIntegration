<?php

namespace SaguIntegration;

use MapasCulturais\App;

class Plugin extends \MapasCulturais\Plugin {
    public function _init()
    {
        $app = App::i();

        // Insere botão de exportação dos dados do Mapa da Saúde para o Sagu
        $app->hook('template(opportunity.single.opportunity-registrations--tables):end', function() use($app) {
            $opportunity_id = $this->controller->requestedEntity->id;

            $app->view->enqueueScript('app', 'sagu_integration', 'js/sagu-integration.js');
            $this->part('singles/sagu-mapadasaude-export--button', ['opportunity_id' => $opportunity_id]);
        });
    }

    public function register()
    {
        $app = App::i();

        $app->registerController('sagu-integration', Controllers\SaguIntegration::class);
    }
}
