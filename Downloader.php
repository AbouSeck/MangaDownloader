<?php 

//Nom du Manga
$dir_name =ucfirst($argv[1]).' '.$argv[2] ;

//Chapitre
$chap_num = $argv[2] ;

//Page
$p=1 ;


$exists = true ;

//Vérification de l'existence du manga 
$file = 'https://www.mangapanda.com/'.$argv[1].'/'.$chap_num.'/'.$p;
$file_headers = @get_headers($file);
if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
        $exists = false;
    }
    else {
        $exists = true;
    }

echo "Votre manga est en cours de téléchargement.." ;

while($exists==true){
        //Recherche de la page     
        $html = file_get_contents('https://www.mangapanda.com/'.$argv[1].'/'.$chap_num.'/'.$p);
        preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i',$html, $matches ); 

        $fileUrl = $matches[ 1 ][0 ];

        //Création du dossier si non existant 
        if (!is_dir(ucfirst($argv[1]))) {
            mkdir(ucfirst($argv[1]));
            chdir(ucfirst($argv[1])) ;
            if (!is_dir($dir_name)) {
                mkdir($dir_name);
                chdir('..') ;
            }
        } else {
            chdir(ucfirst($argv[1])) ;
            if (!is_dir($dir_name)) {
            mkdir($dir_name);
            }
            chdir('..') ;
        }
        

        $saveTo = ucfirst($argv[1]).'/'.$dir_name.'/'.$p.'.jpg';
            
        //Ouverture du fichier
        $fp = fopen($saveTo, 'w+');
            
        //En cas d'erreur lors de l'ouverture.
        if($fp === false){
            throw new Exception('Could not open: ' . $saveTo);
        }
            
        //Création du cURL
        $ch = curl_init($fileUrl);
            
        
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
        //Délai de 20 secondes
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        //Exécution de la requête.
        curl_exec($ch);

        //Exception en cas d'erreur 
        if(curl_errno($ch)){
            throw new Exception(curl_error($ch));
        }
            
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $p++ ;
    echo '.' ;
}
 
//Fermeture du cURL
curl_close($ch);

//Fermeture du fichier
fclose($fp);

if($statusCode == 200){
    echo 'Downloaded !';
} else{
    echo "Status Code: " . $statusCode;
}



?>