<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador</title>
	<link href="https://cdn.obeygdbot.xyz/css/dashboard.css?v=14" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
	<?php 
        echo file_get_contents("https://cdn.obeygdbot.xyz/htmlext/loadingalert.html");
        echo file_get_contents("https://cdn.obeygdbot.xyz/htmlext/flayeralert.html");
    ?>
</head>
<style>
    .songscontent {
        border: 3px solid transparent;
    }

    .dashboard-container.gdbox-brown.songs {
        margin-top: 1%;
        margin-left: calc(5vw + (10vw / 1000));
        margin-right: calc(5vw + (10vw / 1000));
        margin-bottom: 1%;
    }

</style>
<body>

<div class="dashboard-container gdbox-brown songs">
    <h1 class="gdfont-Pusab normal">ObeyGDBrowser Installer</h1>
    <label class="gdfont-Pusab small">Log into an administrator account in your database to install.</label>
    <?php
	
	require "./incl/lib/connection.php";
	require "./incl/lib/generatePass.php";

	
    function getLatestReleaseUrl($owner, $repo) {
        $url = "https://api.github.com/repos/$owner/$repo/releases/latest";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: PHP']);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        if (isset($data['html_url'])) {
            return $data['html_url'];
        } else {
            return false;
        }
    }

    function downloadAndExtractRepo($repoUrl) {
        $zipFile = 'repo.zip';
        file_put_contents($zipFile, fopen($repoUrl, 'r'));
        if (file_exists($zipFile)) {
            $zip = new ZipArchive;
            if ($zip->open($zipFile) === TRUE) {
                $zip->extractTo('./');
                $firstDir = $zip->getNameIndex(0);
                $zip->close();
                unlink($zipFile);
                return $firstDir;
            } else {
                return -1;
            }
        } else {
            return -2;
        }
    }

    function moveFilesToCurrentDirectory($sourceDir) {
        $sourceDir = rtrim($sourceDir, '/');
        $destinationDir = './';
        $files = scandir("./".$sourceDir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $sourcePath = $sourceDir . '/' . $file;
                $destinationPath = $destinationDir . $file;
                if (is_dir($sourcePath)) {
                    rename($sourcePath, $destinationPath);
                } else {
                    copy($sourcePath, $destinationPath);
                    unlink($sourcePath);
                }
            }
        }
        unlink("./".$sourceDir);
        return true;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $owner = 'MigMatos';
        $repo = 'ObeyGDBrowser';
		echo "<script>";
		echo '$("#loading-main").show();';
		echo "const event = new Event('initLoadingAlert');";
		echo 'changeLoadingAlert("Checking login...");';
		echo "</script>";
		if(isset($_POST['userName']) && isset($_POST['password'])) {
			$userName = $_POST['userName'];
			$password = $_POST['password'];
    

			$pass = GeneratePass::isValidUsrname($userName, $password);
			
			if($pass == "1"){
			
			
			$latestReleaseUrl = getLatestReleaseUrl($owner, $repo);
			if ($latestReleaseUrl) {
				
				echo "<script>";
				echo 'changeLoadingAlert("Installing...");';
				echo "</script>";
				
				$dir = downloadAndExtractRepo($latestReleaseUrl);
				moveFilesToCurrentDirectory($dir);
				header("Location: ./");
			} else {
				header("Location: ./?alert=1");
				}
			}
			else {
				header("Location: ./?alert=2");
			}
		} else {
			header("Location: ./?alert=3");
		}
    } else {
    
	
	if ( isset($_GET["alert"] ) ) {
		$num = $_GET["alert"];
		if($num == 1){
			echo '<script>CreateFLAlert("Error","`r0 Failed to get installation` \n Report in ObeyGDBrowser Support Server: [![Geometry Dash](https://invidget.switchblade.xyz/EbYKSHh95B)](https://discord.gg/EbYKSHh95B)");</script>';
		}
		elseif ($num == 2){
			echo '<script>CreateFLAlert("Error","`r0 You are not an administrator` \n You need to be an **administrator account** to continue with the installation, if you need help join our Discord Support Server: [![Geometry Dash](https://invidget.switchblade.xyz/EbYKSHh95B)](https://discord.gg/EbYKSHh95B)");</script>';
		}
		elseif ($num == 3){
			echo '<script>CreateFLAlert("Error","`r0 Submit empty...`");</script>';
		}
		
	}
	
	}
	
	?>
	<br><br><br>
	
	
	
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<div>
			<label class="gdfont-Pusab small" for="userName">UserName:</label><br>
			<input  class="gdInput text" type="text" id="userName" name="userName" required>
		</div>
		<div>
			<label class="gdfont-Pusab small" for="password">Password:</label><br>
			<input class="gdInput text" type="password" id="password" name="password" required>
		</div>
		<br>
        <button class="gdButton" type="submit" onclick="submitForm()"><label class="gdfont-Pusab small">Install</label></button>
    </form>
	<br><br>
</div>
</body>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
        $("#loading-main").hide();
		
		if ((window.location.search == "?alert=1") || (window.location.search == "?alert=2" ||(window.location.search == "?alert=3") || (window.location.search == "?alert=4"))) {
		let newURLpush = window.location.href.replace(/(\?alert=1|\?alert=2|\?alert=3|\?alert=4)/g, '');
		window.history.pushState(null, null, newURLpush);
		}
		
		function submitForm(){
			$("#loading-main").show();
			const event = new Event('initLoadingAlert');
			document.dispatchEvent(event);
			changeLoadingAlert("Checking login...");
		}
		
</script>
</html>
