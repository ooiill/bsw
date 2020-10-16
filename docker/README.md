
### 环境安装步骤

1. 克隆项目。

    ```bash
    git clone https://github.com/ooiill/bsw.git bsw-app
    ```

    > 若果你还没有安装 `docker` 和 `docker-compose` 软件，请先安装软件。

2. 使用 [`DaoCloud`](https://www.daocloud.io/mirror) 为 `Docker` 加速。

    > 注册后可获取加速 `ID` 用替换以下备用 `ID`。

    ```bash

    curl -sSL https://get.daocloud.io/daotools/set_mirror.sh | sh -s http://{ID}.m.daocloud.io

    # 例子
    curl -sSL https://get.daocloud.io/daotools/set_mirror.sh | sh -s http://8dd58468.m.daocloud.io
    sudo service docker restart
    ```

3. 进入到相应项目目录。

    ```bash
    cd bsw-app/docker
    ```

4. 编译 `Docker` 并启动。

    ```bash
    sudo docker-compose up --build
    ```

5. 启动 `Docker` 并在后台运行。

    ```bash
    sudo docker-compose up -d

    # 备用命令.重启 `Nginx` 容器
    sudo docker-compose restart bsw-app-nginx
    ```

6. 部分备用命令。

    ```
    # 删除所有的镜像
    sudo docker rmi $(sudo docker images -q)

    # 删除 `untagged` 镜像
    sudo docker rmi $(sudo docker images | grep "^<none>" | awk "{print $3}")

    # 删除所有的容器
    sudo docker rm $(sudo docker ps -a -q)

    # 启动/停止/重启指定容器（例如 bsw-app-nginx 服务）
    sudo docker-compose start bsw-app-nginx
    sudo docker-compose stop bsw-app-nginx
    sudo docker-compose restart bsw-app-nginx
    ```

7. 安装提供的命令别名脚本。

    ```bash
    cd script
    chmod a+x *.sh
    # 逐一执行 ./install-xxx.sh 脚本
    source ~/.bash_profile
    ```

8. 安装项目依赖。

    ```bash
    bsw-app-composer install --ignore-platform-reqs
    cp .env.dist .env
    # 修改 `.env` 文件中的相关配置
    ```