<?php

namespace model;

/**
 * Class News
 */
class News extends Model
{
    protected const CATEGORY_1 = 0;
    protected const CATEGORY_2 = 1;
    protected const CATEGORY_3 = 2;
    protected const CATEGORY_4 = 3;

    protected $tableName = 'news';

    protected $fields = ['id', 'category', 'title', 'message', 'image', 'created_at'];

    protected $rules = [
        'title' => [
            ['type' => 'required', 'message' => 'User name required'],
            [
                'type' => 'regExp',
                'pattern' => '/^[a-zA-Z0-9_\-\s]+$/',
                'message' => 'Allowed characters: a-z, A-Z, digits and "_"',
            ],
        ],
        'message' => [
            ['type' => 'required', 'message' => 'Message required'],
            [
                'type' => 'regExp',
                'pattern' => '/^[a-zA-Z0-9_\-\s]+$/',
                'message' => 'Allowed characters: a-z, A-Z, digits, _, - and "space"',
            ],
        ],
        'image' => [
            [
                'type' => 'file',
                'allowedType' => ['jpeg', 'gif', 'png'],
                'message' => 'Allowed extension: jpg, gif, png',
            ],
        ],
    ];

    /**
     * @param array $conditions
     * @param array $sort
     *
     * @return array
     */
    public function getNews(array $conditions = [], array $sort = []): array
    {
        return $this->findAll($conditions);
    }

    /**
     * @return int[]
     */
    public function getAvailableCategory(): array
    {
        return [
            self::CATEGORY_1,
            self::CATEGORY_2,
            self::CATEGORY_3,
            self::CATEGORY_4,
        ];
    }
}
