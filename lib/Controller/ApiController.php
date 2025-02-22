<?php
namespace OCA\RSSPictureFeed\Controller;

use OCP\AppFramework\Controller;
use OCP\IRequest;
use OCP\IConfig;
use OCP\Files\Node\Folder;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\FileDisplayResponse;
use OCP\Files\IFilesystem;
use OCA\RSSPictureFeed\AppInfo\Application;
use OCP\ILogger;
use Ramsey\Uuid\Uuid;
use OCP\Preview;

class ApiController extends Controller {
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
    public function regenerateUuid() {
        $newUuid = Uuid::uuid4()->toString();
        return new JSONResponse(['new_uuid' => $newUuid], 200);
    }

    #[NoAdminRequired]
    #[NoCSRFRequired]
    #[PublicPage]
    public function getFeed($uuid) {
        $config = json_decode($this->config->getUserValue($this->userId, 'rss_picture_feed', $uuid, '{}'), true);
        $clientIp = $this->getClientIp();

        if (!$this->isIpAllowed($clientIp, $config['allowed_ips'])) {
            return new JSONResponse(['error' => 'Forbidden'], 403);
        }
        
        if (!empty($config['password']) && !$this->checkAuth($config['password'])) {
            return new JSONResponse(['error' => 'Unauthorized'], 401);
        }

        $rssTitle = $config['rss_title'] ?? 'RSS Picture Feed';

        $this->logger->info("RSS feed accessed for UUID: $uuid by IP: $clientIp", ['app' => 'rss_picture_feed']);

        $imageUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/apps/rss_picture_feed/random-image/' . $uuid;

        $rss = '<?xml version="1.0" encoding="UTF-8"?>
        <rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
            <channel>
                <title>'.$rssTitle.'</title>
                <ttl>'.$config['ttl'].'</ttl>
                <item>
                    <media:content url="'.$imageUrl.'" type="image/jpeg" />
                </item>
            </channel>
        </rss>';

        return new JSONResponse($rss, 200, ['Content-Type' => 'application/rss+xml']);
    }

    #[NoAdminRequired]
    #[NoCSRFRequired]
    #[PublicPage]
    public function getRandomImage($uuid) {
        $config = json_decode($this->config->getUserValue($this->userId, 'rss_picture_feed', $uuid, '{}'), true);
        $clientIp = $this->getClientIp();

        if (!$this->isIpAllowed($clientIp, $config['allowed_ips'])) {
            return new JSONResponse(['error' => 'Forbidden'], 403);
        }
        
        if (!empty($config['password']) && !$this->checkAuth($config['password'])) {
            return new JSONResponse(['error' => 'Unauthorized'], 401);
        }

        $imagePath = $this->getWeightedImage($config['folder'], $config['allowed_extensions'], $uuid);

        if (!$imagePath) {
            return new JSONResponse(['error' => 'No images found'], 404);
        }

        // Log the image request
        $this->logImageRequest($uuid, $imagePath);

        // Resize image with caching based on Nextcloud preview system
        $preview = new Preview($this->rootFolder);
        $resizedImage = $preview->getPreview($imagePath, $config['max_width'] ?? 1024, $config['max_height'] ?? 768, true, true);

        return new FileDisplayResponse($resizedImage);
    }
}
