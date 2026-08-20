<?php
namespace Starbug\App\Page;

use GuzzleHttp\Psr7\Response;
use Starbug\Routing\Controller;

class HomeController extends Controller {
  public function __invoke() {
    return new Response(
      200,
      ['Content-Type' => 'text/html; charset=utf-8'],
      '<!DOCTYPE html><html><head><title>Home</title></head><body><nav class="primary-tabs" aria-label="Primary tabs"><ul class="nav nav-tabs"><li class="nav-item active"><a class="nav-link active" href="/">Home</a></li><li class="nav-item"><a class="nav-link" href="/admin/pages/1/edit">Edit</a></li></ul></nav></body></html>'
    );
  }
}
