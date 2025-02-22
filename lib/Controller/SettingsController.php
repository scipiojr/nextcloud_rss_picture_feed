<?php
namespace OCA\RSSPictureFeed\Controller;

use OCP\AppFramework\Controller;
use OCP\IRequest;
use OCP\IConfig;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\TemplateResponse;

class SettingsController extends Controller {
    private $config;
    private $userId;

    public function __construct($appName, IRequest $request, IConfig $config, $userId) {
        parent::__construct($appName, $request);
        $this->config = $config;
        $this->userId = $userId;
    }

    #[NoAdminRequired]
    public function getSettings() {
        $settings = json_decode($this->config->getUserValue($this->userId, 'rss_picture_feed', 'settings', '{}'), true);
        return new JSONResponse($settings);
    }

    #[NoAdminRequired]
    public function saveSettings($settings) {
        $this->config->setUserValue($this->userId, 'rss_picture_feed', 'settings', json_encode($settings));
        return new JSONResponse(['success' => true]);
    }

    #[NoAdminRequired]
    public function showSettingsPage() {
        return new TemplateResponse('rss_picture_feed', 'settings');
    }

    #[NoAdminRequired]
    public function getMetrics($uuid) {
        $metrics = json_decode($this->config->getAppValue('rss_picture_feed', "metrics_$uuid", '{}'), true);
        return new JSONResponse(['metrics' => $metrics]);
    }

    #[NoAdminRequired]
    public function resetMetrics($uuid) {
        // Placeholder function for resetting metrics
        return new JSONResponse(['success' => true]);
    }
}

?>