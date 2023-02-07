# chat.api
这是一个及时通信的后端项目  
环境要求 >= php8.1

## 主要功能
* [x] 好友申请
* [x] 用户搜索
* [x] 系统通知（非实时现在需要刷新页面，后续支持实时）
* [x] 及时聊天对话
* [x] 聊天表情发送

## 后续支持
* [ ] 基本信息修改
* [ ] 系统通知（实时提醒）
* [ ] gif 动图
* [ ] 表情收藏
* [ ] 附件发送
* [ ] 文件传输助手

## 项目方向
打造一个免费的客服 sass 系统，支持私有化部署

## 快速开始
```sh
# 依赖包安装
composer install

# 环境配置
cp .env.example .env

# 数据迁移填充
php artisan migrate --path="database/migrations/create"
php artisan db:seed

# octane 配置
php artisan octane:install
```

### 本地
```sh
npm install chokidar -D
php artisan optimize:clear
php artisan octane:start --watch
php artisan websocket:start
```

### 生产
```sh
php artisan optimize
php artisan octane:start
php artisan websocket:start
```

#### nginx 配置参考
```shell
server {
    listen      443 ssl http2;
    server_name chat.knowthat.cn;

    # ssl
    ssl_certificate_key /etc/letsencrypt/live/knowthat.cn/privkey.pem;
    ssl_certificate     /etc/letsencrypt/live/knowthat.cn/fullchain.pem;

    location / {
        root    /usr/share/nginx/html/chat/chat.web/dist;
        index   index.html index.htm;
        try_files $uri $uri/ /index.html;
    }

    # 代理到 Swoole server
    location /websocket {
        proxy_set_header client-real-ip $remote_addr;
        proxy_pass http://localhost:8001/websocket;
    }
}

server {
    listen       443 ssl http2;
    server_name  chat-websocket.knowthat.cn;

    # ssl
    ssl_certificate_key /etc/letsencrypt/live/chat.knowthat.cn/privkey.pem;
    ssl_certificate     /etc/letsencrypt/live/chat.knowthat.cn/fullchain.pem;

    # 重点，转发 websocket 需要的设置
    proxy_set_header X-Real_IP $remote_addr;
    proxy_set_header Host $host;
    proxy_set_header X_Forward_For $proxy_add_x_forwarded_for;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection 'upgrade';


    location / {
        # 注意，这里是 http 不是 ws
        proxy_pass http://127.0.0.1:9502/;
    }

    error_page   500 502 503 504  /50x.html;

    location = /50x.html {
        root   html;
    }
}
```

#### supervisor 命令参考
```angular2html
# 刷新配置文件
$ sudo supervisorctl reread

# 更新服务
$ sudo supervisorctl update

# 启动服务
$ sudo supervisorctl start chat-octane:*
$ sudo supervisorctl stop chat-octane:*

# 服务状态
$ sudo supervisorctl status
```

#### supervisor 进程管理脚本参考
```bash
# octane swoole 服务
[program:chat-octane]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /usr/share/nginx/html/chat/chat.api/artisan octane:start --port=8001 --workers=4 --task-workers=6 --max-requests=500
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
[program:chat-octane]
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
