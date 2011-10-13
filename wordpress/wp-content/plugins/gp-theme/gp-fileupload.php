<?php
// Adding Wordpress on top. Not an ideal solution. We really need to intergrate this fully into forms properly. (see: core/gp-forms.php)

require_once('/var/www/production/www.thegreenpages.com.au/wordpress/wp-load.php');

class UploadHandler
{
    private $options;

    function __construct($options=null) {
    	
    	$upload_dir = wp_upload_dir();

        $this->options = array(
        	'db_table' => 'wp_gp_drinquiries',
        	'inq_id' => $_SESSION['gp_drinquiries']['id'],
            'script_url' => $_SERVER['PHP_SELF'],
            'upload_dir' => $upload_dir['basedir'] . '/gp-drinquiries/' . $_SESSION['gp_drinquiries']['id'] . '/original/',
            'upload_url' => $upload_dir['baseurl'] .'/gp-drinquiries/' . $_SESSION['gp_drinquiries']['id'] . '/original/',
            'param_name' => 'dir_images',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size' => 2000000,
            'min_file_size' => 1,
            #'accept_file_types' => '/.+$/i',
        	'accept_file_types' => '/(\.|\/)(gif|jpe?g|png)$/i',
            'max_number_of_files' => 9,
            'discard_aborted_uploads' => true,
            'image_versions' => array(
                // Uncomment the following version to restrict the size of
                // uploaded images. You can also add additional versions with
                // their own upload directories:
                /*
                'large' => array(
                    'upload_dir' => dirname(__FILE__).'/files/',
                    'upload_url' => dirname($_SERVER['PHP_SELF']).'/files/',
                    'max_width' => 1920,
                    'max_height' => 1200
                ),
                */
                'directory' => array(
                    'upload_dir' => $upload_dir['basedir'] . '/gp-drinquiries/' . $_SESSION['gp_drinquiries']['id'] . '/directory/',
                    'upload_url' => $upload_dir['baseurl'] . '/gp-drinquiries/' . $_SESSION['gp_drinquiries']['id'] . '/directory/',
                    'max_width' => 200,
                    'max_height' => 200
                )
            )
        );
        if ($options) {
            $this->options = array_replace_recursive($this->options, $options);
        }
    }
    
    private function get_file_object($file_name) {
        $file_path = $this->options['upload_dir'].$file_name;
        if (is_file($file_path) && $file_name[0] !== '.') {
            $file = new stdClass();
            $file->name = $file_name;
            $file->size = filesize($file_path);
            $file->url = $this->options['upload_url'].rawurlencode($file->name);
            foreach($this->options['image_versions'] as $version => $options) {
                if (is_file($options['upload_dir'].$file_name)) {
                    $file->{$version.'_url'} = $options['upload_url']
                        .rawurlencode($file->name);
                }
            }
            $file->delete_url = $this->options['script_url']
                .'?file='.rawurlencode($file->name);
            $file->delete_type = 'DELETE';
            return $file;
        }
        return null;
    }
    
    private function get_file_objects() {
        return array_values(array_filter(array_map(
            array($this, 'get_file_object'),
            scandir($this->options['upload_dir'])
        )));
    }

    private function create_scaled_image($file_name, $options) {
        $file_path = $this->options['upload_dir'].$file_name;
        $new_file_path = $options['upload_dir'].$file_name;
        list($img_width, $img_height) = @getimagesize($file_path);
        if (!$img_width || !$img_height) {
            return false;
        }
        $scale = min(
            $options['max_width'] / $img_width,
            $options['max_height'] / $img_height
        );
        if ($scale > 1) {
            $scale = 1;
        }
        $new_width = $img_width * $scale;
        $new_height = $img_height * $scale;
        $new_img = @imagecreatetruecolor($new_width, $new_height);
        switch (strtolower(substr(strrchr($file_name, '.'), 1))) {
            case 'jpg':
            case 'jpeg':
                $src_img = @imagecreatefromjpeg($file_path);
                $write_image = 'imagejpeg';
                break;
            case 'gif':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                $src_img = @imagecreatefromgif($file_path);
                $write_image = 'imagegif';
                break;
            case 'png':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                @imagealphablending($new_img, false);
                @imagesavealpha($new_img, true);
                $src_img = @imagecreatefrompng($file_path);
                $write_image = 'imagepng';
                break;
            default:
                $src_img = $image_method = null;
        }
        $success = $src_img && @imagecopyresampled(
            $new_img,
            $src_img,
            0, 0, 0, 0,
            $new_width,
            $new_height,
            $img_width,
            $img_height
        ) && $write_image($new_img, $new_file_path);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($src_img);
        @imagedestroy($new_img);
        return $success;
    }
    
    private function has_error($uploaded_file, $file, $error) {
        if ($error) {
            return $error;
        }
        if (!preg_match($this->options['accept_file_types'], $file->name)) {
            return 'acceptFileTypes';
        }
        if ($uploaded_file && is_uploaded_file($uploaded_file)) {
            $file_size = filesize($uploaded_file);
        } else {
            $file_size = $_SERVER['CONTENT_LENGTH'];
        }
        if ($this->options['max_file_size'] && (
                $file_size > $this->options['max_file_size'] ||
                $file->size > $this->options['max_file_size'])
            ) {
            return 'maxFileSize';
        }
        if ($this->options['min_file_size'] &&
            $file_size < $this->options['min_file_size']) {
            return 'minFileSize';
        }
        if (is_int($this->options['max_number_of_files']) && (
                count($this->get_file_objects()) >= $this->options['max_number_of_files'])
            ) {
            return 'maxNumberOfFiles';
        }
        return $error;
    }
    
    private function handle_file_upload($uploaded_file, $name, $size, $type, $error) {
        $file = new stdClass();
        // Remove path information and dots around the filename, to prevent uploading
        // into different directories or replacing hidden system files.
        // Also remove control characters and spaces (\x00..\x20) around the filename:
        $file->name = trim(basename(stripslashes($name)), ".\x00..\x20");
        $file->size = intval($size);
        $file->type = $type;      
        $error = $this->has_error($uploaded_file, $file, $error);
        
        $upload_dir = wp_upload_dir();
        
    	if (isset($_SESSION['gp_drinquiries']) && isset($_SESSION['gp_drinquiries']['id'])) {
			wp_mkdir_p( $upload_dir['basedir'] . '/gp-drinquiries/' . $_SESSION['gp_drinquiries']['id'] . '/original/' );
			wp_mkdir_p( $upload_dir['basedir'] . '/gp-drinquiries/' . $_SESSION['gp_drinquiries']['id'] . '/directory/' );
		} else {
			$error = "Session Expired";
		}
        
        if (!$error && $file->name) {
            $file_path = $this->options['upload_dir'].$file->name;
            $append_file = !$this->options['discard_aborted_uploads'] &&
                is_file($file_path) && $file->size > filesize($file_path);
            clearstatcache();
            if ($uploaded_file && is_uploaded_file($uploaded_file)) {
                // multipart/formdata uploads (POST method uploads)
                if ($append_file) {
                    file_put_contents(
                        $file_path,
                        fopen($uploaded_file, 'r'),
                        FILE_APPEND
                    );
                } else {
                    move_uploaded_file($uploaded_file, $file_path);
                }
            } else {
                // Non-multipart uploads (PUT method support)
                file_put_contents(
                    $file_path,
                    fopen('php://input', 'r'),
                    $append_file ? FILE_APPEND : 0
                );
            }
            $file_size = filesize($file_path);
            if ($file_size === $file->size) {
                $file->url = $this->options['upload_url'].rawurlencode($file->name);
                foreach($this->options['image_versions'] as $version => $options) {
                    if ($this->create_scaled_image($file->name, $options)) {
                        $file->{$version.'_url'} = $options['upload_url']
                            .rawurlencode($file->name);
                    }
                }
            } else if ($this->options['discard_aborted_uploads']) {
                unlink($file_path);
                $file->error = 'abort';
            }
            $file->size = $file_size;
            $file->delete_url = $this->options['script_url']
                .'?file='.rawurlencode($file->name);
            $file->delete_type = 'DELETE';
        } else {
            $file->error = $error;
        }
        return $file;
    }
    
    public function get() {
        $file_name = isset($_REQUEST['file']) ?
            basename(stripslashes($_REQUEST['file'])) : null; 
        if ($file_name) {
            $info = $this->get_file_object($file_name);
        } else {
            $info = $this->get_file_objects();
        }
        header('Content-type: application/json');
        echo json_encode($info);
    }
    
    public function post() {
        $upload = isset($_FILES[$this->options['param_name']]) ?
            $_FILES[$this->options['param_name']] : array(
                'tmp_name' => null,
                'name' => null,
                'size' => null,
                'type' => null,
                'error' => null
            );
        $info = array();
        if (is_array($upload['tmp_name'])) {
            foreach ($upload['tmp_name'] as $index => $value) {
                $info[] = $this->handle_file_upload(
                    $upload['tmp_name'][$index],
                    isset($_SERVER['HTTP_X_FILE_NAME']) ?
                        $_SERVER['HTTP_X_FILE_NAME'] : $upload['name'][$index],
                    isset($_SERVER['HTTP_X_FILE_SIZE']) ?
                        $_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'][$index],
                    isset($_SERVER['HTTP_X_FILE_TYPE']) ?
                        $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'][$index],
                    $upload['error'][$index]
                );
            }
        } else {
            $info[] = $this->handle_file_upload(
                $upload['tmp_name'],
                isset($_SERVER['HTTP_X_FILE_NAME']) ?
                    $_SERVER['HTTP_X_FILE_NAME'] : $upload['name'],
                isset($_SERVER['HTTP_X_FILE_SIZE']) ?
                    $_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'],
                isset($_SERVER['HTTP_X_FILE_TYPE']) ?
                    $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'],
                $upload['error']
            );
        }
        
        
        // Note: It seems jquery is actually posting images 1 at a time.
        
    	global $wpdb, $current_user;

	    if (is_user_logged_in()) {
			$user_id = $current_user->ID;
		} else {
			$user_id = 0;
		}
    	
        $images = array();

        for ($i=0;$i<count($info);$i++) {
        	if (!isset($info[$i]->error)) {
        		$images[] = $info[$i]->name;
        	}
        }
        
        if (isset($_SESSION['gp_drinquiries']) && isset($_SESSION['gp_drinquiries']['id'])) {
	        if (count($images) > 0) {
	        	if ($user_id != 0) {$adduser = ' AND UID = ' . $user_id;} else {$adduser = '';}
	        	$qrystring = $wpdb->prepare("SELECT " . $this->options['param_name'] ." FROM " . $this->options['db_table'] . " WHERE ID = " . $this->options['inq_id'] ." AND session_id = '" . session_id() . "'". $adduser);
				$qryresults = $wpdb->get_row($qrystring);
	
				if ($qryresults->dir_images) {
					$value = $qryresults->dir_images . "," . implode(",", $images);
				} else {
					$value = implode(",", $images);
				}
				
	        	$update_result = $wpdb->update( $this->options['db_table'], array($this->options['param_name'] => $value ), array('ID' => $this->options['inq_id']), array('%s'), array('%d'));
				if ($update_result === 0 || $update_result === false) {
					// $info[?]->error = ?;
					// unset($info[?]->name) 
					// ...etc
				}
	       	}
        } else {
	        for ($i=0;$i<count($info);$i++) {
	        	$info[$i]->error = "Session Expired" . session_id();
	        }
        }
        
        header('Vary: Accept');
        if (isset($_SERVER['HTTP_ACCEPT']) &&
            (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
        echo json_encode($info);
    }
    
    public function delete() {
        $file_name = isset($_REQUEST['file']) ?
            basename(stripslashes($_REQUEST['file'])) : null;
        $file_path = $this->options['upload_dir'].$file_name;
        $success = is_file($file_path) && $file_name[0] !== '.' && unlink($file_path);
        if ($success) {
            foreach($this->options['image_versions'] as $version => $options) {
                $file = $options['upload_dir'].$file_name;
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        
        global $wpdb, $current_user;
        
		if (is_user_logged_in()) {
			$user_id = $current_user->ID;
		} else {
			$user_id = 0;
		}
        
        if (isset($_SESSION['gp_drinquiries']) && isset($_SESSION['gp_drinquiries']['id'])) {
	        if ($user_id != 0) {$adduser = ' AND UID = ' . $user_id;} else {$adduser = '';}
	        $qrystring = $wpdb->prepare("SELECT " . $this->options['param_name'] ." FROM " . $this->options['db_table'] . " WHERE ID = " . $this->options['inq_id'] ." AND session_id = '" . session_id() . "'". $adduser);
			$qryresults = $wpdb->get_row($qrystring);
	        
			$images = explode(",", $qryresults->dir_images);
			$pos = array_search($file_name, $images);
			unset($images[$pos]);
			
	    	$update_result = $wpdb->update( $this->options['db_table'], array($this->options['param_name'] => implode(",", $images) ), array('ID' => $this->options['inq_id']), array('%s'), array('%d'));
			if ($update_result === 0 || $update_result === false) {
				// error
			}
        }
        
        header('Content-type: application/json');
        echo json_encode($success);
    }
}

$upload_handler = new UploadHandler();

header('Pragma: no-cache');
header('Cache-Control: private, no-cache');
header('Content-Disposition: inline; filename="files.json"');
header('X-Content-Type-Options: nosniff');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'HEAD':
    case 'GET':
        $upload_handler->get();
        break;
    case 'POST':
        $upload_handler->post();
        break;
    case 'DELETE':
        $upload_handler->delete();
        break;
    case 'OPTIONS':
        break;
    default:
        header('HTTP/1.0 405 Method Not Allowed');
}
?>