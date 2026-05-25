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

        // Проект 1
        $project1 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Система газоочистки Вельц-цеха печей № 3-6',
            'short_description' => 'Очистка отходящих газов от оксида серы со степенью эффективности не менее 94%',
            'full_description' => 'Система газоочистки Вельц-цеха печей № 3-6 на АО «ЧЦЗ». Очистка 280-330 тыс.нм³/ч отходящих газов от SO2 (оксида серы) со степенью эффективности не менее 94%',
            'category' => json_encode(['Промышленность', 'Экология']),
            'status' => 'Одобрен',
            'total_investment' => 250000000,
            'collected_total_investment' => 50000000,
            'number_date_realise' => 72,
            'ownership' => 'АО «ЧЦЗ»',
            'activity' => 'Металлургия',
            'type_build' => 'Модернизация',
            'count_new_job' => 15,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 2
        $project2 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Участок очистных сооружений',
            'short_description' => 'Прекращение сброса сточных вод в реку Миасс. Организация замкнутого водооборотного цикла предприятия',
            'full_description' => 'Строительство участка очистных сооружений для прекращения сброса сточных вод в реку Миасс. Организация замкнутого водооборотного цикла предприятия',
            'category' => json_encode(['Экология', 'Водоснабжение']),
            'status' => 'Одобрен',
            'total_investment' => 380000000,
            'collected_total_investment' => 100000000,
            'number_date_realise' => 84,
            'ownership' => 'АО «ЧЦЗ»',
            'activity' => 'Водоочистка',
            'type_build' => 'Новое строительство',
            'count_new_job' => 25,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 3
        $project3 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Крытый футбольный манеж по ул. Университетская Набережная в Калининском районе города Челябинска',
            'short_description' => 'Осуществление круглогодичных тренировок по футболу и легкой атлетике',
            'full_description' => 'Строительство крытого футбольного манежа для осуществления круглогодичных тренировок по футболу и легкой атлетике',
            'category' => json_encode(['Спорт', 'Строительство']),
            'status' => 'На модерации',
            'total_investment' => 450000000,
            'collected_total_investment' => 80000000,
            'number_date_realise' => 84,
            'ownership' => 'Муниципальная',
            'activity' => 'Спорт',
            'type_build' => 'Новое строительство',
            'count_new_job' => 30,
            'latitude' => 55.199087,
            'longitude' => 61.401107,
            'address' => 'Челябинская область, г. Челябинск, ул. Университетская Набережная',
            'is_moderated' => false,
        ]);

        // Проект 4
        $project4 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Увеличение мощностей по производству отводов гнутых ТВЧ Ø530-1420мм',
            'short_description' => 'Строительство участка, приобретение и установка оборудования для обработки отводов гнутых',
            'full_description' => 'Увеличение мощностей по производству отводов гнутых ТВЧ Ø530-1420мм. Строительство участка, приобретение, установка и запуск в эксплуатацию оборудования для обработки отводов гнутых',
            'category' => json_encode(['Промышленность', 'Машиностроение']),
            'status' => 'Одобрен',
            'total_investment' => 520000000,
            'collected_total_investment' => 150000000,
            'number_date_realise' => 36,
            'ownership' => 'АО «Трубодеталь»',
            'activity' => 'Металлообработка',
            'type_build' => 'Расширение',
            'count_new_job' => 45,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 5
        $project5 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Инвестиционная программа филиала ПАО «Россети Урал» - «Челябэнерго»',
            'short_description' => 'Инвестиционная программа развития энергосистемы Челябинской области',
            'full_description' => 'Инвестиционная программа филиала ПАО «Россети Урал» - «Челябэнерго» по развитию и модернизации энергосистемы Челябинской области',
            'category' => json_encode(['Энергетика', 'Инфраструктура']),
            'status' => 'Одобрен',
            'total_investment' => 8500000000,
            'collected_total_investment' => 2000000000,
            'number_date_realise' => 48,
            'ownership' => 'ПАО «Россети Урал»',
            'activity' => 'Энергетика',
            'type_build' => 'Модернизация',
            'count_new_job' => 120,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область',
            'is_moderated' => true,
        ]);

        // Проект 6
        $project6 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Отель «МЕЛИОТ»',
            'short_description' => 'Строительство отеля',
            'full_description' => 'Строительство современного отеля на территории города Челябинска',
            'category' => json_encode(['Гостиничный бизнес', 'Туризм']),
            'status' => 'На модерации',
            'total_investment' => 680000000,
            'collected_total_investment' => 95000000,
            'number_date_realise' => 60,
            'ownership' => 'ООО СК «МАГИСТР»',
            'activity' => 'Гостиничный бизнес',
            'type_build' => 'Новое строительство',
            'count_new_job' => 50,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => false,
        ]);

        // Проект 7
        $project7 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Спортивно-зрелищный комплекс с объектами обслуживания инженерного и транспортного назначения в городе Челябинске',
            'short_description' => 'Создание крупного спортивного объекта для занятий единоборствами и проведения массовых мероприятий',
            'full_description' => 'Создание в Челябинске крупного спортивного объекта, который объединит в себе занятия всеми видами единоборств, игровыми видами и проведение крупных массовых спортивных мероприятий',
            'category' => json_encode(['Спорт', 'Развлечения']),
            'status' => 'Одобрен',
            'total_investment' => 3200000000,
            'collected_total_investment' => 780000000,
            'number_date_realise' => 60,
            'ownership' => 'ООО «РМК-Арена»',
            'activity' => 'Спорт',
            'type_build' => 'Новое строительство',
            'count_new_job' => 200,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 8
        $project8 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Станция УФ-обеззараживания фильтрованной воды на очистных сооружениях водопровода города Челябинска',
            'short_description' => 'Модернизация системы водоподготовки мощностью 28 500 м³/ч',
            'full_description' => 'Станция УФ-обеззараживания фильтрованной воды на очистных сооружениях водопровода города Челябинска, включая ПИР. Мощность 28 500 м³/ч',
            'category' => json_encode(['Водоснабжение', 'Экология']),
            'status' => 'Одобрен',
            'total_investment' => 420000000,
            'collected_total_investment' => 120000000,
            'number_date_realise' => 96,
            'ownership' => 'Управление капитального строительства Администрации города Челябинска',
            'activity' => 'Водоснабжение',
            'type_build' => 'Модернизация',
            'count_new_job' => 20,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 9
        $project9 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство школы в микрорайоне № 48 жилого района №11 Краснопольской площадки № 1 в Курчатовском районе города Челябинска',
            'short_description' => 'Строительство школы на 1100 учащихся',
            'full_description' => 'Строительство современной школы на 1100 учащихся в Курчатовском районе города Челябинска',
            'category' => json_encode(['Образование', 'Строительство']),
            'status' => 'Одобрен',
            'total_investment' => 950000000,
            'collected_total_investment' => 300000000,
            'number_date_realise' => 96,
            'ownership' => 'Муниципальная',
            'activity' => 'Образование',
            'type_build' => 'Новое строительство',
            'count_new_job' => 80,
            'latitude' => 55.199087,
            'longitude' => 61.401107,
            'address' => 'Челябинская область, г. Челябинск, Курчатовский район',
            'is_moderated' => true,
        ]);

        // Проект 10
        $project10 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство школы в микрорайоне № 57 в Курчатовском районе города Челябинска',
            'short_description' => 'Строительство школы на 1100 учащихся',
            'full_description' => 'Строительство современной школы на 1100 учащихся в Курчатовском районе города Челябинска',
            'category' => json_encode(['Образование', 'Строительство']),
            'status' => 'На модерации',
            'total_investment' => 890000000,
            'collected_total_investment' => 150000000,
            'number_date_realise' => 48,
            'ownership' => 'Муниципальная',
            'activity' => 'Образование',
            'type_build' => 'Новое строительство',
            'count_new_job' => 80,
            'latitude' => 55.199087,
            'longitude' => 61.401107,
            'address' => 'Челябинская область, г. Челябинск, Курчатовский район',
            'is_moderated' => false,
        ]);

        // Проект 11
        $project11 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство лыжероллерной трассы в Центральном районе города Челябинска, 2 этап',
            'short_description' => 'Строительство лыжероллерной трассы протяженностью 1250 м',
            'full_description' => 'Второй этап строительства лыжероллерной трассы в Центральном районе города Челябинска. Протяженность 1250 м',
            'category' => json_encode(['Спорт', 'Туризм']),
            'status' => 'Одобрен',
            'total_investment' => 180000000,
            'collected_total_investment' => 45000000,
            'number_date_realise' => 36,
            'ownership' => 'Муниципальная',
            'activity' => 'Спорт',
            'type_build' => 'Расширение',
            'count_new_job' => 10,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск, Центральный район',
            'is_moderated' => true,
        ]);

        // Проект 12
        $project12 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Реконструкция очистных сооружений канализации города Челябинска',
            'short_description' => 'Реконструкция очистных сооружений мощностью 400 тыс. м³/сут.',
            'full_description' => 'Реконструкция очистных сооружений канализации города Челябинска мощностью 400 тыс. м³/сут.',
            'category' => json_encode(['Экология', 'Водоснабжение']),
            'status' => 'Одобрен',
            'total_investment' => 2800000000,
            'collected_total_investment' => 600000000,
            'number_date_realise' => 48,
            'ownership' => 'Муниципальная',
            'activity' => 'Водоочистка',
            'type_build' => 'Реконструкция',
            'count_new_job' => 60,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 13
        $project13 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Система защиты и повышения барьерных функций очистных сооружений водопровода города Челябинска с использованием порошкообразного активированного угля',
            'short_description' => 'Модернизация системы водоподготовки с использованием активированного угля',
            'full_description' => 'Система защиты и повышения барьерных функций очистных сооружений водопровода города Челябинска с использованием порошкообразного активированного угля, включая ПИР. Мощность 33-82 м³/ч',
            'category' => json_encode(['Водоснабжение', 'Экология']),
            'status' => 'На модерации',
            'total_investment' => 350000000,
            'collected_total_investment' => 70000000,
            'number_date_realise' => 36,
            'ownership' => 'Муниципальная',
            'activity' => 'Водоснабжение',
            'type_build' => 'Модернизация',
            'count_new_job' => 15,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => false,
        ]);

        // Проект 14
        $project14 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство защитных сооружений в границах от Свердловского проспекта до ул. Университетская Набережная в Калининском и Центральном районах города Челябинска (1 и 2 этапы)',
            'short_description' => 'Строительство защитных сооружений протяженностью 842,5 м',
            'full_description' => 'Строительство защитных сооружений в границах от Свердловского проспекта до ул. Университетская Набережная в Калининском и Центральном районах города Челябинска. Протяженность 842,5 м',
            'category' => json_encode(['Строительство', 'Безопасность']),
            'status' => 'Одобрен',
            'total_investment' => 560000000,
            'collected_total_investment' => 120000000,
            'number_date_realise' => 36,
            'ownership' => 'Муниципальная',
            'activity' => 'Строительство',
            'type_build' => 'Новое строительство',
            'count_new_job' => 40,
            'latitude' => 55.199087,
            'longitude' => 61.401107,
            'address' => 'Челябинская область, г. Челябинск, Калининский и Центральный районы',
            'is_moderated' => true,
        ]);

        // Проект 15
        $project15 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство защитных сооружений в границах от Свердловского проспекта до ул. Университетская Набережная в Калининском и Центральном районах города Челябинска (4 этап)',
            'short_description' => 'Строительство защитных сооружений протяженностью 900 п.м',
            'full_description' => '4 этап строительства защитных сооружений в границах от Свердловского проспекта до ул. Университетская Набережная. Протяженность 900 п.м',
            'category' => json_encode(['Строительство', 'Безопасность']),
            'status' => 'На модерации',
            'total_investment' => 430000000,
            'collected_total_investment' => 85000000,
            'number_date_realise' => 36,
            'ownership' => 'Муниципальная',
            'activity' => 'Строительство',
            'type_build' => 'Новое строительство',
            'count_new_job' => 35,
            'latitude' => 55.199087,
            'longitude' => 61.401107,
            'address' => 'Челябинская область, г. Челябинск, Калининский и Центральный районы',
            'is_moderated' => false,
        ]);

        // Проект 16
        $project16 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Реконструкция очистных сооружений канализации города Челябинска (3 этап)',
            'short_description' => 'Реконструкция очистных сооружений мощностью 400 м³/ч',
            'full_description' => '3 этап реконструкции очистных сооружений канализации города Челябинска мощностью 400 м³/ч',
            'category' => json_encode(['Экология', 'Водоснабжение']),
            'status' => 'Одобрен',
            'total_investment' => 620000000,
            'collected_total_investment' => 180000000,
            'number_date_realise' => 36,
            'ownership' => 'Муниципальная',
            'activity' => 'Водоочистка',
            'type_build' => 'Реконструкция',
            'count_new_job' => 25,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 17
        $project17 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Проект по установке упаковочного комплекса на технологической линии №2',
            'short_description' => 'Увеличение производительности и снижение потерь',
            'full_description' => 'Проект по установке упаковочного комплекса на технологической линии №2 филиала ООО «Завод ТЕХНО» город Челябинск. Увеличение производительности и снижение потерь',
            'category' => json_encode(['Промышленность', 'Модернизация']),
            'status' => 'Одобрен',
            'total_investment' => 89000000,
            'collected_total_investment' => 25000000,
            'number_date_realise' => 24,
            'ownership' => 'Филиал ООО «Завод ТЕХНО»',
            'activity' => 'Производство',
            'type_build' => 'Модернизация',
            'count_new_job' => 8,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 18
        $project18 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство объекта заправки транспортных средств компримированным природным газом',
            'short_description' => 'Строительство АГНКС для заправки транспорта природным газом',
            'full_description' => 'Строительство объекта заправки транспортных средств компримированным природным газом. Размещение на территории города Челябинска автомобильных газовых накопительных компримированных станций природного газа (метана)',
            'category' => json_encode(['Энергетика', 'Транспорт']),
            'status' => 'На модерации',
            'total_investment' => 156000000,
            'collected_total_investment' => 30000000,
            'number_date_realise' => 84,
            'ownership' => 'ООО «НОМИНАЛ-ОЙЛ»',
            'activity' => 'Энергетика',
            'type_build' => 'Новое строительство',
            'count_new_job' => 12,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => false,
        ]);

        // Проект 19
        $project19 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Предприятие общественного питания быстрого обслуживания «Вкусно и точка»',
            'short_description' => 'Строительство сети ресторанов «Вкусно и точка»',
            'full_description' => 'Строительство сети ресторанов быстрого обслуживания «Вкусно и точка»',
            'category' => json_encode(['Общепит', 'Ритейл']),
            'status' => 'Одобрен',
            'total_investment' => 125000000,
            'collected_total_investment' => 35000000,
            'number_date_realise' => 96,
            'ownership' => 'ООО «СПП»',
            'activity' => 'Общественное питание',
            'type_build' => 'Новое строительство',
            'count_new_job' => 60,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 20
        $project20 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Создание в Челябинске сети автомобильных газонаполнительных компрессорных станций',
            'short_description' => 'Строительство сети АГНКС для заправки ТС природным газом',
            'full_description' => 'Создание в Челябинске сети автомобильных газонаполнительных компрессорных станций для заправки транспортных средств природным газом (метан)',
            'category' => json_encode(['Энергетика', 'Транспорт']),
            'status' => 'На модерации',
            'total_investment' => 340000000,
            'collected_total_investment' => 68000000,
            'number_date_realise' => 72,
            'ownership' => 'ООО «Партнёр»',
            'activity' => 'Энергетика',
            'type_build' => 'Новое строительство',
            'count_new_job' => 25,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => false,
        ]);

        // Проект 21
        $project21 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Внедрение микромобильного транспорта',
            'short_description' => 'Запуск в Челябинске сервиса кикшеринга электросамокатов',
            'full_description' => 'Запуск в городе Челябинске сервиса кикшеринга электросамокатов',
            'category' => json_encode(['Транспорт', 'ИТ']),
            'status' => 'Одобрен',
            'total_investment' => 45000000,
            'collected_total_investment' => 12000000,
            'number_date_realise' => 60,
            'ownership' => 'ООО «ВУШ»',
            'activity' => 'Транспортные услуги',
            'type_build' => 'Внедрение',
            'count_new_job' => 30,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 22
        $project22 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Грузовой шинный центр Cordiant Professional',
            'short_description' => 'Центр по комплексному обслуживанию грузовых автомобилей',
            'full_description' => 'Грузовой шинный центр Cordiant Professional. Центр по комплексному обслуживанию грузовых автомобилей под одной крышей: грузовой шиномонтаж, автомойка, замена масел, мелкосрочный ремонт, ремонт тентов и т.д.',
            'category' => json_encode(['Автосервис', 'Транспорт']),
            'status' => 'На модерации',
            'total_investment' => 89000000,
            'collected_total_investment' => 15000000,
            'number_date_realise' => 60,
            'ownership' => 'ООО «Автобизнес»',
            'activity' => 'Автосервис',
            'type_build' => 'Новое строительство',
            'count_new_job' => 20,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => false,
        ]);

        // Проект 23
        $project23 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Внедрение микромобильного транспорта последней мили «ЮрентБайк»',
            'short_description' => 'Сервис поминутной аренды электросамокатов и велосипедов',
            'full_description' => 'Сервис поминутной аренды электросамокатов и велосипедов «ЮрентБайк»',
            'category' => json_encode(['Транспорт', 'ИТ']),
            'status' => 'Одобрен',
            'total_investment' => 67000000,
            'collected_total_investment' => 18000000,
            'number_date_realise' => 48,
            'ownership' => 'ООО «ЮрентБайк.ру»',
            'activity' => 'Транспортные услуги',
            'type_build' => 'Внедрение',
            'count_new_job' => 25,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 24
        $project24 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство производственно-складских помещений для производства тротуарной плитки',
            'short_description' => 'Строительство производственно-складских помещений',
            'full_description' => 'Строительство производственно-складских помещений для производства тротуарной плитки и складирования строительных материалов',
            'category' => json_encode(['Строительство', 'Производство']),
            'status' => 'Одобрен',
            'total_investment' => 234000000,
            'collected_total_investment' => 56000000,
            'number_date_realise' => 48,
            'ownership' => 'ООО «Строймиг»',
            'activity' => 'Производство',
            'type_build' => 'Новое строительство',
            'count_new_job' => 35,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 25
        $project25 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Развитие центра креативных индустрий SVOBODA2',
            'short_description' => 'Создание креативного кластера',
            'full_description' => 'Создание креативного кластера - сообщества креативных (творческих) предпринимателей, взаимодействующих на единой территории',
            'category' => json_encode(['Креативные индустрии', 'Культура']),
            'status' => 'На модерации',
            'total_investment' => 89000000,
            'collected_total_investment' => 12000000,
            'number_date_realise' => 132,
            'ownership' => 'ИП Грачев А.В.',
            'activity' => 'Креативные индустрии',
            'type_build' => 'Развитие',
            'count_new_job' => 50,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => false,
        ]);

        // Проект 26
        $project26 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Создание предприятия по переработке многослойной упаковки на основе картона',
            'short_description' => 'Переработка тетрапака для получения бумажной массы',
            'full_description' => 'Реконструкция или строительство производственного здания по переработке многослойной упаковки на основе картона («тетрапак») для получения бумажной массы и полиала на реализацию',
            'category' => json_encode(['Экология', 'Переработка']),
            'status' => 'Одобрен',
            'total_investment' => 178000000,
            'collected_total_investment' => 34000000,
            'number_date_realise' => 24,
            'ownership' => 'ООО «ПК «ЦУП»',
            'activity' => 'Переработка отходов',
            'type_build' => 'Реконструкция',
            'count_new_job' => 18,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 27
        $project27 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Расширение производства обогатительного и горно-шахтного оборудования с целью импортозамещения',
            'short_description' => 'Увеличение объемов производства и импортозамещение',
            'full_description' => 'Увеличение объемов реализации и расширение линейки производимого обогатительного и горно-шахтного оборудования за счет замещения импортного оборудования',
            'category' => json_encode(['Машиностроение', 'Импортозамещение']),
            'status' => 'На модерации',
            'total_investment' => 345000000,
            'collected_total_investment' => 67000000,
            'number_date_realise' => 24,
            'ownership' => 'ООО НПО «Пневмаш»',
            'activity' => 'Машиностроение',
            'type_build' => 'Расширение',
            'count_new_job' => 30,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => false,
        ]);

        // Проект 28
        $project28 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Расширение и модернизация производства изделий из вторичного сырья',
            'short_description' => 'Расширение производства изделий из вторичного сырья',
            'full_description' => 'Строительство дополнительных производственных и складских площадей с целью расширения производства изделий из вторичного сырья (пластиковые люки, элементы городских сооружений и пр.)',
            'category' => json_encode(['Экология', 'Производство']),
            'status' => 'Одобрен',
            'total_investment' => 234000000,
            'collected_total_investment' => 56000000,
            'number_date_realise' => 120,
            'ownership' => 'ООО «Символ»',
            'activity' => 'Производство',
            'type_build' => 'Расширение',
            'count_new_job' => 25,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 29
        $project29 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство завода по производству автоклавного газобетона в городе Челябинске',
            'short_description' => 'Строительство завода по производству газобетона',
            'full_description' => 'Строительство завода по производству автоклавного газобетона в городе Челябинске',
            'category' => json_encode(['Строительство', 'Производство']),
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

        // Проект 30
        $project30 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство предприятия по изготовлению металлических изделий и металлоконструкций в городе Челябинске',
            'short_description' => 'Строительство предприятия по изготовлению металлоконструкций',
            'full_description' => 'Строительство предприятия по изготовлению металлических изделий и металлоконструкций в городе Челябинске',
            'category' => json_encode(['Металлообработка', 'Производство']),
            'status' => 'Одобрен',
            'total_investment' => 567000000,
            'collected_total_investment' => 89000000,
            'number_date_realise' => 24,
            'ownership' => 'ООО «НКТ»',
            'activity' => 'Металлообработка',
            'type_build' => 'Новое строительство',
            'count_new_job' => 45,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 31
        $project31 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Строительство сети АГНКС по заправке транспорта компримированным газом (метан) в городе Челябинске',
            'short_description' => 'Строительство сети АГНКС для заправки метаном',
            'full_description' => 'Строительство сети АГНКС по заправке транспорта компримированным газом (метан) в городе Челябинске',
            'category' => json_encode(['Энергетика', 'Транспорт']),
            'status' => 'На модерации',
            'total_investment' => 456000000,
            'collected_total_investment' => 78000000,
            'number_date_realise' => 24,
            'ownership' => 'ООО «МЕТСЕРВИС»',
            'activity' => 'Энергетика',
            'type_build' => 'Новое строительство',
            'count_new_job' => 35,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => false,
        ]);

        // Проект 32
        $project32 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Создание промышленного технопарка',
            'short_description' => 'Строительство промышленного технопарка',
            'full_description' => 'Строительство промышленного технопарка предполагает создание производственных площадок для размещения резидентов отрасли трубопроводной арматуры, а также производства латунных фитингов и шаровых кранов, оснастки и других продуктов машиностроения и металлообработки',
            'category' => json_encode(['Промышленность', 'Технопарк']),
            'status' => 'Одобрен',
            'total_investment' => 3200000000,
            'collected_total_investment' => 450000000,
            'number_date_realise' => 60,
            'ownership' => 'ООО «УК «Технопарк ЛД»',
            'activity' => 'Управление недвижимостью',
            'type_build' => 'Новое строительство',
            'count_new_job' => 300,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 33
        $project33 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Модернизация производства с целью перехода на серийный выпуск комплекса радионавигационного оборудования',
            'short_description' => 'Переход к серийному производству радионавигационного оборудования',
            'full_description' => 'Переход к серийному производству комплекса радионавигационного оборудования',
            'category' => json_encode(['Машиностроение', 'ИТ']),
            'status' => 'На модерации',
            'total_investment' => 234000000,
            'collected_total_investment' => 45000000,
            'number_date_realise' => 84,
            'ownership' => 'ООО НПО «РТС»',
            'activity' => 'Радиоэлектроника',
            'type_build' => 'Модернизация',
            'count_new_job' => 20,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => false,
        ]);

        // Проект 34
        $project34 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Создание инфраструктуры для автомобилей, работающих на газомоторном топливе в городе Челябинске',
            'short_description' => 'Создание сети АГНКС и СТО для газомоторного топлива',
            'full_description' => 'Создание сети АГНКС для автомобилей, которые работают на газомоторном топливе, а также СТО для данного транспорта в городе Челябинске',
            'category' => json_encode(['Энергетика', 'Транспорт']),
            'status' => 'Одобрен',
            'total_investment' => 456000000,
            'collected_total_investment' => 89000000,
            'number_date_realise' => 96,
            'ownership' => 'ООО «АрТранс»',
            'activity' => 'Энергетика',
            'type_build' => 'Новое строительство',
            'count_new_job' => 40,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => true,
        ]);

        // Проект 35
        $project35 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'IT-квартал «IT-парк 1736 Квартал создателей»',
            'short_description' => 'Создание IT-парка как центра притяжения высокотехнологичных отраслей',
            'full_description' => 'Создание центра притяжения резидентов по развитию высокотехнологичных отраслей промышленности. IT-парк, как центр городской культуры и бизнеса современной постиндустриальной культуры',
            'category' => json_encode(['ИТ', 'Технопарк']),
            'status' => 'На модерации',
            'total_investment' => 4500000000,
            'collected_total_investment' => 680000000,
            'number_date_realise' => 132,
            'ownership' => 'ООО «ИТ-Парк1736»',
            'activity' => 'ИТ',
            'type_build' => 'Новое строительство',
            'count_new_job' => 500,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск',
            'is_moderated' => false,
        ]);

        // Проект 36
        $project36 = Project::create([
            'user_id' => $organisator->id,
            'title' => 'Осуществление действий, направленных на энергосбережение и повышение энергетической эффективности при использовании электрической энергии при эксплуатации объектов наружного (уличного) освещения Металлургического района города Челябинска',
            'short_description' => 'Энергосбережение систем уличного освещения',
            'full_description' => 'Осуществление действий, направленных на энергосбережение и повышение энергетической эффективности при использовании (потреблении) электрической энергии при эксплуатации объектов наружного (уличного) освещения Металлургического района города Челябинска',
            'category' => json_encode(['Энергетика', 'Инфраструктура']),
            'status' => 'Одобрен',
            'total_investment' => 89000000,
            'collected_total_investment' => 23000000,
            'number_date_realise' => 84,
            'ownership' => 'Комитет дорожного хозяйства города Челябинска',
            'activity' => 'Энергосбережение',
            'type_build' => 'Модернизация',
            'count_new_job' => 5,
            'latitude' => 55.146845,
            'longitude' => 61.464444,
            'address' => 'Челябинская область, г. Челябинск, Металлургический район',
            'is_moderated' => true,
        ]);

        $projects = [
            $project1, $project2, $project3, $project4, $project5,
            $project6, $project7, $project8, $project9, $project10,
            $project11, $project12, $project13, $project14, $project15,
            $project16, $project17, $project18, $project19, $project20,
            $project21, $project22, $project23, $project24, $project25,
            $project26, $project27, $project28, $project29, $project30,
            $project31, $project32, $project33, $project34, $project35, $project36
        ];

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

        // Избранное для инвестора (первые 5 проектов)
        for ($i = 0; $i < 5 && $i < count($projects); $i++) {
            Favorite::create([
                'user_id' => $investor->id,
                'project_id' => $projects[$i]->id,
            ]);
        }
        
        // Избранное для админа
        Favorite::create([
            'user_id' => $admin->id,
            'project_id' => $projects[0]->id,
        ]);
        
        // Избранное для организатора
        Favorite::create([
            'user_id' => $organisator->id,
            'project_id' => $projects[1]->id,
        ]);
        
        Favorite::create([
            'user_id' => $organisator->id,
            'project_id' => $projects[2]->id,
        ]);

        // // Фотографии для проектов (пример)
        // $photoPaths = [
        //     'projectsPhoto/2/TbT0L5C7ntGnTOvmBD6qvjV64zAjYIFFs158bmhn.jpg',
        //     'projectsPhoto/2/WFyjpigJQ5Os5GM7rKdiBqo2KyxiDlHeZMludn0s.webp',
        //     'projectsPhoto/3/DCQdxLcT0ExX6gA1xvtqljC3JT8NN65RyQMpu7Fe.webp',
        // ];

        // foreach ($projects as $index => $project) {
        //     if ($index < count($photoPaths)) {
        //         Project_photos::create([
        //             'project_id' => $project->id,
        //             'photo_path' => $photoPaths[$index % count($photoPaths)],
        //             'order' => 0,
        //         ]);
        //     }
        // }
    }
}