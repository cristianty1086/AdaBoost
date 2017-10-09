<?php
 
/**
 * File to handle all API requests
 * Accepts GET and POST
 * 
 * Each request will be identified by TAG
 * Response will be JSON data
 
  /**
 * check for POST request 
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

class emailProfile
{
    function __construct(){
        
    }
    
    function sendEmail($message, $to, $subject)
    {
        
         // to, from, subject, message body, attachment filename, etc.
        $from = "no-reply@advm2msec.com";
        //$filename="/home/user/file.jpeg";
        //$fname="file.jpeg";

        $headers = "From: $from"; 
        // boundary 
        $semi_rand = md5(time()); 
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

        // headers for attachment 
        $headers = 'From:'.$from. "\r\n" .
        'Reply-To: webmaster@example.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

        //$ok = @mail($to, $subject, $message, $headers, "-f " . $from); 
        $ok = @mail($to, $subject, $message, $headers);
        
    }
 
        
    function sendEmailWithImages($message, $to, $subject, $img_path, $img_name)
    {
        
         // to, from, subject, message body, attachment filename, etc.
        //$to = "ctinocoy123@gmail.com";
        $from = "no-reply@advm2msec.com";
        $filename=$img_path;
        $fname=$img_name;

        $headers = "From: $from"; 
        // boundary 
        $semi_rand = md5(time()); 
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

        // headers for attachment 
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 

        // multipart boundary 
        $message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
        $message .= "--{$mime_boundary}\n";

        // preparing attachments            
            $file = fopen($filename,"rb");
            $data = fread($file,filesize($filename));
            fclose($file);
            $data = chunk_split(base64_encode($data));
            $message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"".$fname."\"\n" . 
            "Content-Disposition: attachment;\n" . " filename=\"$fname\"\n" . 
            "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
            $message .= "--{$mime_boundary}--\n";


        // send
        //print $message;

        $ok = @mail($to, $subject, $message, $headers, "-f " . $from); 
        
    }
    
    
      
    function sendEmailWithMultImages($message, $to, $subject, $img_base_path, $img_names)
    {
        
         // to, from, subject, message body, attachment filename, etc.
        $from = "no-reply@advm2msec.com";

        $headers = "From: $from"; 
        // boundary 
        $semi_rand = md5(time()); 
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

        // headers for attachment 
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 

        // multipart boundary 
        $message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
        

        // preparing attachments 
        for($idx=0; $idx<count($img_names) ; $idx++)
        {
            $img_name = $img_names[$idx];
            $fname=$img_name;
            $filename=$img_base_path."/".$img_name;
            
            $file = fopen($filename,"rb");
            echo "\npara send email: ".$filename."/n";
            $data = fread($file,filesize($filename));
            fclose($file);
            $data = chunk_split(base64_encode($data));
            
            $message .= "--{$mime_boundary}\n";
            $message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"".$fname."\"\n" .                   
            "Content-Description: ".$fname."\n" .
            "Content-Disposition: attachment;\n" . " filename=\"$fname\"\n" . 
            "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        }

        // send
        //print $message;

        $ok = @mail($to, $subject, $message, $headers, "-f " . $from); 
        
    }
    
}


                
if (isset($_POST['tag']) && $_POST['tag'] != '') {
    // get tag
    $tag = $_POST['tag'];
 
    // response Array
    $response = array("tag" => $tag, "error" => FALSE);
 
    $emailController = new emailProfile();
    
    // check for tag type
    if ($tag == 'emailandsavedata') {
        // Request type is check Login
        $username = $_POST['usuario'];
        $email = $_POST['email'];
        
        $infoUsuario = $_POST['infoUsuario'];
        $datoEnvio = $_POST['datosDelEnvio'];
        $resumenEnvio = $_POST['resumenDelEnvio'];
        
        $imgCount = $_POST['numImagenesEnviadas'];
         
        $target_dir = "./temp/uploadFiles";
        
        if(!file_exists($target_dir))
        {
            mkdir($target_dir, 0777, true);
        }
        
        //envio de email
        $msg = "Estimado usuario ".$username."\n\n";        
        $msg = $msg.$resumenEnvio."\n\n";
        $msg = $msg.$datoEnvio."\n\n";
        //$msg = $msg.$infoUsuario."\n\n";
        $msg = $msg.$infoProductos."\n\n";
                
        
        $imgCount;
     
        $img_names = array();
        $base_dir = $target_dir;
        for ( $idx  = 0 ; $idx < $imgCount; $idx++ )
        {
            $tagFile = "file".$idx;
            
            $img_name = basename($_FILES[$tagFile]["name"]);
            $target_dir = $base_dir . "/" . $img_name;
            //$img_names[$idx] = $img_name;
            array_push($img_names, $img_name);

            echo $img_name."  ".$target_dir."\n";   
            
            if (move_uploaded_file($_FILES[$tagFile]["tmp_name"], $target_dir)) 
            {
                echo json_encode([
                    "Message" => "El archivo ". $img_name. " fue subido al servidor con exito.",
                    "Status" => "OK"
                ]);            

            } else {

                echo json_encode([
                    "Message" => "Sorry, there was an error uploading your file: ".$img_name,
                    "Status" => "Error"
                ]);
            }     
            
        }
        

        echo "\npreparando envio de email con imagen\n";
        $emailController->sendEmailWithMultImages($msg, $email, $subject, $base_dir, $img_names);
        echo "envio de email: ok";

    } 
    else
    {
        $response["Message"] = "Datos enviados incorrectamente";
        $response["Status"] = "Error";
        echo json_encode($response);
    }
    
} else {
    $response["Message"] = "Se requiere el parametro 'tag'";
    $response["Status"] = "Error";
    echo json_encode($response);
}





?>

<html>
    <head>
        <title>Crea y Regala Movil</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div>Diseñado para servir las peticiones desde la Iphone y Android</div>
    </body>
</html>

