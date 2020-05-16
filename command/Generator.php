<?php

namespace command;

use components\DBwrapper;
use model\News;

/**
 * Class Generator
 */
class Generator implements ConsoleCommand
{
    private const IMAGE_EXT = 'png';

    /**
     * Limit for chunk that writing in DB
     */
    private const BATCH_SIZE = 100000;

    /**
     * @var string
     */
    private $outputFilePath;

    /**
     * @var resource
     */
    private $fileHandler;

    /**
     * @var array
     */
    private $category;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var DBwrapper
     */
    private $db;

    /**
     * @var array
     */
    private $recordFields;

    /**
     * @var string
     */
    private $tableName;

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

        if (null === $this->db) {
            throw new \RuntimeException('Database isn\'t configured.');
        }

        if (null === $this->outputFilePath) {
            throw new \RuntimeException('Path to temporary file isn\'t configured.');
        }
    }

    /**
     * @throws \RuntimeException
     */
    public function run()
    {
        if (null === $this->limit || 0 === (int) $this->limit) {
            echo 'Limit must be greater than 0' . PHP_EOL;

            return;
        }

        $this->limit = (int) $this->limit;

        $model = new News($this->db);
        $this->category = $model->getAvailableCategory();
        $this->tableName = $model->getTableName();

        $this->initFileHandlers();

        echo date('c') . ' :: Start' . PHP_EOL;

        for ($i = 1; $i <= $this->limit; $i++) {
            $data = $this->generateData();

            if ($i === 1) {
                $this->recordFields = array_keys($data);
            }

            if (!fputcsv($this->fileHandler, $data)) {
                throw new \RuntimeException('Couldn\'t write data in to file ' . $this->outputFilePath);
            }

            if (0 === $i % 1000) {
                echo date('c') . ' :: It remains to generate ' . ($this->limit - $i) . ' models.' . PHP_EOL;
            }

            if (0 === $i % self::BATCH_SIZE) {
                $this->processBatch();
            }
        }

        $this->processBatch(false);

        echo date('c') . ' :: Finish' . PHP_EOL;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    private function generateData(): array
    {
        $filePath = $this->getImageFilePath();
        $this->generateImage($filePath);

        return [
            'category' => $this->category[array_rand($this->category)],
            'title' => generateRandomString(10),
            'message' => generateRandomString(10000),
            'image' => basename($filePath),
        ];
    }

    /**
     * @param bool $initFileHandlerAfterProcessing
     */
    private function processBatch($initFileHandlerAfterProcessing = true)
    {
        fclose($this->fileHandler);

        $fields = implode(',', $this->recordFields);

        echo date('c') . ' :: Load data into DB' . PHP_EOL;

        $this->db->query("
            LOAD DATA LOCAL INFILE '{$this->outputFilePath}'
            INTO TABLE {$this->tableName}
            FIELDS TERMINATED BY ','
            ENCLOSED BY '\"'
            LINES TERMINATED BY '\\n'
            ({$fields})
        ");

        echo date('c') . ' :: Done' . PHP_EOL;

        unlink($this->outputFilePath);

        if ($initFileHandlerAfterProcessing) {
            $this->initFileHandlers();
        }
    }

    /**
     * @throws \RuntimeException
     */
    private function initFileHandlers()
    {
        if (false === $h = fopen($this->outputFilePath, 'wb')) {
            throw new \RuntimeException('Couldn\'t open file ' . $this->outputFilePath);
        }

        $this->fileHandler = $h;
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    private function getImageFilePath(): string
    {
        $fileName = uuid4() . '.' . self::IMAGE_EXT;
        $dir = generateRandomUploadPath($fileName);

        if (false === is_dir($dir)
            && false === mkdir($dir, 0755, true)
            && false === is_dir($dir)
        ) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

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
