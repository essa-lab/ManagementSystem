<?php

use App\Http\Resources\DigitalResource\DigitalResource;
use App\Models\Article\Article;
use App\Models\Book\Book;
use App\Models\Research\Research;

return [


    'relations'=>[
        'book'=>[
            'resourceable.bookTranslateType',
            'resourceable.bookTranslator',
            'resourceable.specificSubjects',
            'resourceable.bookPoetryCollection',
            'resourceable.poetryCollectionName',
            'resourceable.printInformation',
            'resourceable.printInformation.conditions',
            'resourceable.printInformation.type',
        ],
        'article'=>[
            'resourceable.articleType',
            'resourceable.articleSpecification',
            'resourceable.articleScientificClassification',
            'resourceable.articleKeyword'
        ],
        'research'=>[
            'resourceable.researchType',
            'resourceable.researchFormat',
            'resourceable.researchEducationLevel',
            'resourceable.researchKeywords'
        ],
        'digital_resource'=>[
            'resourceable.digitalType',
            'resourceable.digitalFormat',
            'resourceable.relations',
            'resourceable.right',
            'resourceable.specificSubject'
        ]
    ],
    'searchableFeilds'=>[
        'book'=>[
            Book::class,
            [
            'isbn',
            'barcode',
            'registeration_number',
            'order_number',
            'book_national_id_number',
            ]
        ],
        'article'=>[
            Article::class,
            [
            'registeration_number',
            'order_number',
            'article_scientific_classification_id',
            'article_type_id',
            'article_specification_id',
            ]
        ],
        'research'=>[
            Research::class,[
            'registeration_number',
            'order_number',
            'education_level_id',
            'research_type_id',
            'research_format_id',
            ]
        ],
        'digital_resource'=>[
            DigitalResource::class,[
            'digital_format_id',
            'digital_resource_type_id',
            'identifier'
            ]
        ]
    ]
];