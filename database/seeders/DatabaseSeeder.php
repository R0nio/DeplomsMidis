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

    public function run(): void
    {
        // Пользователи
        $organisator = User::create([
            'name' => 'Иванов',
            'middlename' => 'Владимирович',
            'lastname' => 'Спиридов',
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
            'lastname' => 'Шафурин',
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
            'lastname' => 'Зайцев',
            'email' => 'admin@example.com',
            'login' => 'admin',
            'number' => '+7 (999) 444-55-66',
            'role' => 'Admin',
            'organization_name' => null,
            'password' => bcrypt('aaaaaaaa')
        ]);


        // 1. Лыжероллерная трасса
        $project1 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство лыжероллерной трассы в Центральном районе города Челябинска',
            'short_description' => 'Строительство современной лыжероллерной трассы для круглогодичных тренировок',
            'full_description' => 'Строительство лыжероллерной трассы протяженностью 1250 метров в Центральном районе города Челябинска для проведения тренировок и соревнований по лыжным гонкам, биатлону, роллерным видам спорта.',
            'category' => json_encode(['Спорт', 'Инфраструктура']),
            'status' => 'Одобрен',
            'total_investment' => 180000000,
            'collected_total_investment' => 45000000,
            'number_date_realise' => 36,
            'ownership' => 'Муниципальная собственность',
            'activity' => 'Спортивная инфраструктура',
            'type_build' => 'Новое строительство',
            'count_new_job' => 15,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск, Центральный район',
            'is_moderated' => true,
        ]);

        // 2. IT-квартал 1736
        $project2 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'IT-квартал «IT-парк 1736 Квартал создателей»',
            'short_description' => 'Создание современного IT-кластера в центре Челябинска',
            'full_description' => 'Создание центра притяжения резидентов по развитию высокотехнологичных отраслей промышленности. IT-парк как центр городской культуры и бизнеса современной постиндустриальной культуры. Объединение IT-компаний, стартапов и креативных индустрий.',
            'category' => json_encode(['ИТ', 'Технопарк', 'Инновации']),
            'status' => 'Одобрен',
            'total_investment' => 418300000,
            'collected_total_investment' => 91900000,
            'number_date_realise' => 132,
            'ownership' => 'ООО «Управляющая компания «ФишерГрупп»',
            'activity' => 'Информационные технологии',
            'type_build' => 'Новое строительство',
            'count_new_job' => 500,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск, ул. Свободы, 1736',
            'is_moderated' => true,
        ]);

        // 3. Школа №48
        $project3 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство школы в микрорайоне № 48 жилого района №11 Краснопольской площадки № 1 в Курчатовском районе города Челябинска',
            'short_description' => 'Строительство современной школы на 1100 учащихся',
            'full_description' => 'Строительство современной общеобразовательной школы на 1100 мест в активно развивающемся микрорайоне Курчатовского района. Оснащение по современным стандартам: спортивные залы, актовый зал, библиотека, столовые, лаборатории.',
            'category' => json_encode(['Образование', 'Строительство']),
            'status' => 'Одобрен',
            'total_investment' => 950000000,
            'collected_total_investment' => 300000000,
            'number_date_realise' => 96,
            'ownership' => 'Муниципальная собственность',
            'activity' => 'Образование',
            'type_build' => 'Новое строительство',
            'count_new_job' => 80,
            'latitude' => 55.199087,
            'longitude' => 61.401107,
            'address' => 'Челябинская область, г. Челябинск, Курчатовский район, микрорайон №48',
            'is_moderated' => true,
        ]);

        // 4. Отель Мелиот
        $project4 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Отель «МЕЛИОТ»',
            'short_description' => 'Строительство современного отеля в центре Челябинска',
            'full_description' => 'Строительство четырехзвездочного отеля на 150 номеров с развитой инфраструктурой: ресторан, конференц-залы, фитнес-центр, SPA-комплекс. Создание современного гостиничного комплекса европейского уровня.',
            'category' => json_encode(['Гостиничный бизнес', 'Туризм']),
            'status' => 'Одобрен',
            'total_investment' => 680000000,
            'collected_total_investment' => 95000000,
            'number_date_realise' => 60,
            'ownership' => 'ООО СК «МАГИСТР»',
            'activity' => 'Гостиничный бизнес',
            'type_build' => 'Новое строительство',
            'count_new_job' => 50,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск, Центральный район',
            'is_moderated' => true,
        ]);

        // 5. Футбольный манеж
        $project5 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Крытый футбольный манеж по ул. Университетская Набережная в Калининском районе города Челябинска',
            'short_description' => 'Крытый футбольный манеж с искусственным покрытием для круглогодичных тренировок',
            'full_description' => 'Строительство крытого футбольного манежа с искусственным покрытием для осуществления круглогодичных тренировок по футболу и легкой атлетике. Возможность проведения соревнований любого уровня.',
            'category' => json_encode(['Спорт', 'Строительство']),
            'status' => 'На модерации',
            'total_investment' => 450000000,
            'collected_total_investment' => 80000000,
            'number_date_realise' => 84,
            'ownership' => 'Муниципальная собственность',
            'activity' => 'Спортивная инфраструктура',
            'type_build' => 'Новое строительство',
            'count_new_job' => 30,
            'latitude' => 55.199087,
            'longitude' => 61.401107,
            'address' => 'Челябинская область, г. Челябинск, Калининский район, ул. Университетская Набережная',
            'is_moderated' => false,
        ]);

        // 6. Спортивно-зрелищный комплекс (РМК-Арена)
        $project6 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Спортивно-зрелищный комплекс с объектами обслуживания инженерного и транспортного назначения в городе Челябинске',
            'short_description' => 'Крупный спортивный объект для единоборств и массовых мероприятий',
            'full_description' => 'Создание в Челябинске крупного спортивного объекта, который объединит в себе занятия всеми видами единоборств, игровыми видами спорта и проведение крупных массовых спортивных мероприятий.',
            'category' => json_encode(['Спорт', 'Развлечения']),
            'status' => 'Одобрен',
            'total_investment' => 5000000000,
            'collected_total_investment' => 1500000000,
            'number_date_realise' => 60,
            'ownership' => 'ООО «РМК-Арена»',
            'activity' => 'Спорт',
            'type_build' => 'Новое строительство',
            'count_new_job' => 363,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // 7. Проект «Притяжение»
        $project7 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Проект «Притяжение»',
            'short_description' => 'Масштабный общественно-культурный центр',
            'full_description' => 'Создание современного общественно-культурного пространства с парком, прогулочными зонами, фонтанами, арт-объектами, спортивными и детскими площадками. Новое городское пространство для отдыха и развлечений.',
            'category' => json_encode(['Культура', 'Развлечения', 'Благоустройство']),
            'status' => 'Одобрен',
            'total_investment' => 1512000000,
            'collected_total_investment' => 500000000,
            'number_date_realise' => 108,
            'ownership' => 'ООО «Территория Притяжения»',
            'activity' => 'Культура и развлечения',
            'type_build' => 'Новое строительство',
            'count_new_job' => 20,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // 8. Теннисный центр
        $project8 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство Теннисного центра в городе Челябинске',
            'short_description' => 'Современный теннисный центр для тренировок и соревнований',
            'full_description' => 'Строительство современного теннисного центра с крытыми и открытыми кортами, тренажерным залом, зоной отдыха. Возможность проведения турниров различного уровня.',
            'category' => json_encode(['Спорт']),
            'status' => 'На модерации',
            'total_investment' => 4800000000,
            'collected_total_investment' => 800000000,
            'number_date_realise' => 72,
            'ownership' => 'ООО «Мистраль»',
            'activity' => 'Спорт',
            'type_build' => 'Новое строительство',
            'count_new_job' => 35,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => false,
        ]);

        // 9. Индустриальный парк «Малая Сосновка»
        $project9 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Развитие индустриального парка «Малая Сосновка»',
            'short_description' => 'Развитие инфраструктуры индустриального парка',
            'full_description' => 'Развитие индустриального парка «Малая Сосновка»: строительство новых производственных корпусов, дорожной сети, инженерной инфраструктуры. Привлечение новых резидентов.',
            'category' => json_encode(['Промышленность', 'Инфраструктура']),
            'status' => 'Одобрен',
            'total_investment' => 1400000000,
            'collected_total_investment' => 350000000,
            'number_date_realise' => 120,
            'ownership' => 'АО «Корпорация развития Челябинской области»',
            'activity' => 'Промышленность',
            'type_build' => 'Расширение',
            'count_new_job' => 150,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, Сосновский район',
            'is_moderated' => true,
        ]);

        // 10. Школа единоборств «КРАШЕР»
        $project10 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Школа единоборств С.А. Ковалева «КРАШЕР»',
            'short_description' => 'Центр единоборств для подготовки спортсменов',
            'full_description' => 'Строительство современного центра единоборств с залами для тренировок по боксу, ММА, дзюдо, борьбе. Создание базы для подготовки спортсменов высокого уровня.',
            'category' => json_encode(['Спорт']),
            'status' => 'Одобрен',
            'total_investment' => 950000000,
            'collected_total_investment' => 200000000,
            'number_date_realise' => 108,
            'ownership' => 'ООО «Спортивный комплекс С. Ковалева «КРАШЕР»',
            'activity' => 'Спорт',
            'type_build' => 'Новое строительство',
            'count_new_job' => 27,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // 11. Фанпарк «Привилегия»
        $project11 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Создание в пос. Западный Кременкульского сельского поселения Фанпарка «Привилегия»',
            'short_description' => 'Крупный развлекательный парк с горнолыжными трассами',
            'full_description' => 'Создание современного горнолыжного и развлекательного парка с трассами различной сложности, гостиничным комплексом, зонами отдыха. Круглогодичный курорт.',
            'category' => json_encode(['Туризм', 'Развлечения', 'Спорт']),
            'status' => 'На модерации',
            'total_investment' => 7721800000,
            'collected_total_investment' => 1200000000,
            'number_date_realise' => 108,
            'ownership' => 'ООО «Специализированный застройщик «ФлайПлэнингПартнерс»',
            'activity' => 'Туризм',
            'type_build' => 'Новое строительство',
            'count_new_job' => 22,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, Кременкульское сельское поселение, пос. Западный',
            'is_moderated' => false,
        ]);

        // 12. Термальный комплекс на озере Смолино
        $project12 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство термального комплекса с помещениями для организации общественного питания на набережной озера Смолино в городе Челябинске по улице Новороссийской',
            'short_description' => 'Термальный комплекс с зонами отдыха и питания',
            'full_description' => 'Строительство современного термального комплекса с бассейнами, саунами, зонами отдыха, ресторанами и кафе. Создание круглогодичного курорта на берегу озера Смолино.',
            'category' => json_encode(['Туризм', 'Развлечения', 'Общепит']),
            'status' => 'Одобрен',
            'total_investment' => 350000000,
            'collected_total_investment' => 50000000,
            'number_date_realise' => 48,
            'ownership' => 'ООО «Империя игр»',
            'activity' => 'Туризм',
            'type_build' => 'Новое строительство',
            'count_new_job' => 40,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск, ул. Новороссийская, набережная оз. Смолино',
            'is_moderated' => true,
        ]);

        // 13. Завод по производству автоклавного газобетона
        $project13 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство завода по производству автоклавного газобетона в городе Челябинске',
            'short_description' => 'Новое производство строительных материалов',
            'full_description' => 'Строительство современного завода по производству автоклавного газобетона мощностью 500 000 куб.м в год. Обеспечение рынка качественными строительными материалами.',
            'category' => json_encode(['Производство', 'Строительство']),
            'status' => 'На модерации',
            'total_investment' => 890000000,
            'collected_total_investment' => 120000000,
            'number_date_realise' => 24,
            'ownership' => 'ООО «НКТ-ПРОЕКТ»',
            'activity' => 'Производство',
            'type_build' => 'Новое строительство',
            'count_new_job' => 60,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => false,
        ]);

        // 14. Общественно-деловой центр «Конгресс-холл»
        $project14 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство общественно-делового центра на пересечении ул. Береговая и ул. Бр. Кашириных в Калининском районе г. Челябинска',
            'short_description' => 'Многофункциональный деловой центр',
            'full_description' => 'Строительство современного общественно-делового центра с конференц-залами, офисными помещениями, выставочными площадями, зонами отдыха и общепита.',
            'category' => json_encode(['Бизнес', 'Инфраструктура']),
            'status' => 'Одобрен',
            'total_investment' => 6800000000,
            'collected_total_investment' => 1500000000,
            'number_date_realise' => 120,
            'ownership' => 'Хозяйственное партнерство «Конгресс-холл»',
            'activity' => 'Бизнес',
            'type_build' => 'Новое строительство',
            'count_new_job' => 250,
            'latitude' => 55.199087,
            'longitude' => 61.401107,
            'address' => 'Челябинская область, г. Челябинск, Калининский район, пересечение ул. Береговая и ул. Бр. Кашириных',
            'is_moderated' => true,
        ]);

        // 15. Производство мебельного щита
        $project15 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Создание производства мебельного щита и других высокотехнологичных изделий из древесины в Челябинской области',
            'short_description' => 'Высокотехнологичное деревообрабатывающее производство',
            'full_description' => 'Создание современного производства мебельного щита и высокотехнологичных изделий из древесины. Применение инновационных технологий обработки древесины.',
            'category' => json_encode(['Производство', 'Деревообработка']),
            'status' => 'Одобрен',
            'total_investment' => 129000000,
            'collected_total_investment' => 25000000,
            'number_date_realise' => 48,
            'ownership' => 'ООО «ЧООК»',
            'activity' => 'Производство',
            'type_build' => 'Новое строительство',
            'count_new_job' => 50,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область',
            'is_moderated' => true,
        ]);

        $projects = [
            $project1, $project2, $project3, $project4, $project5,
            $project6, $project7, $project8, $project9, $project10,
            $project11, $project12, $project13, $project14, $project15
        ];

        // Инвестиции (расходы по проектам)
        $investmentNames = [
            'Строительно-монтажные работы',
            'Оборудование и инвентарь',
            'Проектные и изыскательские работы',
            'Пусконаладочные работы',
            'Земельный участок и подготовка территории',
            'Коммуникации и инженерная инфраструктура',
            'Благоустройство территории',
            'ИТ-инфраструктура',
            'Обучение персонала',
            'Маркетинг и реклама',
            'Лицензирование и сертификация',
        ];

        foreach ($projects as $project) {
            $numItems = rand(3, 6);
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

        // Прогнозы на 5 лет
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

        // Избранные проекты для инвестора
        $favoriteProjects = [$project1->id, $project3->id, $project4->id, $project6->id, $project7->id];
        foreach ($favoriteProjects as $projectId) {
            Favorite::create([
                'user_id' => $investor->id,
                'project_id' => $projectId,
            ]);
        }
    }
}