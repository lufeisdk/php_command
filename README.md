# php_command
这是一个通过命令行执行PHP脚本的轻量级框架雏形，目前暂时还不支持数据库连接操作。
在项目目录下，可通过命令执行PHP脚本，解析命令行所输入的参数

举个栗子：

命令行执行：php index.php index index name=Lufei age:20 city.Hangzhou 1600790399

参数解析结果：
<pre>Array
(
    [name] => Lufei
    [age] => 20
    [city] => Hangzhou
    [0] => 1600790399
)

支持等号（=），冒号（:），点号（.）连接的键值对解析
