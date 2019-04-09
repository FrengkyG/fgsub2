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
			
			
			
			
			//$fileToUpload = $_POST['hiddenVal'];
			//$fileToUpload = $_POST['hiddenVal'];
			//echo("<p>masuk</p>");
			
			
			if(isset($_POST['inputImage2'])){
				$fileToUpload = $_POST['inputImage2'];
				echo("<p>UPLOADING BLOB</p>");
								
			}
			else{
				$fileToUpload ="http://upload.wikimedia.org/wikipedia/commons/3/3c/Shaki_waterfall.jpg";
				//echo("kosong");
			}
			//echo("kosong lagi");
				
						
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
					echo("ContainerName2 = ".$containerName."<br>");
					// Getting local file so that we can upload it to Azure
					$myfile = fopen($fl, "r") or die("Unable to open file!");
					fclose($myfile);
					# Upload file as a block blob
					echo "Uploading BlockBlob: ".PHP_EOL;
					echo $fl;
					echo "<br />";
					
					$content = fopen($fl, "r");
					//echo("SLESAI DECLARE myfile<br>");
					//Upload blob
					$blobClient->createBlockBlob($containerName, "image.jpg" , $content);
					//echo("SLESAI DECLARE UPLOAD BLOB<br>");
					
					// List blobs.
					$listBlobsOptions = new ListBlobsOptions();
					$listBlobsOptions->setPrefix("HelloWorld");

					//echo "These are the blobs present in the container: ";

					do{
						$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
						foreach ($result->getBlobs() as $blob)
						{
							echo $blob->getName().": ".$blob->getUrl()."<br />";
					
							
						}
					
						$listBlobsOptions->setContinuationToken($result->getContinuationToken());
					} while($result->getContinuationToken());
					//echo "<br />";

					// Get blob.
					echo "This is the content of the blob uploaded: ";
					echo '<img src="' . $fl . '">';
					$blob = $blobClient->getBlob($containerName, $fl);
					fpassthru($blob->getContentStream());
					
					//echo "<br />";
					
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