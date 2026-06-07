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
            'name' => 'Иван',
            'middlename' => 'Владимирович',
            'lastname' => 'Спиридов',
            'email' => 'spiridov@investpro.ru',
            'login' => 'organisator',
            'number' => '+7 (951) 777-88-99',
            'role' => 'Organisator',
            'organization_name' => 'ООО "ИнвестПроект Челябинск"',
            'password' => bcrypt('aaaaaaaa'),
        ]);

        $organisator1 = User::create([
            'name' => 'Елена',
            'middlename' => 'Сергеевна',
            'lastname' => 'Ковалёва',
            'email' => 'kovalyova@turgojak.ru',
            'login' => 'kovalyova',
            'number' => '+7 (922) 555-44-33',
            'role' => 'Organisator',
            'organization_name' => 'ООО "Баден-Баден Тургояк"',
            'password' => bcrypt('aaaaaaaa'),
        ]);

        $organisator2 = User::create([
            'name' => 'Дмитрий',
            'middlename' => 'Александрович',
            'lastname' => 'Морозов',
            'email' => 'morozov@promtech74.ru',
            'login' => 'morozov',
            'number' => '+7 (919) 333-22-11',
            'role' => 'Organisator',
            'organization_name' => 'ООО "ПромТех"',
            'password' => bcrypt('aaaaaaaa'),
        ]);

        $investor = User::create([
            'name' => 'Михаил',
            'middlename' => 'Андреевич',
            'lastname' => 'Шафурин',
            'email' => 'shafurin@investor.ru',
            'login' => 'investor',
            'number' => '+7 (999) 444-55-66',
            'role' => 'Investor',
            'organization_name' => null,
            'password' => bcrypt('aaaaaaaa'),
        ]);

        $investor1 = User::create([
            'name' => 'Анна',
            'middlename' => 'Петровна',
            'lastname' => 'Волкова',
            'email' => 'volkova.fund@gmail.com',
            'login' => 'volkova',
            'number' => '+7 (917) 123-45-67',
            'role' => 'Investor',
            'organization_name' => 'Инвестиционный фонд "УралКапитал"',
            'password' => bcrypt('aaaaaaaa'),
        ]);

        $investor2 = User::create([
            'name' => 'Сергей',
            'middlename' => 'Викторович',
            'lastname' => 'Лебедев',
            'email' => 'lebedev.business@mail.ru',
            'login' => 'lebedev',
            'number' => '+7 (950) 987-65-43',
            'role' => 'Investor',
            'organization_name' => null,
            'password' => bcrypt('aaaaaaaa'),
        ]);

        $admin = User::create([
            'name' => 'Александр',
            'middlename' => 'Игоревич',
            'lastname' => 'Зайцев',
            'email' => 'admin@investregion174.ru',
            'login' => 'admin',
            'number' => '+7 (951) 000-11-22',
            'role' => 'Admin',
            'organization_name' => null,
            'password' => bcrypt('admin123'),
        ]);



        $projectsData = [
            [
                'title' => 'Строительство лыжероллерной трассы в Центральном районе города Челябинска',
                'short_description' => 'Современная лыжероллерная трасса для круглогодичных тренировок',
                'full_description' => 'Строительство лыжероллерной трассы протяженностью 1250 метров в Центральном районе города Челябинска для проведения тренировок и соревнований.',
                'category' => json_encode(['Спорт', 'Инфраструктура']),
                'status' => 'Одобрен',
                'total_investment' => 180000000,
                'collected_total_investment' => 45000000,
                'number_date_realise' => 36,
                'ownership' => 'Муниципальная собственность',
                'activity' => 'Спортивная инфраструктура',
                'type_build' => 'Новое строительство',
                'count_new_job' => 15,
                'latitude' => 55.14786,
                'longitude' => 61.270627,
                'address' => 'Челябинская область, г. Челябинск, ул. Усадебная, з/у 8А',
            ],
            [
                'title' => 'IT-квартал «1736 Квартал создателей»',
                'short_description' => 'Создание современного IT-кластера в центре Челябинска',
                'full_description' => 'Центр притяжения резидентов высокотехнологичных отраслей, IT-компаний, стартапов и креативных индустрий.',
                'category' => json_encode(['ИТ', 'Технопарк', 'Инновации']),
                'status' => 'Одобрен',
                'total_investment' => 418300000,
                'collected_total_investment' => 91900000,
                'number_date_realise' => 132,
                'ownership' => 'ООО «Управляющая компания «ФишерГрупп»',
                'activity' => 'Информационные технологии',
                'type_build' => 'Новое строительство',
                'count_new_job' => 500,
                'latitude' => 55.167781,
                'longitude' => 61.415876,
                'address' => 'Челябинская область, г. Челябинск улицы: Труда, Российской, Карла Маркса и Красноармейской в Центральном районе Челябинска',
            ],
            [
                'title' => 'Строительство школы в микрорайоне №48 Курчатовского района',
                'short_description' => 'Современная школа на 1100 учащихся',
                'full_description' => 'Строительство общеобразовательной школы с спортивными залами, лабораториями и современной инфраструктурой.',
                'category' => json_encode(['Образование']),
                'status' => 'Одобрен',
                'total_investment' => 950000000,
                'collected_total_investment' => 300000000,
                'number_date_realise' => 96,
                'ownership' => 'Муниципальная собственность',
                'activity' => 'Образование',
                'type_build' => 'Новое строительство',
                'count_new_job' => 80,
                'latitude' => 55.205697,
                'longitude' => 61.284344,
                'address' => 'Челябинская область, г. Челябинск, Курчатовский район улицы: Бейвеля, Маршала Чуйкова, Хариса Юсупова и Мусы Джалиля',
            ],
            [
                'title' => 'Отель «МЕЛИОТ»',
                'short_description' => 'Четырехзвездочный отель в центре Челябинска',
                'full_description' => 'Отель на 150 номеров с рестораном, конференц-залами, SPA и фитнес-центром.',
                'category' => json_encode(['Туризм', 'Гостиничный бизнес']),
                'status' => 'Одобрен',
                'total_investment' => 680000000,
                'collected_total_investment' => 95000000,
                'number_date_realise' => 60,
                'ownership' => 'ООО СК «МАГИСТР»',
                'activity' => 'Гостиничный бизнес',
                'type_build' => 'Новое строительство',
                'count_new_job' => 50,
                'latitude' => 55.176038,
                'longitude' => 61.288755,
                'address' => 'Челябинская область, г. Челябинск, ул. Салавата Юлаева, 17Г',
            ],
            [
                'title' => 'Крытый футбольный манеж по ул. Университетская Набережная',
                'short_description' => 'Крытый футбольный манеж с искусственным покрытием',
                'full_description' => 'Строительство крытого футбольного манежа для круглогодичных тренировок и соревнований.',
                'category' => json_encode(['Спорт']),
                'status' => 'На модерации',
                'total_investment' => 450000000,
                'collected_total_investment' => 80000000,
                'number_date_realise' => 84,
                'ownership' => 'Муниципальная собственность',
                'activity' => 'Спортивная инфраструктура',
                'type_build' => 'Новое строительство',
                'count_new_job' => 30,
                'latitude' => 55.173092,
                'longitude' => 61.333015,
                'address' => 'Челябинская область, г. Челябинск, Калининский район',
            ],
            [
                'title' => 'Спортивно-зрелищный комплекс «РМК-Арена»',
                'short_description' => 'Крупный спортивно-зрелищный комплекс',
                'full_description' => 'Многофункциональная арена для единоборств, игровых видов спорта и массовых мероприятий.',
                'category' => json_encode(['Спорт']),
                'status' => 'Одобрен',
                'total_investment' => 5000000000,
                'collected_total_investment' => 1500000000,
                'number_date_realise' => 60,
                'ownership' => 'ООО «РМК-Арена»',
                'activity' => 'Спорт',
                'type_build' => 'Новое строительство',
                'count_new_job' => 363,
                'latitude' => 55.164947,
                'longitude' => 61.30435,
                'address' => 'Челябинская область, г. Челябинск, ул. Университетская наб., 127,',
            ],
            [
                'title' => 'Проект «Притяжение»',
                'short_description' => 'Общественно-культурное пространство',
                'full_description' => 'Создание современного парка с арт-объектами, спортивными и детскими площадками.',
                'category' => json_encode(['Культура', 'Благоустройство']),
                'status' => 'Одобрен',
                'total_investment' => 1512000000,
                'collected_total_investment' => 500000000,
                'number_date_realise' => 108,
                'ownership' => 'ООО «Территория Притяжения»',
                'activity' => 'Культура и отдых',
                'type_build' => 'Новое строительство',
                'count_new_job' => 20,
                'latitude' => 53.382238,
                'longitude' => 58.960349,
                'address' => 'Челябинская область, г. Магнитогорск, улица Советская, 132 ',
            ],
            [
                'title' => 'Строительство Теннисного центра в городе Челябинске',
                'short_description' => 'Современный теннисный центр',
                'full_description' => 'Теннисный центр с крытыми и открытыми кортами.',
                'category' => json_encode(['Спорт']),
                'status' => 'На модерации',
                'total_investment' => 480000000,
                'collected_total_investment' => 80000000,
                'number_date_realise' => 72,
                'ownership' => 'ООО «Мистраль»',
                'activity' => 'Спорт',
                'type_build' => 'Новое строительство',
                'count_new_job' => 35,
                'latitude' => 55.163477,
                'longitude' => 61.294091,
                'address' => 'Челябинская область,  г. Челябинск, Центральный и Калининский районы, пересечение ул. Университетская Набережная и ул. Академика Королева.',
            ],
            [
                'title' => 'Развитие индустриального парка «Малая Сосновка»',
                'short_description' => 'Развитие инфраструктуры индустриального парка',
                'full_description' => 'Строительство новых производственных корпусов и инженерной инфраструктуры.',
                'category' => json_encode(['Промышленность', 'Инфраструктура']),
                'status' => 'Одобрен',
                'total_investment' => 1400000000,
                'collected_total_investment' => 350000000,
                'number_date_realise' => 120,
                'ownership' => 'АО «Корпорация развития Челябинской области»',
                'activity' => 'Промышленность',
                'type_build' => 'Расширение',
                'count_new_job' => 150,
                'latitude' => 55.060518,
                'longitude' => 61.287354,
                'address' => 'Челябинская область, Сосновский муниципальный округ, посёлок Малая Сосновка, Лучистая улица',
            ],
            [
                'title' => 'Школа единоборств С.А. Ковалева «КРАШЕР»',
                'short_description' => 'Центр единоборств высокого уровня',
                'full_description' => 'Современный спортивный комплекс для подготовки спортсменов.',
                'category' => json_encode(['Спорт']),
                'status' => 'Одобрен',
                'total_investment' => 950000000,
                'collected_total_investment' => 200000000,
                'number_date_realise' => 108,
                'ownership' => 'ООО «Спортивный комплекс С. Ковалева «КРАШЕР»',
                'activity' => 'Спорт',
                'type_build' => 'Новое строительство',
                'count_new_job' => 27,
                'latitude' => 55.166439,
                'longitude' => 61.379323,
                'address' => 'Челябинская область, г. Челябинск, улица Энгельса, 20',
            ],
            [
                'title' => 'Фанпарк «Привилегия»',
                'short_description' => 'Горнолыжный и развлекательный парк',
                'full_description' => 'Круглогодичный курорт с горнолыжными трассами и гостиничным комплексом.',
                'category' => json_encode(['Туризм', 'Развлечения']),
                'status' => 'Одобрен',
                'total_investment' => 7721800000,
                'collected_total_investment' => 1200000000,
                'number_date_realise' => 108,
                'ownership' => 'ООО «Специализированный застройщик «ФлайПлэнингПартнерс»',
                'activity' => 'Туризм',
                'type_build' => 'Новое строительство',
                'count_new_job' => 132,
                'latitude' => 55.105712,
                'longitude' => 61.250765,
                'address' => 'Челябинская область, Челябинск, посёлок Западный, микрорайон Привилегия, Уютная улица, 7А',
            ],
            [
                'title' => 'Строительство термального комплекса на озере Смолино',
                'short_description' => 'Термальный комплекс с зонами отдыха',
                'full_description' => 'Термальный комплекс с бассейнами и общественным питанием на набережной озера Смолино.',
                'category' => json_encode(['Туризм']),
                'status' => 'Одобрен',
                'total_investment' => 350000000,
                'collected_total_investment' => 50000000,
                'number_date_realise' => 48,
                'ownership' => 'ООО «Империя игр»',
                'activity' => 'Туризм',
                'type_build' => 'Новое строительство',
                'count_new_job' => 40,
                'latitude' => 55.113395,
                'longitude' => 61.458402,
                'address' => 'Челябинская область, г. Челябинск, ул. Новороссийская',
            ],
            [
                'title' => 'Завод по производству автоклавного газобетона',
                'short_description' => 'Производство строительных материалов',
                'full_description' => 'Завод мощностью 500 000 куб.м газобетона в год.',
                'category' => json_encode(['Производство']),
                'status' => 'На модерации',
                'total_investment' => 890000000,
                'collected_total_investment' => 120000000,
                'number_date_realise' => 24,
                'ownership' => 'ООО «НКТ-ПРОЕКТ»',
                'activity' => 'Производство',
                'type_build' => 'Новое строительство',
                'count_new_job' => 60,
                'latitude' => 54.790521,
                'longitude' => 61.231676,
                'address' => 'Челябинская область, Еманжелинский муниципальный округ, рабочий посёлок Зауральский, улица Труда, 1А',
            ],
            [
                'title' => 'Строительство общественно-делового центра «Конгресс-холл»',
                'short_description' => 'Многофункциональный деловой центр',
                'full_description' => 'Общественно-деловой центр с конференц-залами и офисами.',
                'category' => json_encode(['Бизнес', 'Инфраструктура']),
                'status' => 'Одобрен',
                'total_investment' => 6800000000,
                'collected_total_investment' => 1500000000,
                'number_date_realise' => 120,
                'ownership' => 'Хозяйственное партнерство «Конгресс-холл»',
                'activity' => 'Бизнес',
                'type_build' => 'Новое строительство',
                'count_new_job' => 250,
                'latitude' => 55.178583,
                'longitude' => 61.348987,
                'address' => 'Челябинская область, г. Челябинск, улица Братьев Кашириных',
            ],
            [
                'title' => 'Создание производства мебельного щита и высокотехнологичных изделий из древесины',
                'short_description' => 'Деревообрабатывающее производство',
                'full_description' => 'Производство мебельного щита и инновационных изделий из древесины.',
                'category' => json_encode(['Производство', 'Деревообработка']),
                'status' => 'Одобрен',
                'total_investment' => 129000000,
                'collected_total_investment' => 25000000,
                'number_date_realise' => 48,
                'ownership' => 'ООО «ЧООК»',
                'activity' => 'Производство',
                'type_build' => 'Новое строительство',
                'count_new_job' => 50,
                'latitude' => 55.159866,
                'longitude' => 61.398044,
                'address' => 'Челябинская область, Челябинск, проспект Ленина, 57',
            ],
            [
                'title' => 'Центр по хранению и переработке овощей',
                'short_description' => 'Современный агропромышленный комплекс',
                'full_description' => 'Строительство центра хранения и переработки овощей.',
                'category' => json_encode(['АПК', 'Производство']),
                'status' => 'Одобрен',
                'total_investment' => 590200000,
                'collected_total_investment' => 150000000,
                'number_date_realise' => 84,
                'ownership' => 'ООО «Завод СпецАгрегат»',
                'activity' => 'Агропромышленный комплекс',
                'type_build' => 'Новое строительство',
                'count_new_job' => 89,
                'latitude' => 55.243028,
                'longitude' => 61.422433,
                'address' => 'Челябинск, Хлебозаводская улица, 33Ж',
            ],
            [
                'title' => 'Расширение комплекса по производству готовых лекарственных средств «Материа Медика Холдинг»',
                'short_description' => 'Расширение фармацевтического производства',
                'full_description' => 'Расширение производства лекарственных средств.',
                'category' => json_encode(['Фармацевтика']),
                'status' => 'Одобрен',
                'total_investment' => 800000000,
                'collected_total_investment' => 200000000,
                'number_date_realise' => 96,
                'ownership' => 'ООО «НПФ «Материа Медика Холдинг»',
                'activity' => 'Фармацевтика',
                'type_build' => 'Расширение',
                'count_new_job' => 27,
                'latitude' => 55.105985,
                'longitude' => 61.493652,
                'address' => 'Челябинская область, г. Челябинск, Бугурусланская улица, 54',
            ],
            [
                'title' => 'Завод по производству керамических пропантов',
                'short_description' => 'Производство керамических пропантов',
                'full_description' => 'Вторая очередь завода мощностью 50 000 тонн в год.',
                'category' => json_encode(['Промышленность']),
                'status' => 'Одобрен',
                'total_investment' => 1329200000,
                'collected_total_investment' => 300000000,
                'number_date_realise' => 96,
                'ownership' => 'ООО «ВеллПроп»',
                'activity' => 'Производство',
                'type_build' => 'Новое строительство',
                'count_new_job' => 83,
                'latitude' => 54.968933,
                'longitude' => 61.488208,
                'address' => 'Челябинская область, Копейск, Еткульский тракт, 11',
            ],
            [
                'title' => 'Разработка золоторудного месторождения «Курасан»',
                'short_description' => 'Освоение золоторудного месторождения',
                'full_description' => 'Разработка месторождения открытым способом.',
                'category' => json_encode(['Горнодобыча']),
                'status' => 'Одобрен',
                'total_investment' => 16438700000,
                'collected_total_investment' => 4000000000,
                'number_date_realise' => 168,
                'ownership' => 'АО «Южуралзолото Группа Компаний»',
                'activity' => 'Горнодобыча',
                'type_build' => 'Разработка месторождения',
                'count_new_job' => 400,
                'latitude' => 53.872603,
                'longitude' => 59.204071,
                'address' => 'Челябинская область, Верхнеуральск, улица Ерёмина, 43',
            ],
            [
                'title' => 'Транспортно-логистический комплекс «Южноуральский» (2 этап)',
                'short_description' => 'Развитие логистического комплекса',
                'full_description' => 'Второй этап транспортно-логистического комплекса.',
                'category' => json_encode(['Логистика']),
                'status' => 'Одобрен',
                'total_investment' => 329700000,
                'collected_total_investment' => 100000000,
                'number_date_realise' => 120,
                'ownership' => 'ПАО «Государственная транспортная лизинговая компания»',
                'activity' => 'Логистика',
                'type_build' => 'Расширение',
                'count_new_job' => 32,
                'latitude' => 54.515497,
                'longitude' => 61.299391,
                'address' => 'Челябинская область, Увельский муниципальный округ, посёлок железнодорожная станция Формачёво, Интернациональная улица, 1',
            ],

            // Продолжение 21–40
            [
                'title' => 'Техническое перевооружение АО «Трубодеталь»',
                'short_description' => 'Модернизация производства',
                'full_description' => 'Техническое перевооружение производства трубодеталей.',
                'category' => json_encode(['Промышленность']),
                'status' => 'Одобрен',
                'total_investment' => 2596200000,
                'collected_total_investment' => 800000000,
                'number_date_realise' => 120,
                'ownership' => 'АО «Трубодеталь»',
                'activity' => 'Металлообработка',
                'type_build' => 'Реконструкция',
                'count_new_job' => 100,
                'latitude' => 55.04417,
                'longitude' => 61.376386,
                'address' => 'Челябинская область, Челябинск, Челябинская улица, 23',
            ],
            [
                'title' => 'Строительство объекта заправки компримированным природным газом',
                'short_description' => 'АГНКС в г. Челябинске',
                'full_description' => 'Строительство станции заправки транспортных средств газом.',
                'category' => json_encode(['Энергетика', 'Инфраструктура']),
                'status' => 'Одобрен',
                'total_investment' => 109600000,
                'collected_total_investment' => 30000000,
                'number_date_realise' => 72,
                'ownership' => 'ООО «НоминалОйл»',
                'activity' => 'Топливная инфраструктура',
                'type_build' => 'Новое строительство',
                'count_new_job' => 14,
                'latitude' => 54.892493,
                'longitude' => 61.393049,
                'address' => 'Челябинская область, Коркино, площадь Ленина',
            ],
            [
                'title' => 'Расширение и модернизация производства изделий из вторичного сырья «СимВОЛ»',
                'short_description' => 'Производство материалов для дорожного строительства',
                'full_description' => 'Расширение производства изделий из вторичного сырья для дорожного строительства и городской среды.',
                'category' => json_encode(['Экология', 'Производство']),
                'status' => 'Одобрен',
                'total_investment' => 173800000,
                'collected_total_investment' => 50000000,
                'number_date_realise' => 48,
                'ownership' => 'ООО «СимВОЛ»',
                'activity' => 'Переработка отходов',
                'type_build' => 'Расширение',
                'count_new_job' => 24,
                'latitude' => 55.193211,
                'longitude' => 61.44587,
                'address' => 'Челябинская область, г. Челябинск, Сормовская улица, 13',
            ],
            [
                'title' => 'Комплекс по производству сульфида натрия «Доминат»',
                'short_description' => 'Производство химической продукции',
                'full_description' => 'Строительство комплекса по производству сульфида натрия из сульфата натрия.',
                'category' => json_encode(['Химическая промышленность']),
                'status' => 'Одобрен',
                'total_investment' => 2174700000,
                'collected_total_investment' => 600000000,
                'number_date_realise' => 60,
                'ownership' => 'ООО «Доминат»',
                'activity' => 'Химическое производство',
                'type_build' => 'Новое строительство',
                'count_new_job' => 79,
                'latitude' => 55.205728,
                'longitude' => 61.38253,
                'address' => 'Челябинская область, Челябинск, Свердловский тракт, 24',
            ],
            [
                'title' => 'Строительство автомобильной газонаполнительной компрессорной станции в Усть-Катаве',
                'short_description' => 'АГНКС в Усть-Катаве',
                'full_description' => 'Строительство станции заправки компримированным природным газом.',
                'category' => json_encode(['Инфраструктура', 'Энергетика']),
                'status' => 'Одобрен',
                'total_investment' => 174600000,
                'collected_total_investment' => 40000000,
                'number_date_realise' => 48,
                'ownership' => 'ООО «Газпром газомоторное топливо»',
                'activity' => 'Топливная инфраструктура',
                'type_build' => 'Новое строительство',
                'count_new_job' => 12,
                'latitude' => 54.919451,
                'longitude' => 58.13689,
                'address' => 'Челябинская область, Усть-Катав, Автодорожная улица',
            ],
            [
                'title' => 'Завод по производству стеклодеталей изоляторов',
                'short_description' => 'Производство закаленного стекла для изоляторов',
                'full_description' => 'Создание завода по производству стеклодеталей изоляторов из закаленного стекла.',
                'category' => json_encode(['Производство', 'Энергетика']),
                'status' => 'Одобрен',
                'total_investment' => 500700000,
                'collected_total_investment' => 150000000,
                'number_date_realise' => 60,
                'ownership' => 'АО «Завод стеклянных изоляторов»',
                'activity' => 'Производство',
                'type_build' => 'Новое строительство',
                'count_new_job' => 60,
                'latitude' => 54.452444,
                'longitude' => 61.267142,
                'address' => 'Челябинская область, Южноуральск, улица Строителей, 1Б',
            ],
            [
                'title' => 'Реконструкция очистных сооружений правого берега Магнитогорска',
                'short_description' => 'Модернизация очистных сооружений',
                'full_description' => 'Реконструкция объектов городской инфраструктуры — очистные сооружения правого берега (1-4 этапы).',
                'category' => json_encode(['Экология', 'Инфраструктура']),
                'status' => 'Одобрен',
                'total_investment' => 735600000,
                'collected_total_investment' => 200000000,
                'number_date_realise' => 120,
                'ownership' => 'МП трест «Водоканал» г. Магнитогорск',
                'activity' => 'Коммунальная инфраструктура',
                'type_build' => 'Реконструкция',
                'count_new_job' => 50,
                'latitude' => 53.309268,
                'longitude' => 59.002623,
                'address' => 'Челябинская область, Магнитогорск, улица Очистные сооружения, 1А',
            ],
            [
                'title' => 'Строительство социальной инфраструктуры «Белый хутор»',
                'short_description' => 'Социальная инфраструктура загородного квартала',
                'full_description' => 'Строительство социальной инфраструктуры для второго этапа комплексного освоения «Белый хутор».',
                'category' => json_encode(['ЖКХ', 'Инфраструктура']),
                'status' => 'Одобрен',
                'total_investment' => 1828300000,
                'collected_total_investment' => 500000000,
                'number_date_realise' => 168,
                'ownership' => 'АО «ЮжноУральская Корпорация жилищного строительства»',
                'activity' => 'Жилищное строительство',
                'type_build' => 'Новое строительство',
                'count_new_job' => 322,
                'latitude' => 55.103719,
                'longitude' => 61.251798,
                'address' => 'Челябинск, посёлок Западный, микрорайон Белый Хутор, Светлая улица, 2',
            ],
            [
                'title' => 'Административно-производственный комплекс «Челябторгтехника»',
                'short_description' => 'Современный административно-производственный комплекс',
                'full_description' => 'Строительство современного административно-производственного комплекса.',
                'category' => json_encode(['Производство']),
                'status' => 'Одобрен',
                'total_investment' => 243100000,
                'collected_total_investment' => 80000000,
                'number_date_realise' => 60,
                'ownership' => 'ООО «Завод «Челябторгтехника»',
                'activity' => 'Производство',
                'type_build' => 'Новое строительство',
                'count_new_job' => 57,
                'latitude' => 55.162541,
                'longitude' => 61.474661,
                'address' => 'Челябинская область, г. Челябинск, проспект Ленина, 2В',
            ],
            [
                'title' => 'Многофункциональное административно-диспетчерское здание «Транснефть-Урал»',
                'short_description' => 'Административно-диспетчерское здание',
                'full_description' => 'Строительство административно-диспетчерского здания управления магистральным трубопроводным транспортом.',
                'category' => json_encode(['Инфраструктура']),
                'status' => 'Одобрен',
                'total_investment' => 2975600000,
                'collected_total_investment' => 800000000,
                'number_date_realise' => 60,
                'ownership' => 'АО «Транснефть-Урал»',
                'activity' => 'Трубопроводный транспорт',
                'type_build' => 'Новое строительство',
                'count_new_job' => 36,
                'latitude' => 55.137286,
                'longitude' => 61.430365,
                'address' => 'Челябинская область, г. Челябинск, улица Барбюса, 120',
            ],
            [
                'title' => 'Развитие Нижнего лагеря Музея-Заповедника «Аркаим»',
                'short_description' => 'Развитие туристической инфраструктуры Аркаима',
                'full_description' => 'Строительство инфраструктуры Нижнего лагеря музея-заповедника «Аркаим».',
                'category' => json_encode(['Туризм', 'Культура']),
                'status' => 'Одобрен',
                'total_investment' => 319700000,
                'collected_total_investment' => 80000000,
                'number_date_realise' => 48,
                'ownership' => 'АНО «Центр проектного развития территорий и туризма»',
                'activity' => 'Туризм',
                'type_build' => 'Новое строительство',
                'count_new_job' => 36,
                'latitude' => 52.642811,
                'longitude' => 59.542026,
                'address' => 'Челябинская область, Брединский муниципальный округ, посёлок Аркаим, Музейная улица, 5',
            ],
            [
                'title' => 'Создание производства перспективного модельного ряда мостов и осей «УРАЛ»',
                'short_description' => 'Производство автокомпонентов для автомобилей УРАЛ',
                'full_description' => 'Разработка и освоение производства ведущих мостов и передних осей для автомобилей УРАЛ.',
                'category' => json_encode(['Машиностроение']),
                'status' => 'Одобрен',
                'total_investment' => 12851000000,
                'collected_total_investment' => 3000000000,
                'number_date_realise' => 144,
                'ownership' => 'АО «Автомобильный завод «УРАЛ»',
                'activity' => 'Машиностроение',
                'type_build' => 'Расширение',
                'count_new_job' => 291,
                'latitude' => 55.071169,
                'longitude' => 60.100607,
                'address' => 'Челябинская область, Миасс, Динамовское шоссе, 2А',
            ],
            [
                'title' => 'Транспортно-логистический центр «Троицкий»',
                'short_description' => 'Новый логистический центр',
                'full_description' => 'Строительство современного транспортно-логистического центра.',
                'category' => json_encode(['Логистика']),
                'status' => 'Одобрен',
                'total_investment' => 952500000,
                'collected_total_investment' => 200000000,
                'number_date_realise' => 36,
                'ownership' => 'ООО «Стратегия»',
                'activity' => 'Логистика',
                'type_build' => 'Новое строительство',
                'count_new_job' => 75,
                'latitude' => 54.067696,
                'longitude' => 61.561403,
                'address' => 'Челябинская область, Троицк, Заводская улица, 6',
            ],
            [
                'title' => 'Центр семейного отдыха «Городские термы «Тортуга»',
                'short_description' => 'Семейный термальный комплекс',
                'full_description' => 'Новая очередь центра семейного отдыха с термальными источниками.',
                'category' => json_encode(['Туризм']),
                'status' => 'Одобрен',
                'total_investment' => 103200000,
                'collected_total_investment' => 30000000,
                'number_date_realise' => 48,
                'ownership' => 'ООО «Стоп-Т»',
                'activity' => 'Туризм',
                'type_build' => 'Новое строительство',
                'count_new_job' => 29,
                'latitude' => 55.14253,
                'longitude' => 61.429386,
                'address' => 'Челябинская область, г. Челябинск, улица Руставели, 30А',
            ],
            [
                'title' => 'Создание новой упаковочной тары «ПОЛИХИМТЕХНОЛОГИИ»',
                'short_description' => 'Расширение производства упаковки',
                'full_description' => 'Создание новой упаковочной тары и расширение действующего производства.',
                'category' => json_encode(['Производство', 'Упаковка']),
                'status' => 'Одобрен',
                'total_investment' => 350800000,
                'collected_total_investment' => 100000000,
                'number_date_realise' => 48,
                'ownership' => 'ООО «ПОЛИХИМТЕХНОЛОГИИ»',
                'activity' => 'Производство',
                'type_build' => 'Расширение',
                'count_new_job' => 29,
                'latitude' => 55.118168,
                'longitude' => 61.539547,
                'address' => 'Челябинская область, Челябинск, Копейское шоссе, 5П',
            ],
            [
                'title' => 'Строительство универсальных складов класса «А»',
                'short_description' => 'Современные складские комплексы',
                'full_description' => 'Строительство универсальных складов продовольственных и непродовольственных товаров класса А.',
                'category' => json_encode(['Логистика', 'Склады']),
                'status' => 'Одобрен',
                'total_investment' => 18098000000,
                'collected_total_investment' => 5000000000,
                'number_date_realise' => 24,
                'ownership' => 'ООО «ПФО Девелопмент»',
                'activity' => 'Логистика',
                'type_build' => 'Новое строительство',
                'count_new_job' => 8629, // крупный проект
                'latitude' => 55.010251,
                'longitude' => 61.482935,
                'address' => 'Челябинская область, Копейск, Логопарковая улица, 1',
            ],
            [
                'title' => 'Физкультурно-оздоровительный комплекс в г. Бакал',
                'short_description' => 'ФОК с универсальным игровым залом',
                'full_description' => 'Строительство физкультурно-оздоровительного комплекса.',
                'category' => json_encode(['Спорт']),
                'status' => 'Одобрен',
                'total_investment' => 200000000,
                'collected_total_investment' => 50000000,
                'number_date_realise' => 36,
                'ownership' => 'ООО «Мистраль»',
                'activity' => 'Спорт',
                'type_build' => 'Новое строительство',
                'count_new_job' => 10,
                'latitude' => 54.941627,
                'longitude' => 58.808093,
                'address' => 'Челябинская область, Саткинский муниципальный округ, Бакал, улица 50 лет ВЛКСМ, 2',
            ],
            // Добавь ещё 5–6 проектов по аналогии, если нужно ровно 40
        ];

        $organizers = [$organisator, $organisator1, $organisator2]; 

        $createdProjects = [];

        foreach ($projectsData as $index => $data) {
            $organizer = $organizers[$index % count($organizers)]; 

            $data['user_id'] = $organizer->id;
            $data['is_moderated'] = true;

            $project = Project::create($data);
            $createdProjects[] = $project;
        }


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
        ];

        foreach ($createdProjects as $project) {
            $remaining = (int)($project->total_investment * 0.97);
            $numItems = rand(4, 7);
            $usedNames = [];

            for ($i = 0; $i < $numItems; $i++) {
                do {
                    $itemName = $investmentNames[array_rand($investmentNames)];
                } while (in_array($itemName, $usedNames));

                $usedNames[] = $itemName;

                $amount = ($i === $numItems - 1) 
                    ? $remaining 
                    : rand((int)($remaining * 0.12), (int)($remaining * 0.28));

                Project_investment::create([
                    'project_id' => $project->id,
                    'item_name' => $itemName,
                    'amount' => $amount,
                ]);

                $remaining -= $amount;
                if ($remaining < 10_000_000) break;
            }
        }


        $currentYear = (int)date('Y');

        foreach ($createdProjects as $project) {
            $baseRevenue = (int)($project->total_investment * 0.17); 
            $margin = 0.13;

            for ($i = 0; $i < 5; $i++) {
                $year = $currentYear + $i;
                $growth = 1 + (0.14 * $i); // ежегодный рост

                $expectedRevenue = (int)($baseRevenue * $growth);
                $expectedExpenses = (int)($expectedRevenue * (1 - $margin));

                $margin = min(0.38, $margin + 0.04); // маржа растёт

                Project_forecast::create([
                    'project_id' => $project->id,
                    'year' => $year,
                    'expected_revenue' => $expectedRevenue,
                    'expected_expenses' => $expectedExpenses,
                ]);
            }
        }


        $favoriteIndexes = [0, 2, 4, 6, 8, 11, 15, 19, 23, 27, 31, 35, 38];

        foreach ($favoriteIndexes as $idx) {
            if (isset($createdProjects[$idx])) {
                Favorite::create([
                    'user_id' => $investor->id,
                    'project_id' => $createdProjects[$idx]->id,
                ]);
            }
        }


        $favoriteIndexes2 = [1, 5, 9, 12, 18, 25, 30];
        foreach ($favoriteIndexes2 as $idx) {
            if (isset($createdProjects[$idx])) {
                Favorite::create([
                    'user_id' => $investor2->id,  
                    'project_id' => $createdProjects[$idx]->id,
                ]);
            }
        }
    }
}