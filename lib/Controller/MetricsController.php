<?php
namespace OCA\RSSPictureFeed\Controller;

use OCP\AppFramework\Controller;
use OCP\IRequest;
use OCP\IConfig;
use OCP\Files\Node\Folder;
use OCP\AppFramework\Http\JSONResponse;
use OCP\Files\IFilesystem;
use OCP\ILogger;

class MetricsController extends Controller {
    private $config;
    private $rootFolder;
    private $userId;
    private $logger;

    public function __construct($appName, IRequest $request, IConfig $config, IFilesystem $rootFolder, $userId, ILogger $logger) {
        parent::__construct($appName, $request);
        $this->config = $config;
        $this->rootFolder = $rootFolder;
        $this->userId = $userId;
        $this->logger = $logger;
    }

    #[NoAdminRequired]
    #[NoCSRFRequired]
    #[PublicPage]
    public function getMetrics() {
        $metrics = json_decode($this->config->getAppValue('rss_picture_feed', 'metrics', '{}'), true);
        return new JSONResponse($metrics);
    }

    #[NoAdminRequired]
    #[NoCSRFRequired]
    #[PublicPage]
    public function logImageRequest($uuid, $imagePath) {
        $metrics = json_decode($this->config->getAppValue('rss_picture_feed', 'metrics', '{}'), true);
        
        if (!isset($metrics[$uuid])) {
            $metrics[$uuid] = [];
        }
        
        if (!isset($metrics[$uuid][$imagePath])) {
            $metrics[$uuid][$imagePath] = ['requests' => 0];
        }
        
        $metrics[$uuid][$imagePath]['requests'] += 1;
        
        $this->config->setAppValue('rss_picture_feed', 'metrics', json_encode($metrics));
        return new JSONResponse(['success' => true]);
    }
}
