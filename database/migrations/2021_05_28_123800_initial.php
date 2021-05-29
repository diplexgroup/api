<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Initial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_log', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';

            $table->integer('id')->autoIncrement();
            $table->integer('projectId')->comment('Проект')->default(null)->nullable();
            $table->char('api', 100)->comment('ссылка АПИ');
            $table->text('request')->comment('Тело запроса');
            $table->text('response')->comment('Тело ответа');
            $table->dateTime('date')->comment('Время');
        });

        Schema::create('currency', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';

            $table->integer('id')->autoIncrement();
            $table->char('name', 40)->comment('Наименование валюты (ex: US Dollar)');
            $table->char('code', 5)->comment('Код валюты в ISO (префикс)');
            $table->integer('type')->comment('	1-фиат, 2 - крипто');
            $table->double('inDLX')->comment('Курс перевода в dlx');
        });

        Schema::create('modules', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';

            $table->integer('id')->autoIncrement();
            $table->char('name', 40);
            $table->char('link', 40);
            $table->string('readRoles', 255);
            $table->string('writeRoles', 255);
        });

        Schema::create('project', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';

            $table->integer('id')->autoIncrement();
            $table->char('name', 40);
            $table->char('api_endpont', 80);
            $table->char('api_front_link', 80);
            $table->integer('type')->default(1);
            $table->integer('addr_need_flag')->default(0);
//            $table->addColumn('integer', 'addr_need_flag', ['length' => 1, 'default' => 0]);
            $table->char('pref', 10)->index('index1');
            $table->text('description');
            $table->integer('status')->default(1);
            $table->char('token', 80)->index('search');
            $table->smallInteger('show_in_explorer')->default(1);
            $table->char('svg', 100)->nullable()->default('');
        });
        DB::statement('ALTER TABLE project MODIFY COLUMN addr_need_flag INTEGER (1) NOT NULL DEFAULT 0;');

        Schema::create('project_road', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';

            $table->integer('from_project')->index('fromProject');
            $table->foreign('from_project', $name='project_road_ibfk_1')->references('id')->on('project');
            $table->integer('to_project')->index('toProject');
            $table->foreign('to_project', $name='project_road_ibfk_2')->references('id')->on('project');
            $table->text('tax_strategy');
            $table->double('min_amount')->default(0);
            $table->double('max_amount')->default(0);
            $table->double('burn_percent')->default(0);
            $table->integer('status');
            $table->double('max_day_amount');
            $table->double('max_month_amount');
            $table->integer('id')->autoIncrement();

            $table->unique(['from_project','to_project','status'], $name='search2');
            $table->index(['from_project', 'to_project'], $name='search');

        });

        Schema::create('shareholder', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';

            $table->integer('id')->autoIncrement();
            $table->char('telegram', 50);
            $table->char('uid', 50);
            $table->char('user', 200);
            $table->char('sponsor_id', 50);
            $table->char('sponsor_user_name', 200);
            $table->integer('type')->default(2)->comment('1 - юзер, 2-акционер, 3-юзер-акционер');
        });

        Schema::create('transaction', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';

            $table->integer('id')->autoIncrement();
            $table->integer('type')->comment('	0 -Неизвестно, 1-Прямой перевод, 11-Списание с локального счёта отправителя, 12-Перевод с основного кошелька на транзитный, 13-Перевод с транзитного кошелька (комиссия), 14-Перевод с транзитного кошелька (сжигание), 15-Перевод с транзитного на транзитный получателя, 16-Перевод с транзитного на основной кошелёк, 17-Начисление на локальный кошелёк получателя');
            $table->char('tid', 39)->comment('id транзакции');
            $table->char('trid', 39)->comment('трансфер id')->default(null)->nullable();
            $table->dateTime('nextDate')->comment('время следующей попытки осуществить транзакцию в случае неудачи');
            $table->double('duration')->comment('время выполнения транзакции (длительность)');
            $table->dateTime('createdAt')->comment('время создания транзакции')->nullable()->default(null);
            $table->dateTime('updatedAt')->nullable()->default(null);
            $table->integer('retryCount');
            $table->integer('errorCode');
            $table->integer('status')->comment('1-Ожидает, 2-Открыта, 3-Успешна, 4 -Ошибка');
            $table->decimal('amount', 18, 6);
            $table->char('currency', 5);
            $table->text('data');
        });

        Schema::create('transfer', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';

            $table->integer('id')->autoIncrement();
            $table->char('trid', 39);
            $table->decimal('amount', 18, 6);
            $table->integer('type');
            $table->integer('fromProject');
            $table->integer('toProject');
            $table->char('fromAddress', 40);
            $table->char('toAddress', 40);
            $table->integer('errorCode');
            $table->integer('status')->comment('1-Открыт, 2-Упешно, 3-Ошибка');
            $table->integer('step');
            $table->dateTime('dateCreated');
            $table->dateTime('dateUpdated');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';

            $table->integer('id')->autoIncrement();
            $table->char('email', 80)->nullable()->default(null);
            $table->char('password', 80)->nullable()->default(null);
            $table->integer('confirm_code')->nullable()->default(0);
            $table->integer('confirm_attempts')->nullable()->default(0)->comment('Кол-во уже сделанных попыток с кодом входа');
            $table->integer('status')->nullable()->default(1)->comment('1 - активен, 2 - забанен');
            $table->string('roles', 255)->nullable()->default(null)->comment('список ролей через запятую');
            $table->timestamp('updated_at')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->char('fname', 20)->comment('имя')->nullable()->default(null);
            $table->char('sname', 40)->comment('фамилия')->nullable()->default(null);
            $table->char('tname', 20)->comment('отчество')->nullable()->default(null);
            $table->char('position', 60)->comment('Должность')->nullable()->default(null);
            $table->char('department', 40)->comment('Отдел')->nullable()->default(null);
            $table->char('ip', 23)->comment('IP (23 - макс длина v6)')->nullable()->default(null);
            $table->char('tg_nick', 80)->comment('Ник в Телеге')->nullable()->default(null);
        });

        Schema::create('user_role', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';

            $table->integer('id')->autoIncrement();
            $table->char('name', 40);
        });

        Schema::create('wallet', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';

            $table->integer('id')->autoIncrement();
            $table->integer('type')->comment('1 - внутренний. 2 - внешний');
            $table->integer('rootType')->comment('0 - не назначен, 1 - основной, 2 - транзакционный, 3- комиссионный, 4 - для сжигания');
            $table->integer('relationId')->nullable()->default(null)->comment('реляция к проекту, если есть');
            $table->char('addr', 40)->comment('адрес')->collation('utf8_unicode_ci')	;
            $table->char('pkey', 120)->nullable()->default(null)->comment('приватный ключ, если есть')->collation('utf8_unicode_ci')	;
            $table->integer('status')->comment('1 - актвиен, 2 - закрыт');
            $table->char('currency', 5)->comment('код валюты в ISO')->collation('utf8_unicode_ci')	;

            $table->index(['rootType', 'relationId'], $name='search');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_log');
        Schema::dropIfExists('currency');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('project');
        Schema::dropIfExists('project_road');
        Schema::dropIfExists('shareholder');
        Schema::dropIfExists('transaction');
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('wallet');
    }
}
