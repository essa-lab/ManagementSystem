<?php

namespace Database\Seeders;


use App\Models\Article\ArticleType;
use App\Models\Article\ScientificBranches;
use App\Models\Article\Specification;
use App\Models\Book\Book;
use App\Models\Book\PoetryCollection;
use App\Models\Book\PrintCondition;
use App\Models\Book\PrintType;
use App\Models\Book\TranslationType;
use App\Models\DigitalResource\DigitalFormat;
use App\Models\DigitalResource\DigitalResourceType;
use App\Models\Library;
use App\Models\Patron;
use App\Models\Privilage;
use App\Models\Research\EducationLevel;
use App\Models\Research\ResearchFormat;
use App\Models\Research\ResearchType;
use App\Models\Resource\Language;
use App\Models\Resource\Resource;
use App\Models\Resource\Source;
use App\Models\Resource\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class DataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
       
        Language::factory(5)->create();
        Subject::factory(5)->create();
        Source::factory(5)->create();
        PrintType::factory(5)->create();
        PrintCondition::factory(5)->create();
        EducationLevel::factory(5)->create();
        ResearchFormat::factory(5)->create();
        ResearchType::factory(5)->create();
        DigitalFormat::factory(5)->create();
        DigitalResourceType::factory(5)->create();
        PoetryCollection::factory(5)->create();
        ArticleType::factory(5)->create();
        ScientificBranches::factory(5)->create();
        Specification::factory(5)->create();
        Resource::factory(50)->create();
        // ResearchType::factory(30)->create();
        // Book::factory(100)->create();
        // Subject::factory(30)->create();
        // Source::factory(30)->create();
        // Patron::factory(40)->create();
        // DigitalResourceType::factory(40)->create();
        // DigitalFormat::factory(40)->create();
        // ArticleType::factory(40)->create();
        // Specification::factory(40)->create();
        // ScientificBranches::factory(40)->create();
        // PoetryCollection::factory(40)->create();
        // TranslationType::factory(40)->create();
        // PrintType::factory(40)->create();

      
        // EducationLevel::factory()->create([
        //     'title_ar'=>'PHD',
        //     'title_en'=>'PHD',
        //     'title_ku'=>'PHD'
        // ]);
        // EducationLevel::factory()->create([
        //     'title_ar'=>'bachelor',
        //     'title_en'=>'bachelor',
        //     'title_ku'=>'bachelor'
        // ]);
        // EducationLevel::factory()->create([
        //     'title_ar'=>'master',
        //     'title_en'=>'master',
        //     'title_ku'=>'master'
        // ]);

    }
}
