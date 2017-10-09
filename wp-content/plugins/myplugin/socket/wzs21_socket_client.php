<?php
//create socket only if user is logged in
if (is_user_logged_in()) {

    global $post;
    $current_user = wp_get_current_user();
    $user_id = ($current_user) ? $current_user->ID : null;
    $user_role = Users::get_user_role();

    $socket_data = array();
    $socket_data['id'] = $user_id;
    $socket_data['page'] = $post->post_name;
    $socket_data['role'] = $user_role;
    if ($user_role == SiteInfo::ROLE_RECRUITER) {
        $socket_data['company_id'] = get_usermeta($user_id, SiteInfo::USERMETA_REC_COMPANY);
    }

    if (IS_PROD) {
        //for production
        //$socket_url = "https://seedsjobfair.com/socket";
        $socket_url = "https://seedsjobfair.com";
        $socket_client_url = "https://seedsjobfair.com/socket/socket-client";
    } else {
        //for localhost
        $port = "5000";
        //$port = "88";
        //$port = "3000";
        $socket_url = "http://localhost:$port";
        //$socket_client_url = "http://localhost:$port/socket.io/socket.io.js";
        $socket_client_url = "http://localhost:$port/socket/socket-client";
        //$socket_client_url = "https://cdn.socket.io/socket.io-1.3.5.js";
    }
    ?>
    <script src='<?= $socket_client_url ?>'></script>
    <script>
        jQuery(document).ready(function () {
            var socket_data = '<?= json_encode($socket_data) ?>';
            socketData = new SocketData();

            try {
                //init data
                socket_data = JSON.parse(socket_data);
                var page_name = socket_data.page;
                var role = socket_data.role;

                //create socket connection
                console.log("Init socket");
                socket = io.connect('<?= $socket_url ?>');


                socket.on('connect', function () {
                    console.log("Connected");
                    socket.emit('join', socket_data);
                });

                socket.on("notification", function (data) {
                    console.log("notification");
                    console.log(data);
                    notificationCenter.showNotification(page_name, data.event, data.data);
                });


                //set event handler
                if (page_name === 'home') {
                    if (role === "<?= SiteInfo::ROLE_STUDENT ?>") {
                        //dom from myp_partial_company_listing_card
                        socket.on('all_online_company', function (data) {
                            socketData.updateViewOnlineCompany(data);
                        });

                        socket.on('in_queue_emit', function (data) {
                            console.log(data);
                            socketData.updateViewQueues(data);
                        });

                    }
                }


                if (page_name !== 'session') {
                    socket.on('session_ready', function (data) {
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