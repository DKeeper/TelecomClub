<?php

namespace model;

/**
 * Class News
 */
class News extends Model
{
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
}
