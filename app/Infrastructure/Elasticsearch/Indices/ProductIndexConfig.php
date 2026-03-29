<?php

namespace App\Infrastructure\Elasticsearch\Indices;

class ProductIndexConfig
{
    public function getName(): string
    {
        return config('scout.prefix') . 'products';
    }

    public function getConfig(): array
    {
        return [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'autocomplete' => [
                            'tokenizer' => 'autocomplete_tokenizer',
                            'filter' => ['lowercase'],
                        ],
                    ],
                    'tokenizer' => [
                        'autocomplete_tokenizer' => [
                            'type' => 'edge_ngram',
                            'min_gram' => 3,
                            'max_gram' => 10,
                            'token_chars' => ['letter', 'digit'],
                        ],
                    ],
                ],
            ],
            'mappings' => [
                'properties' => [
                    'category_id' => [
                        'type' => 'integer',
                    ],
                    'price' => [
                        'type' => 'float',
                    ],
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'autocomplete',
                        'search_analyzer' => 'standard',
                        'fields' => [
                            'keyword' => ['type' => 'keyword', 'ignore_above' => 256]
                        ]
                    ],
                    'description' => [
                        'type' => 'text',
                    ],
                    'category' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => ['type' => 'keyword']
                        ]
                    ],
                ],
            ],
        ];
    }
}
