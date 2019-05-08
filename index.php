<?php
mysql_connect("localhost","","");
mysql_select_db("slot1");
echo "<head><title>澳科大新生翻墙回国申请</title></head>";
$action=$_GET["action"];
if ($action=="submit"){
	include 'header.htm';
	$qq=$_POST["qq"];
	$pt=$_POST["pt"];
	if ($qq=="" || $pt==""){
		echo "请检查您的填写，QQ和平台都为必填项";
		include 'footer.htm';
		exit();
	}
	$result=mysql_query("SELECT * FROM `gfw` WHERE `qq`='$qq'");
	$rows=mysql_fetch_array($result);
	if (mysql_num_rows($result)){
		echo "您已经申请过，请不要重复申请，谢谢合作！";
		include 'footer.htm';
		exit();
	}
	$time=date('y-m-d h:i:s',time());
	mysql_query("INSERT INTO gfw(`qq`,`pt`,`time`,`pass`,`install`,`port`,`password`) VALUES('$qq','$pt','$time','null','no','null','null')");
	echo "申请成功，您可以最迟在9月3日之前通过<a href='http://gfw.staveworld.com/?action=query'>这里</a>查询是否申请成功<br>申请成功系统会自动提供翻墙方法";	
	include 'footer.htm';
	exit();
}
if ($action=="query"){
	include 'header.htm';
	echo "
		<form name='form1' method='post' action='index.php?action=queryget'>
			<p>请输入你的申请QQ：</p>
			<label for='qq'></label>
		    <input type='text' name='qq' id='qq'>
		    <input type='submit' name='button' id='button' value='submit' />
		</form>
	";
	include 'footer.htm';
	exit();
}
if ($action=="queryget"){
	include 'header.htm';
	$qq=$_POST["qq"];
	$result=mysql_query("SELECT * FROM `gfw` WHERE `qq`='$qq'");
	$rows=mysql_fetch_array($result);
	if (!mysql_num_rows($result)){
		echo "没有查询到您的申请！";
		include 'footer.htm';
		exit();
	}
	if ($rows["pass"]=="null"){
		echo "您的申请结果暂未出来，请耐心等待，谢谢";
		include 'footer.htm';
		exit();
	}else if($rows["pass"]=="no"){
		echo "您的申请未通过，不过还有机会，待未来资源补充后将会优先为您提供服务";
	}else if($rows["pass"]=="yes"){
		echo "恭喜您申请成功，如果还未安装请等待管理员为您安装<br>安装状态:";
		if($rows["install"]=="no"){
			echo "暂未安装";
		}else if($rows["install"]=="yes"){
			echo "已经安装<br>";
			echo "您的翻墙端口为：".$rows["port"]."<br>";
			echo "翻墙登陆密码为：".$rows["password"]."<br>";
			echo "
				<p>翻墙使用方法</p>
				<p>Windows PC平台可以通过<a href='https://github.com/shadowsocksr-backup/shadowsocksr-csharp/releases/download/4.7.0/ShadowsocksR-4.7.0-win.7z'>这里</a>下载最新翻墙客户端</p>
				<p>Android平台可以通过<a href='https://github.com/shadowsocksrr/shadowsocksr-android/releases/download/3.5.1/shadowsocksr-android-3.5.1.apk'>这里</a>下载最新翻墙客户端</p>
				<p>下载完后添加新服务器，输入上述给予的端口和密码，加密方法为chacha20，协议和混淆为默认就好</p>
				<p>然后开启代理即可使用国内各种应用</P>
				<p>开始您的畅游之旅吧orz</p>
			";
		}
		include 'footer.htm';
		exit();
	}
	include 'footer.htm';
	exit();
}
if ($action=="admin"){
	include 'header.htm';
	if ($_COOKIE["password"]!="Stave2333"){
	echo "
		<form name='form1' method='post' action='index.php?action=adminlogin'>
		 <p>请输入管理员密码：</p>
		  <label for='password'></label>
		  <input type='text' name='password' id='password'>
		  <input type='submit' name='button' id='button' value='submit' />
		</form>
		";
	include 'footer.htm';
	exit();
	}else{
		$result=mysql_query("SELECT * FROM `gfw`");
		echo "<table width='200' border='1'>";
		echo "<tr>";
		echo "<td>QQ号</td>";
		echo "<td>申请平台</td>";
		echo "<td>申请时间</td>";
		echo "<td>通过状态</td>";
		echo "<td>安装状态</td>";
		echo "<td>通过</td>";
		echo "<td>不通过</td>";
		echo "<td>安装</td>";
		echo "</tr>";
		while ($row=mysql_fetch_array($result)){

			echo "<tr>";
			echo "<td>".$row["qq"]."</td>";
			echo "<td>".$row["pt"]."</td>";
			echo "<td>".$row["time"]."</td>";
			echo "<td>".$row["pass"]."</td>";
			echo "<td>".$row["install"]."</td>";
			if ($row["pass"]=="null"){
				echo "<td><a href='index.php?action=changepass&value=yes&qq=".$row["qq"]."'>Pass</a></td>";
				echo "<td><a href='index.php?action=changepass&value=no&qq=".$row["qq"]."'>No Pass</a></td>";
			}else{
				echo "<td></td><td></td>";
			}
			if ($row["pass"]=="yes" && $row["install"]=="no"){
				echo "<td><a href='index.php?action=changeinstall&qq=".$row["qq"]."'>Installed</a></td>";
			}else{
				echo "<td></td>";
			}
			echo "</tr>";
		
		}
		echo "</table>";

	}
	include 'footer.htm';	
	exit();
}
if ($action=="adminlogin"){
	include 'header.htm';
	if ($_POST["password"]=="Stave2333"){
		echo "登陆成功，<a href='http://gfw.staveworld.com/index.php?action=admin'>点击返回后台</a>";
		setcookie("password", "Stave2333", time()+3600);
		//echo $_SESSION['password'];
		include 'footer.htm';
		exit();
	}else{
		echo "管理员密码错误!";
	}
	
	include 'footer.htm';
	exit();
}
if ($action=="changepass"){
	include 'header.htm';
	$qq=$_GET["qq"];
	if ($_COOKIE["password"]!="Stave2333"){
		echo "Access Denied!";
	}else{
		if($_GET["value"]=="yes"){
			mysql_query("UPDATE `gfw` SET `pass`='yes' WHERE `qq`='$qq'");
		}else{
			mysql_query("UPDATE `gfw` SET `pass`='no' WHERE `qq`='$qq'");
		}
	}
	echo "操作成功，<a href='index.php?action=admin'>返回</a>";
	include 'footer.htm';
	exit();
}
if ($action=="changeinstall"){
	include 'header.htm';
	$qq=$_GET["qq"];
	if ($_COOKIE["password"]!="Stave2333"){
		echo "Access Denied!";
	}else{
		echo "
		<form name='form1' method='post' action='index.php?action=install&qq=".$qq."'>
		<p>端口：
	    <input type='text' name='port' id='port'>
	    </p>
	    <p>密码： 
        <input type='text' name='password' id='password'>
	    </p>
        <input type='submit' name='button' id='button' value='submit' />
        </form>
		";
	}
	include 'footer.htm';
	exit();
}
if ($action=="install"){
	include 'header.htm';
	$qq=$_GET["qq"];
	$port=$_POST["port"];
	$password=$_POST["password"];
	if ($_COOKIE["password"]!="Stave2333"){
		echo "Access Denied!";
	}else{
		mysql_query("UPDATE `gfw` SET `port`='$port',`password`='$password',`install`='yes' WHERE `qq`='$qq'");
		echo "Success,<a href='index.php?action=admin'>return<a>";
	}
	include "footer.htm";
	exit();
}
include  "index.htm";
?>
