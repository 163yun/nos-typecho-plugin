# Netease Object Storage For Typecho

网易云对象存储 Typecho 插件
##前置阅读
* [NOS快速入门](https://c.163.com/wiki/index.php?title=%E5%AF%B9%E8%B1%A1%E5%AD%98%E5%82%A8%E5%BF%AB%E9%80%9F%E5%85%A5%E9%97%A8)
* [NOS计费说明](https://c.163.com/wiki/index.php?title=%E5%AF%B9%E8%B1%A1%E5%AD%98%E5%82%A8%E4%BB%B7%E6%A0%BC%E4%B8%8E%E8%AE%A1%E8%B4%B9)
* [Access Key 获取](https://c.163.com/dashboard#/m/account/accesskey/)

## 使用说明
1. 下载文件
2. 解压到 typecho 目录下的 usr/plugin/Nos文件夹内。
3. 登陆到 typecho 的后台，启动插件，并设置 endPoint 、 Bucket 、Access Key 、Access Secret、保存路径等。
4. 新建文章上传即可。

## 注意事项
1. 保存路径的首位不能为 `/` ，保存路径设置为 `ccc/` 时，文件的完整路径为 domain.com/ccc/filename。

基于 Apache License 2.0开源
