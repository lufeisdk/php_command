# php_command
这是一个通过命令行执行PHP脚本的轻量级框架雏形，可以满足日常数据迁移使用的定时脚本编写。

目前支持数据库连接（包括mysql和SQL server两类），支持缓存（本地文件缓存和Redis缓存），支持本地日志文件记录。
在项目目录下，可通过命令执行PHP脚本，解析命令行所输入的参数



命令行执行说明：php [入口文件] [控制器类][类方法] [参数]

举个栗子：
php index.php index index name=Lufei age:20 city.Hangzhou 1600790399

<pre>
参数解析结果：
Array
(
    [name] => Lufei
    [age] => 20
    [city] => Hangzhou
    [0] => 1600790399
)

支持等号（=），冒号（:），点号（.）连接的键值对解析

