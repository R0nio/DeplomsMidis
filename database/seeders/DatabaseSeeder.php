<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Project;
use App\Models\Project_forecast;
use App\Models\Project_investment;
use App\Models\Project_photos;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::create([
        //     'name' => 'Алексей',
        //     'middlename' => 'Владимирович',
        //     'lastname' => 'Организаторов',
        //     'email' => 'organisator@example.com',
        //     'login' => 'org',
        //     'number' => '+7 (999) 111-22-33',
        //     'role' => 'Organisator',
        //     'organization_name' => 'ООО "ИнвестПроект"',
        //     'password' => 'aaaaaaaa'
        // ]);

        // User::create([
        //     'name' => 'Михаил',
        //     'middlename' => 'Андреевич',
        //     'lastname' => 'Инвесторов',
        //     'email' => 'investor@example.com',
        //     'login' => 'inv',
        //     'number' => '+7 (999) 444-55-66',
        //     'role' => 'Investor',
        //     'organization_name' => null,
        //     'password' => 'aaaaaaaa'
        // ]);

        // User::create([
        //     'name' => 'Александр',
        //     'middlename' => 'Иванов',
        //     'lastname' => 'Инвесторов',
        //     'email' => 'admin@example.com',
        //     'login' => 'admin',
        //     'number' => '+7 (999) 444-55-66',
        //     'role' => 'Admin',
        //     'organization_name' => null,
        //     'password' => '11111111'
        // ]);

        $organisator = User::create([
            'name' => 'Алексей',
            'middlename' => 'Владимирович',
            'lastname' => 'Организаторов',
            'email' => 'organisator@example.com',
            'login' => 'org',
            'number' => '+7 (999) 111-22-33',
            'role' => 'Organisator',
            'organization_name' => 'ООО "ИнвестПроект"',
            'password' => bcrypt('aaaaaaaa')
        ]);

        $investor = User::create([
            'name' => 'Михаил',
            'middlename' => 'Андреевич',
            'lastname' => 'Инвесторов',
            'email' => 'investor@example.com',
            'login' => 'inv',
            'number' => '+7 (999) 444-55-66',
            'role' => 'Investor',
            'organization_name' => null,
            'password' => bcrypt('aaaaaaaa')
        ]);

        $admin = User::create([
            'name' => 'Александр',
            'middlename' => 'Иванов',
            'lastname' => 'Инвесторов',
            'email' => 'admin@example.com',
            'login' => 'admin',
            'number' => '+7 (999) 444-55-66',
            'role' => 'Admin',
            'organization_name' => null,
            'password' => bcrypt('11111111')
        ]);

        $project1 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Индустриальный парк "Станкомаш"',
            'short_description' => 'Реконструкция территории завода "Станкомаш" под современный индустриальный парк',
            'full_description' => 'Проект предусматривает реконструкцию бывшей промышленной площадки завода "Станкомаш" площадью 45 гектаров. Создание современной инфраструктуры для размещения высокотехнологичных производств, логистических центров и офисных помещений.',
            'category' => json_encode(['Промышленность', 'Индустриальный парк', 'Недвижимость']),
            'status' => 'Одобрен',
            'total_investment' => 3200000000,
            'collected_total_investment' => 1200000000,
            'number_date_realise' => 30,
            'ownership' => 'ООО "Промышленные парки ЧО"',
            'activity' => 'Управление недвижимостью',
            'type_build' => 'Реконструкция',
            'count_new_job' => 800,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск, ул. Сортировочная, 38',
            'is_moderated' => true,
        ]);

        $project2 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Лыжный курорт "Солнечная долина"',
            'short_description' => 'Развитие горнолыжного курорта с расширением трасс и строительством гостиничного комплекса',
            'full_description' => 'Расширение действующего горнолыжного курорта: строительство 5 новых трасс разного уровня сложности, 2 кресельных канатных дорог, гостиницы на 150 номеров, SPA-центра и ресторана. Создание всесезонного курорта.',
            'category' => json_encode(['Туризм', 'Спорт', 'Гостиничный бизнес']),
            'status' => 'На модерации',
            'total_investment' => 1850000000,
            'collected_total_investment' => 450000000,
            'number_date_realise' => 24,
            'ownership' => 'ООО "Курорты Урала"',
            'activity' => 'Туризм',
            'type_build' => 'Расширение',
            'count_new_job' => 120,
            'latitude' => 55.046564,
            'longitude' => 59.973393,
            'address' => 'Челябинская область, г. Миасс, пос. Тургояк',
            'is_moderated' => false,
        ]);

        $project3 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Логистический центр "Челябинск-Сити"',
            'short_description' => 'Строительство мультимодального логистического центра у Транссибирской магистрали',
            'full_description' => 'Строительство логистического центра класса А площадью 120 000 кв.м. Включает складские помещения, контейнерный терминал, железнодорожные пути, автомобильную парковку. Интеграция с федеральной трассой М-5.',
            'category' => json_encode(['Логистика', 'Транспорт', 'Склад']),
            'status' => 'Одобрен',
            'total_investment' => 7200000000,
            'collected_total_investment' => 3200000000,
            'number_date_realise' => 36,
            'ownership' => 'ООО "Логистика Южного Урала"',
            'activity' => 'Складские услуги',
            'type_build' => 'Новое строительство',
            'count_new_job' => 450,
            'latitude' => 55.16462,
            'longitude' => 61.40085,
            'address' => 'Челябинская область, г. Челябинск, пос. Чурилово, ул. Транспортная, 1',
            'is_moderated' => true,
        ]);

        $projects = [$project1, $project2, $project3];

        $investmentNames = [
            'Строительно-монтажные работы',
            'Оборудование',
            'Проектные работы',
            'Пусконаладочные работы',
            'Земельный участок',
            'Коммуникации и инфраструктура',
            'Лицензирование и разрешения',
            'Оборотные средства',
            'ИТ-инфраструктура',
            'Обучение персонала',
            'Маркетинг и реклама',
        ];
        foreach ($projects as $project) {
            $numItems = rand(3, 5);
            $usedNames = [];
            
            for ($i = 0; $i < $numItems; $i++) {
                do {
                    $itemName = $investmentNames[array_rand($investmentNames)];
                } while (in_array($itemName, $usedNames));
                $usedNames[] = $itemName;
                
                Project_investment::create([
                    'project_id' => $project->id,
                    'item_name' => $itemName,
                    'amount' => rand(50000000, 500000000),
                ]);
            }
        }

        $currentYear = date('Y');
        
        foreach ($projects as $project) {
            for ($i = 0; $i < 5; $i++) {
                $year = $currentYear + $i;
                $expectedRevenue = rand(100000000, 2000000000);
                $expectedExpenses = rand(80000000, 1500000000);
                
                Project_forecast::create([
                    'project_id' => $project->id,
                    'year' => $year,
                    'expected_revenue' => $expectedRevenue,
                    'expected_expenses' => $expectedExpenses,
                ]);
            }
        }

        $randomProjectsForFavorites = array_rand($projects, 3);
        
        foreach ($randomProjectsForFavorites as $key) {
            Favorite::create([
                'user_id' => $investor->id,
                'project_id' => $projects[$key]->id,
            ]);
        }
        
        Favorite::create([
            'user_id' => $admin->id,
            'project_id' => $projects[0]->id,
        ]);
        
        Favorite::create([
            'user_id' => $organisator->id,
            'project_id' => $projects[1]->id,
        ]);
        
        Favorite::create([
            'user_id' => $organisator->id,
            'project_id' => $projects[2]->id,
        ]);
        Project_photos::create([
            'project_id' => 2,
            'photo_path' => 'projectsPhoto/2/TbT0L5C7ntGnTOvmBD6qvjV64zAjYIFFs158bmhn.jpg',
            'order' => 0,
        ]);
        Project_photos::create([
            'project_id' => 2,
            'photo_path' => 'projectsPhoto/2/WFyjpigJQ5Os5GM7rKdiBqo2KyxiDlHeZMludn0s.webp',
            'order' => 1,
        ]);
        Project_photos::create([
            'project_id' => 3,
            'photo_path' => 'projectsPhoto/3/DCQdxLcT0ExX6gA1xvtqljC3JT8NN65RyQMpu7Fe.webp',
            'order' => 0,
        ]);
    }
}
