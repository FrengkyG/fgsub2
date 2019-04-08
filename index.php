<!DOCTYPE html>
<html>
<head>
    <title>Analyze Sample</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
</head>
<body>
 
<script type="text/javascript">
    function processImage(){
		        // **********************************************
        // *** Update or verify the following values. ***
        // **********************************************
 
        // Replace <Subscription Key> with your valid subscription key.
        var subscriptionKey = "b3e217d502b344bcb2c5c304e0f5bd23";
		console.log("declare sub key");
        // You must use the same Azure region in your REST API method as you used to
        // get your subscription keys. For example, if you got your subscription keys
        // from the West US region, replace "westcentralus" in the URL
        // below with "westus".
        //
        // Free trial subscription keys are generated in the "westus" region.
        // If you use a free trial subscription key, you shouldn't need to change
        // this region.
        var uriBase =
            "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
		//console.log("declare uri base");
        // Request parameters.
        var params = {
            "visualFeatures": "Description",
            "details": "",
            "language": "en",
        };
		//console.log("declare params");
 
        // Display the image.
        var sourceImageUrl = document.getElementById("inputImage").value;
        document.querySelector("#sourceImage").src = sourceImageUrl;
		//console.log(sourceImageUrl);
		//console.log("display images");
		
		
		// Make the REST API call.
        $.ajax({
            url: uriBase + "?" + $.param(params),
 
            // Request headers.
            beforeSend: function(xhrObj){
                xhrObj.setRequestHeader("Content-Type","application/json");
                xhrObj.setRequestHeader(
                    "Ocp-Apim-Subscription-Key", subscriptionKey);
            },
 
            type: "POST",
 
            // Request body.
            data: '{"url": ' + '"' + sourceImageUrl + '"}',
        })
		
 
        .done(function(data) {
            // Show formatted JSON on webpage.
            $("#responseTextArea").val(JSON.stringify(data, null, 2));
		})
		
 
        .fail(function(jqXHR, textStatus, errorThrown) {
            // Display error message.
            var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
            errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
            alert(errorString);
        });
		//console.log("fail");
		
		
    };
</script>
 
<h1>Analyze image:</h1>
Enter the URL to an image, then click the <strong>Analyze image</strong> button.
<br><br>
Image to analyze:
<form action="index.php" method="POST" onsubmit="return false;">
<input type="text" name="inputImage" id="inputImage"
    value="http://upload.wikimedia.org/wikipedia/commons/3/3c/Shaki_waterfall.jpg" />
	<input type="submit" onclick="processImage()" value="Analyze image"/>

			
<br><br>
<div id="wrapper" style="width:1020px; display:table;">
    <div id="jsonOutput" style="width:600px; display:table-cell;">
        Response:
        <br><br>
        <textarea id="responseTextArea" class="UIInput"
                  style="width:580px; height:400px;"></textarea>
    </div>
    <div id="imageDiv" style="width:420px; display:table-cell;">
        Source image:
        <br><br>
        <img id="sourceImage" width="400" />
    </div>
</div>
</form>
<?php
			require_once 'vendor/autoload.php';
			require_once "./random_string.php";

			use MicrosoftAzure\Storage\Blob\BlobRestProxy;
			use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
			use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
			use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
			use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

			$connectionString = "DefaultEndpointsProtocol=https;AccountName=frengkywebapp;AccountKey=+g2IDsqy/m8PUF2iyGtjlxQakYvUzEAlcKgZJ80MvzvV/cgTCgnIL1LWwHGASZayh9n/3OZlei7MM1StDs1s9w==;EndpointSuffix=core.windows.net";
			
			$blobClient = BlobRestProxy::createBlobService($connectionString);
			
			
			//$fileToUpload = false;
			////echo($fileToUpload."<br>");
			$fileToUpload = "http://upload.wikimedia.org/wikipedia/commons/3/3c/Shaki_waterfall.jpg";
			//echo($fileToUpload);
			if(isset($_POST['inputImage'])){
				$fileToUpload = $_POST['inputImage'];
				//echo("<p>masuk</p>");
				//echo("dicoba ".$fileToUpload."<br>");
			}
				
						
			//Get the file
			$content2 = file_get_contents($fileToUpload);
			//Store in the filesystem.
			$fp = fopen("./images/image.jpg", "w");
			fwrite($fp, $content2);
			fclose($fp);
			$fl = "./images/image.jpg";
			
			
			//$a[0] = $_GET['inputImage'];
			//$fileToUpload = (string)$a[0];
			//$fileToUpload = implode("|",$a);
			//echo ($fileToUpload);
			
			if (!isset($_GET["Cleanup"])) {
				//echo("MASUK KE IF<br>");
				// Create container options object.
				$createContainerOptions = new CreateContainerOptions();
				$createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

				// Set container metadata.
				$createContainerOptions->addMetaData("key1", "value1");
				$createContainerOptions->addMetaData("key2", "value2");

				$containerName = "blockblobs".generateRandomString();
				
				//echo("ContainerName = ".$containerName."<br>");
				
				try {
					//echo("MASUK KE TRY 1<br>");
					// Create container.
					$blobClient->createContainer($containerName, $createContainerOptions);
					
					//echo("MASUK KE CREATE CONTAINER<br>");
					//echo("ContainerName2 = ".$containerName."<br>");
					// Getting local file so that we can upload it to Azure
					$myfile = fopen($fl, "r") or die("Unable to open file!");
					fclose($myfile);
					# Upload file as a block blob
					//echo "Uploading BlockBlob: ".PHP_EOL;
					//echo $fl;
					//echo "<br />";
					
					$content = fopen($fl, "r");
					//echo("SLESAI DECLARE myfile<br>");
					//Upload blob
					$blobClient->createBlockBlob($containerName, "image.jpg" , $content);
					//echo("SLESAI DECLARE UPLOAD BLOB<br>");
					/*
					// List blobs.
					$listBlobsOptions = new ListBlobsOptions();
					$listBlobsOptions->setPrefix("HelloWorld");

					//echo "These are the blobs present in the container: ";

					do{
						$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
						foreach ($result->getBlobs() as $blob)
						{
							//echo $blob->getName().": ".$blob->getUrl()."<br />";
						}
					
						$listBlobsOptions->setContinuationToken($result->getContinuationToken());
					} while($result->getContinuationToken());
					//echo "<br />";

					// Get blob.
					//echo "This is the content of the blob uploaded: ";
					$blob = $blobClient->getBlob($containerName, $fl);
					fpassthru($blob->getContentStream());
					//echo "<br />";
					*/
					//echo("MASUK KE TRY");
					
				}
				catch(ServiceException $e){
					// Handle exception based on error codes and messages.
					// Error codes and messages are here:
					// http://msdn.microsoft.com/library/azure/dd179439.aspx
					$code = $e->getCode();
					$error_message = $e->getMessage();
					//echo $code.": ".$error_message."<br />";
					//echo("MASUK KE CATCH 1");
				}
				catch(InvalidArgumentTypeException $e){
					// Handle exception based on error codes and messages.
					// Error codes and messages are here:
					// http://msdn.microsoft.com/library/azure/dd179439.aspx
					$code = $e->getCode();
					$error_message = $e->getMessage();
					//echo $code.": ".$error_message."<br />";
					//echo("MASUK KE CATCH 2");
				}
				
			} 
				
			
			
			else 
			{
				try{
					// Delete container.
					//echo "Deleting Container".PHP_EOL;
					//echo $_GET["containerName"].PHP_EOL;
					//echo "<br />";
					$blobClient->deleteContainer($_GET["containerName"]);
					//echo("masuk ke try else");
				}
				catch(ServiceException $e){
					// Handle exception based on error codes and messages.
					// Error codes and messages are here:
					// http://msdn.microsoft.com/library/azure/dd179439.aspx
					$code = $e->getCode();
					$error_message = $e->getMessage();
					//echo $code.": ".$error_message."<br />";
					//echo("masuk ke catch else");
				}
			}
			
		
			?>
</body>
</html>