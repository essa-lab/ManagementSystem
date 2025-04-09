<?php

namespace Database\Seeders;


use App\Models\Article\ArticleType;
use App\Models\Article\ScientificBranches;
use App\Models\Article\Specification;
use App\Models\Book\Book;
use App\Models\Book\PoetryCollection;
use App\Models\Book\PrintType;
use App\Models\Book\TranslationType;
use App\Models\DigitalResource\DigitalFormat;
use App\Models\DigitalResource\DigitalResourceType;
use App\Models\Library;
use App\Models\Patron;
use App\Models\Privilage;
use App\Models\Research\EducationLevel;
use App\Models\Research\ResearchType;
use App\Models\Resource\Language;
use App\Models\Resource\Source;
use App\Models\Resource\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        Library::factory()->create([
            'name_ar'=>'Central',
            'name_en'=>'Central',
            'name_ku'=>'Central',
            'location'=>'Central',
            'logo'=>'logo'
        ]);
        Library::factory(5)->create();


        $superAdmin = User::factory()->create([
            'name' => 'Test',
            'email' => 'super.admin@gmail.com',
            'password'=>'123123123',
            'role'=>'super_admin',
            'library_id'=>1
        ]);
        
        $admin = User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@gmail.com',
            'password'=>'123123123',
            'role'=>'admin',
            'library_id'=>1
        ]);
        $staff =  User::factory()->create([
            'name' => 'Test User',
            'email' => 'staff@gmail.com',
            'password'=>'123123123',
            'role'=>'staff',
            'library_id'=>1
        ]);


        

        User::factory(10)->create();
        // Language::factory(5)->create();
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

        $patron = Privilage::factory()->create([
            'privilage_name'=>'PATRONS'
        ]);
        $userP = Privilage::factory()->create([
            'privilage_name'=>'USER'
        ]);
        $library = Privilage::factory()->create([
            'privilage_name'=>'LIBRARY'
        ]);
        $library = Privilage::factory()->create([
            'privilage_name'=>'PRIVILEGE'
        ]);

        $superAdmin->privilages()->attach($patron->id);
        $admin->privilages()->attach($patron->id);
        $staff->privilages()->attach($patron->id);

        $superAdmin->privilages()->attach($userP->id);
        $admin->privilages()->attach($userP->id);

        $superAdmin->privilages()->attach($library->id);
        $admin->privilages()->attach($library->id);
        $staff->privilages()->attach($library->id);

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
