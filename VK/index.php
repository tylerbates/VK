<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">        
        <title>Glitch your friends</title>
        <style>            
            .button
            {
                height: 40px;
                width: 200px;
                background: #020000;
                padding: 10px 10px;             
                font-size: 30px;
                font-weight: bold;
                color: #ff6633;
                text-align: center;
                text-decoration: none;
                border: #ff6633 solid 2px;
                cursor: pointer;
                position: fixed;
                top: 50%;
                left: 50%;
                margin-top: -20px;
                margin-left: -100px;
            }
            .button:hover
            {
                border: #ffcc00 solid 2px;
                color: #ffcc00;
            }
            
            .table
            {
                background: #ffffff;
                height: 500px;
                width: 500px;
                position: fixed;
                top: 50%;
                left: 50%;
                margin-top: -250px;
                margin-left: -250px;
            }           
        </style>                       
    </head>
    <body>
        <?php        
        session_start();
        if(isset($_SESSION['access_token']))
        {           
            $got_photo=get_friends_photo($_SESSION);
            glitch_photo($got_photo);
        }
        else
        {            
            $res = authorize();
            if($res)
            {
                $got_photo=get_friends_photo($_SESSION);
                glitch_photo($got_photo);
            }
        }
        
        function authorize()
        {
            $result=false;
            $client_id = '3892880'; // ID РїСЂРёР»РѕР¶РµРЅРёСЏ
            $client_secret = 'ZsvD1GtgAszbrhGN7Vtm'; // Р—Р°С‰РёС‰С‘РЅРЅС‹Р№ РєР»СЋС‡
            $redirect_uri = 'http://localhost/VK/index.php'; // РђРґСЂРµСЃ СЃР°Р№С‚Р°
            $url = 'http://oauth.vk.com/authorize';
            $params = array(
                'client_id'     => $client_id,
                'redirect_uri'  => $redirect_uri,
                'response_type' => 'code'
            );
            echo $link = '<a class="button" href="' . $url . '?' . urldecode(http_build_query($params)) . '">GLITCH EM</a>';            
            if (isset($_GET['code']))
            {
                $params = array(
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'code' => $_GET['code'],
                    'redirect_uri' => $redirect_uri
                );

                $token = json_decode(file_get_contents('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($params))), true);
                $_SESSION=$token;
                $result=true;
            }
            return $result;
        }
        
        function get_friends_photo($got_token)
        {
            $params = array(
                        'uids'         => $got_token['user_id'],
                        'fields'       => 'uid,photo_medium',
                        'count'        => '25',
                        'access_token' => $got_token['access_token']
                );
            
            $friendsInfo = json_decode(file_get_contents('https://api.vk.com/method/friends.get' . '?' . urldecode(http_build_query($params))), true);
                                
            if(isset($friendsInfo['response']))
            {
                for($i=0;$i<25;$i++)
                {
                    $friendsPhoto[$i]=$friendsInfo['response'][$i]['photo_medium'];
                }                    
            }
            return $friendsPhoto;
        }
        
        function glitch_photo($photo_list)
        {
            $header_size=417;
            $index = array(0,5,10,15,20);
            echo '<table class="table">';
                
            foreach ($index as $i) 
            {
                echo "<tr>";
                for($j=$i;$j<$i+5;$j++)
                {
                    $photo = file_get_contents($photo_list[$j]);
                    for($k=0;$k<5;$k++)
                    {
                        $random = rand(1, 1000);
                        if($header_size+$random < strlen($photo))
                        {
                            $pos=$header_size + $random;
                        }
                        else
                        {
                            $pos=2*$header_size+$random-strlen($photo);
                        }
                        $photo[$pos]=0;
                    }
                    $glitch = base64_encode($photo);          
                    echo "<td><img src='data:image/jpeg;base64, $glitch'/></td>";
                        
                }
                echo "</tr>";
            }
            echo "</table>";
            unset($i);
        }
    ?>       
    </body>
</html>
