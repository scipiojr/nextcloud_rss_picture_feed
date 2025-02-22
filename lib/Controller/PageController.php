<?php
namespace OCA\RSSPictureFeed\Controller;

use OCP\AppFramework\Controller;
use OCP\IRequest;
use OCP\IConfig;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Attributes\NoAdminRequired;
use OCP\AppFramework\Attributes\NoCSRFRequired;

class PageController extends Controller {
    private $config;

    public function __construct($appName, IRequest $request, IConfig $config) {
        parent::__construct($appName, $request);
        $this->config = $config;
    }

    #[NoAdminRequired]
    #[NoCSRFRequired]
    public function settings() {
        return new TemplateResponse('rss_picture_feed', 'settings');
    }
}
