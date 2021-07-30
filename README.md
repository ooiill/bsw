
- 拉取项目

    ```bash
    git clone https://git.com/bsw-app.git
    ```

- 安装 `bsw` 命令

    ```bash
    sudo curl -L "https://raw.githubusercontent.com/ooiill/bsw/master/install/bsw.sh" -o /usr/local/bin/bsw && sudo chmod a+x /usr/local/bin/bsw
    ```

- 查看所需依赖（如缺请手动安装）

    ```bash
    bsw -h
    ```

- 一键安装项目环境（耐心等待 `15分钟`）

    ```bash
    bsw --app bsw-app --type bsw-type -install
    ```

- **问题A** Docker pull 较慢的情况下

    ```
    vim /etc/docker/daemon.json
  
    # 添加阿里源
    {
      "registry-mirrors": ["https://9cpn8tt6.mirror.aliyuncs.com"]
    }
  
    # 重启服务
    systemctl daemon-reload
    systemctl restart docker
    ```
  
- **问题B** Docker apt-get update 联网失败

    ```
    vim /etc/docker/daemon.json
  
    # 添加 dns 服务器
    {
      "dns": ["8.8.8.8", "114.114.114.114"]
    }
    
    # 重启服务
    systemctl daemon-reload
    systemctl restart docker
    ```

- 文档地址 [https://ooiill.cn](https://ooiill.cn)
