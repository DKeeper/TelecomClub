<?php

namespace repository;

use model\News;

/**
 * Class ModelRepository
 */
class NewsRepository extends Repository
{
    protected const MODEL_CLASS = News::class;
}
