<?php

namespace model;

use components\DBwrapper;

/**
 * Class Generator
 */
class Generator implements ConsoleCommand
{
    private const IMAGE_EXT = 'png';

    /**
     * @var int
     */
    private $limit;

    /**
     * @var DBwrapper
     */
    private $db;

    /**
     * Generator constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        foreach ($config as $name => $value) {
            $this->$name = $value;
        }
    }

    public function run()
    {
        if (null === $this->limit || 0 === (int) $this->limit) {
            echo 'Limit must be greater than 0' . PHP_EOL;

            return;
        }

        $model = new News($this->db);
        $cat = $model->getAvailableCategory();
        do {
            $filePath = $this->getImageFilePath();
            $this->generateImage($filePath);
            $model->setAttributes([
                'category' => $cat[array_rand($cat)],
                'title' => generateRandomString(10),
                'message' => generateRandomString(10000),
                'image' => basename($filePath),
            ])->save();

            if (0 === $this->limit % 1000) {
                echo date('c') . ' :: It remains to generate ' . $this->limit . ' models.' . PHP_EOL;
            }
        } while (0 < --$this->limit);
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    private function getImageFilePath(): string
    {
        $dir = generateRandomUploadPath();

        if (false === is_dir($dir)
            && false === mkdir($dir, 0755, true)
            && false === is_dir($dir)
        ) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        $fileName = uuid4() . '.' . self::IMAGE_EXT;

        return $dir . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * @param string $filePath
     *
     * @throws \Exception
     */
    private function generateImage(string $filePath)
    {
        $handler = fopen($filePath, 'wb+');
        $image = \imagecreate(400, 400);
        imagecolorallocate($image, random_int(0, 255), random_int(0, 255), random_int(0, 255));
        imagepng($image, $handler);
    }
}
