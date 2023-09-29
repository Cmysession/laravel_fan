#### 生成KEY php artisan key:generate
## 生成软链 php artisan storage:link |  ln -sr storage/app/public public/storage
## 自动生成拼音脚本 php artisan pyinyin
# NG
location / {
try_files $uri $uri/ /index.php?$query_string;
}