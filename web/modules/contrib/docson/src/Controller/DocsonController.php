<?php

namespace Drupal\docson\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DocsonController extends ControllerBase {

  public function inspectSchema(Request $request) {
    $base_path = $this->moduleHandler()->getModule('docson')->getPath();
    $build = [
      '#type' => 'html_tag',
      '#tag' => 'script',
      '#attributes' => [
        'src' => sprintf('/%s/js/widget.js', $base_path),
        'data-schema' => '/docson',
      ],
    ];

    if ($schema = $request->query->get('schema')) {
      $build['#attributes']['data-schema'] = $schema;
    }
    else {
      drupal_set_message($this->t('No schema was selected, please choose one.'), 'warning');
      return new RedirectResponse(Url::fromRoute('docson.schema_selector')->toString());
    }

    return $build;
  }

}
