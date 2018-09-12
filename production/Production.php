<?php

namespace Outlandish\Sync;
 
class Client extends AbstractSync {

	/**
	 * @var resource cURL handle
	 */
	public $curl;

	/**
	 * Initiates the sync by exchanging file lists
	 * @param $url string URL to remote sync server script
	 */
	public function run($url) {
		date_default_timezone_set("Asia/Calcutta");
		$this->curl = curl_init($url);

		//send client file list to server
		$localFiles = $this->getFileList($this->path);
		$request = array(
			'action' => self::ACTION_FILELIST,
			'data' => $localFiles
		);
		
		$response = $this->post($request);

		if (isset($response['error'])) {
			$d = array('error' => "<tr><td col='4'>Error Occured</td></tr>" );
			echo "data: " . json_encode($d);
			return;
		}
		$responseReturn = array();
		//process modified files
		$x = 0;
		$ttlfile = count($response['data']);
		 
		if(isset($_POST["sdate"]) && isset($_POST["edate"]))
		{
			foreach ($response['data'] as $relativePath => $info) 
			{
				$starttimestamp = strtotime(date($_POST["sdate"].' 00:00:00', $_POST["sdate"]));
				$endtimestamp = strtotime(date($_POST["edate"].' 23:59:59', $_POST["edate"]));
				 
				$filetimestamp = $info["timestamp"];
				if(($filetimestamp >= $starttimestamp) && ($filetimestamp <= $endtimestamp))
				{
					$timestamp = $info["timestamp"];
					$timestamp_in_seconds = $timestamp;
					$responseReturn["datetime"] = date('D M d Y H:i:s', $timestamp_in_seconds);
					$responseReturn["timestamp"] = $timestamp;
					$responseReturn["size"] = $info["size"];
					$responseReturn["fileperm"] = $info["fileperm"];
					$responseReturn["filepath"] = $relativePath;
					
					$x++;
					$progress = ($x)*10;
					$d = array('html' => "<tr><td scope='row'>$x</td><td>$relativePath</td><td>".$this->formatSizeUnits($info["size"])."</td><td>".$responseReturn["datetime"]."</td></tr>" , 'progress' => (100/$ttlfile) * $x ,'totalfiles' => $ttlfile,'donefile' => $x);
					
					
					
					
					//fetch file contents
					$response = $this->post(array(
						'action' => self::ACTION_FETCH,
						'file' => $relativePath
					));

					//save file
					$absolutePath = $this->path . $relativePath;
					if (!file_exists(dirname($absolutePath))) {
						mkdir(dirname($absolutePath), 0777, true);
					}
					file_put_contents($absolutePath, $response);

					//update modified time to match server
					touch($absolutePath, $info['timestamp']);

					//update permissions to match server
					chmod($absolutePath, octdec(intval($info['fileperm'])));
				 
					 
					if(isset($_POST["submit"]))
					{
						echo "<tr><td scope='row'>$x</td><td>$relativePath</td><td>".$this->formatSizeUnits($info["size"])."</td><td>".$responseReturn["datetime"]."</td></tr>";
						echo PHP_EOL;
						ob_flush();
						flush();  
					}
					else{
						echo "id: $x" . PHP_EOL;
						echo "data: " . json_encode($d) . PHP_EOL;
						echo PHP_EOL;
						ob_flush();
						flush();  
					}
				}
				else{
					echo "<tr><td colspan='4'>No file between range</td></tr>";
					echo PHP_EOL;
					ob_flush();
					flush();  
				}
			}
			if(isset($_POST["submit"]))
			{
				echo "<tr><td colspan='4'><div class='alert alert-success'><strong>Done Successfully!</strong></div></td></tr>";
				echo PHP_EOL;
				ob_flush();
				flush();  
			}
			else{
				$d = array('html' => "TERMINATE" , 'progress' => 100 ,'totalfiles' => $ttlfile,'donefile' => $x);
				echo "id: $x" . PHP_EOL;
				echo "data: " . json_encode($d) . PHP_EOL;
				echo PHP_EOL;
				ob_flush();
				flush(); 
			}
		}
		else{
			echo "<tr><td colspan='4' class='alert alert-danger'>please select date</td></tr>";
			echo PHP_EOL;
			ob_flush();
			flush();  
		}
	}

	/**
	 * @param $data array
	 * @return mixed
	 * @throws \RuntimeException
	 */
	protected function post($data) {

		$data['key'] = $this->key;

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_HEADER, 1);
		curl_setopt($this->curl, CURLOPT_POST, 1);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

		list($headers, $body) = explode("\r\n\r\n", curl_exec($this->curl), 2);
		$code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
		if ($code != 200) {
			throw new \RuntimeException('HTTP error: '.$code);
		}

		if (stripos($headers, 'Content-type: application/json') !== false) {
			$body = json_decode($body, 1);
		}

		return $body;
	}
	
	function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
	}
}
