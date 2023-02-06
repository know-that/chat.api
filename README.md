#### 3、数据迁移并填充
```bash
$ php artisan migrate --path="database/migrations/create"
$ php artisan db:seed
```

#### supervisor
```angular2html
# 刷新配置文件
$ sudo supervisorctl reread

# 更新服务
$ sudo supervisorctl update

# 启动服务
$ sudo supervisorctl start chat-octane:*
$ sudo supervisorctl stop chat-websocket:*

# 服务状态
$ sudo supervisorctl status
```


```bash
# octane swoole 服务
[program:knowthat-octane]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /usr/share/nginx/html/chat/chat.api/artisan octane:start --workers=4 --task-workers=6 --max-requests=500
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=root
numprocs=1
redirect_stderr=true
stdout_logfile=/usr/share/nginx/html/chat/chat.api/storage/logs/supervisord/octane.log
stopwaitsecs=3600

# swoole websocket
[program:knowthat-octane]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /usr/share/nginx/html/chat/chat.api/artisan websocket:start
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=root
numprocs=1
redirect_stderr=true
stdout_logfile=/usr/share/nginx/html/chat/chat.api/storage/logs/supervisord/swoole-websocket.log
stopwaitsecs=3600
```
