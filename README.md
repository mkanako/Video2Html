# Video2Html

一个视频转html工具

## Demo
[http://mkanako.github.io/Video2Html/](http://mkanako.github.io/Video2Html/)

![img](http://mkanako.github.io/Video2Html/Screenshot.png)
## 原理

使用 ffmpeg 对视频截图，然后程序处理图片生成数据，压缩，分割成 n 个 zip 包，然后前端 js 边下载 zip 包，边解压，边渲染 DOM 

PS:Demo是3分钟的视频，整个页面所有的请求流量是7.2M，可以接受_(:з」∠)_ 

PS2：综合了播放帧率，数据量大小，截图是每秒4张，而且页面播放帧率也难以精确控制，所以播放的时候可能会有卡顿，闪屏，跟声音不同步等现象(＞﹏＜)

## 安装

下载本项目源代码到你本地

然后修改Video2Html.php

```php
$ffmpeg='';
```
为你的本机的ffmpeg路径

ffmpeg可在这里下载，自行选择相应的系统版本

[http://www.ffmpeg.org/download.html](http://www.ffmpeg.org/download.html)

## 使用

```sh
php -f Video2Html.php you/path/of/video.mp4
```

正常执行成功的话就会在视频的同级目录下生成html代码文件夹，然后需要通过HTTP服务器来进行查看，直接打开index.html是不行的

### 注意：php版本需要5.4以上



## Licence

MIT © [kanako]()