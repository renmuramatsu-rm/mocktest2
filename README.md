# 環境構築（mocktest2）
１．Dockerビルド
① git clone git@github.com:renmuramatsu-rm/mocktest2.git
② DockerDesktopアプリを立ち上げる ③ docker-compose up -d --build##

２．Laravel環境構築
① docker-compose exec php bash ② composer install ③「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成 ④ .envに以下の環境変数を追加

DB_CONNECTION=mysql DB_HOST=mysql DB_PORT=3306 DB_DATABASE=laravel_db DB_USERNAME=laravel_user DB_PASSWORD=laravel_pass

④ アプリケーションキーの作成

php artisan key:generate

⑤ マイグレーションの実行

php artisan migrate ⑥ シーディングの実行

php artisan db:seed

３．主要技術
言語・フレームワーク　　 　 MySQL：10.11.6 　php：8.4.2 　Laravel：11.44.2 　Docker：27.4.0 　PHPunit：11.5.12   mailtrap

４．メール認証
mailtrapというツールを使用しています。
以下のリンクから会員登録をしてください。　
https://mailtrap.io/

メールボックスのIntegrationsから 「laravel 9+」を選択し、　
.envファイルのMAIL_MAILERからMAIL_ENCRYPTIONまでの項目をコピー＆ペーストしてください。　
MAIL_FROM_ADDRESSは任意のメールアドレスを入力してください。

５．テスト用アカウント
【一般ユーザ】
name    : test
email   : test@gmail
password: password 

name    : test2
email   : test2@gmail
password: password

【管理者】
name    : admin
email   : example@test.com
password: password

６．PHPUnitを使用したテスト
//テスト用データベースの作成
docker-compose exec mysql bash
mysql -u root -p
//パスワードはrootと入力
create database test_database;

docker-compose exec php bash
php artisan migrate:fresh --env=testing
./vendor/bin/phpunit

７．URL
開発環境：http://localhost/ phpMyAdmin:：http://localhost:8080/

８．ER図

<img width="941" height="706" alt="image" src="https://github.com/user-attachments/assets/c8dce177-7d59-45fd-a0ce-645c0d51f258" />




