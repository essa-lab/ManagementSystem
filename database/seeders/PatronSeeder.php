<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BookSource;
use App\Models\College;
use App\Models\Degree;
use App\Models\Departement;
use App\Models\Feature;
use App\Models\Genre;
use App\Models\Home\About;
use App\Models\Home\AboutContent;
use App\Models\Home\Home;
use App\Models\Keyword;
use App\Models\Language;
use App\Models\Library;
use App\Models\Patron;
use App\Models\Privilage;
use App\Models\Researcher;
use App\Models\ResearchType;
use App\Models\Resource\CirculationLog;
use App\Models\Supervisor;
use App\Models\Translator;
use App\Models\University;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\AuthorFactory;
use Illuminate\Database\Seeder;

class PatronSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Patron::factory(40)->create();
        Home::create([
            'title_en' => 'Home',
            'title_ku' => 'سەرەکی',
            'title_ar' => 'الرئيسية',
            'subtitle_en' => 'Welcome to the University Library',
            'subtitle_ku' => 'بەخێربێیت بۆ کتێبخانەی زانکۆ',
            'subtitle_ar' => 'مرحبا بكم في مكتبة الجامعة',
            'asset' => 'home.jpg',
        ]);

        About::create([
            'title_en' => 'About',
            'title_ku' => 'دەربارە',
            'title_ar' => 'حول',
            'content_en' => 'The University Library is a place where you can find all the resources you need to complete your research and assignments. We have a wide range of books, journals, and other materials that can help you succeed in your academic career.',
            'content_ku' => 'کتێبخانەی زانکۆ یەکێکە لەو شوێنەیانەی کە دەتوانیت هەموو ئامێرەکانی پێویستت بدۆزیت بۆ کۆتایشی لەکاتی تحقیق و بەرنامەکانت. ئێمە هەندێک کتێب، جۆرنال، و ماتێریالێکانی تر هەیە کە دەتوانن بە یارمەتیتان بەربەری کاری زانستیتان بە سەرکەوتوویی بکەن.',
            'content_ar' => 'تعتبر مكتبة الجامعة مكانًا يمكنك فيه العثور على جميع الموارد التي تحتاجها لإكمال أبحاثك وواجباتك. لدينا مجموعة واسعة من الكتب والمجلات والمواد الأخرى التي يمكن أن تساعدك على النجاح في حياتك الأكاديمية.',
            'image' => 'about.jpg',
        ]);

        AboutContent::create([
            'title_en' => 'About',
            'title_ku' => 'دەربارە',
            'title_ar' => 'حول',
            'description_en' => 'The University Library is a place where you can find all the resources you need to complete your research and assignments. We have a wide range of books, journals, and other materials that can help you succeed in your academic career.',
            'description_ku' => 'کتێبخانەی زانکۆ یەکێکە لەو شوێنەیانەی کە دەتوانیت هەموو ئامێرەکانی پێویستت بدۆزیت بۆ کۆتایشی لەکاتی تحقیق و بەرنامەکانت. ئێمە هەندێک کتێب، جۆرنال، و ماتێریالێکانی تر هەیە کە دەتوانن بە یارمەتیتان بەربەری کاری زانستیتان بە سەرکەوتوویی بکەن.',
            'description_ar' => 'تعتبر مكتبة الجامعة مكانًا يمكنك فيه العثور على جميع الموارد التي تحتاجها لإكمال أبحاثك وواجباتك. لدينا مجموعة واسعة من الكتب والمجلات والمواد الأخرى التي يمكن أن تساعدك على النجاح في حياتك الأكاديمية.',
            'location_title_en' => 'Location',
            'location_title_ku' => 'شوێن',
            'location_title_ar' => 'الموقع',
            'location_description_en' => 'The University Library is located in the heart of the campus, next to the main lecture halls and student center. It is easily accessible from all parts of the university, making it a convenient place to study and conduct research.',
            'location_description_ku' => 'کتێبخانەی زانکۆ لە دەستەی ناوەڕاستی کامپوسەکە، لە کەرەوەکانی ساڵەکانی گشتی و ناوەندەکانی خوێندکاران. بە ئاسانی دەتوانن لەگەڵ هەموو بەشەکانی زانکۆ دەستیان بکەن، کە ئەوەی کەتێبخانەی یەکێکەی ڕاحەلە بۆ خوێندن و ئەنجامدانی تحقیقات.',
            'location_description_ar' => 'تقع مكتبة الجامعة في قلب الحرم الجامعي، بجوار القاعات الرئيسية للمحاضرات ومركز الطلاب. يمكن الوصول إليها بسهولة من جميع أنحاء الجامعة، مما يجعلها مكانًا ملائمًا للدراسة وإجراء البحوث.',
            'coordinates' => '35.5614° N, 45.4301° E',]);
    }
}
