<?php
//create socket only if user is logged in
if (is_user_logged_in()) {


    $current_user = wp_get_current_user();
    global $post;

    $IS_PROD = true;

    if ($IS_PROD) {
        //for production
        $socket_url = "https://seedsjobfair.com/socket";
        $socket_client_url = "https://seedsjobfair.com/socket/socket-client";
    } else {
        //for localhost
        $port = "5000";
        $socket_url = "http://localhost:$port";
        $socket_client_url = "http://localhost:$port/socket.io/socket.io.js";
    }
    ?>
    <script src='<?= $socket_client_url ?>'></script>
    <script>
        jQuery(document).ready(function () {
            var page_name = '<?php echo $post->post_name; ?>';
            var self_user_id = <?php echo ($current_user) ? $current_user->ID : null; ?>;
            var socket_data = {};
            socket_data['id'] = self_user_id;
            socket_data['page'] = page_name;
            try {
                console.log("Init socket"); 
                socket = io.connect('<?= $socket_url ?>');

                socket.on('connect', function () {
                    console.log("Connected");
                    socket.emit('join', socket_data);
                });

                if (page_name !== 'session') {
                    socket.on('session_ready',function(data){
                        //data.session_id
                        //data.host_id
                        
                    });
                    
                    //create popup notification
                    socket.on('receive_message', function (data) {
                        //socket id
                        console.log(data);
                        return;
                        //open modal here
                        popup.openPopup("You got new message!", data.from_id + " says : " + data.message);
                    });
                }

            } catch (err) {
                socket = false;
                console.log(err);
            }
        });
    </script>

<?php } ?>