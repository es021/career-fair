<?php
$other_name = get_user_meta($other_user_id, SiteInfo::USERMETA_FIRST_NAME, true);
?>

<div class="wzs21_chat_ui_chatbox no_flash_border">
    <div id="header">
        <?= $other_name ?>
        <div class="subtitle" id="other_status"></div>
    </div>

    <div id="chat_log">
        <div class="text-center container_load_more" hidden="true">
            <small>
                <a id="btn_load_more" class="btn_link btn_blue">
                    Load more message</a>
                <div  id="icon_load_more" hidden="hidden">
                    <i class="fa fa-spinner fa-pulse"></i>
                </div>
            </small>
        </div>
        <div id="chat_content"></div>
    </div> 

    <div id="chat_input">
        <textarea placeholder="Type a message. Press Ctrl+Enter to start new line" id="chat_input" rows="2"></textarea>
        <button id="btn_send" type="button" class="btn btn-primary" disabled="true">Send</button> 
    </div>
</div>

<style>


    .wzs21_chat_ui_chatbox #header{
        padding: 5px 10px;
        background-color: #0084ff;
        color: white;
    }

    .wzs21_chat_ui_chatbox #header .subtitle{
        font-size: 80%;
        color:gainsboro;
    }

    .flash_border{
        -webkit-box-shadow: 0 0 30px cornflowerblue;
        -moz-box-shadow: 0 0 30px cornflowerblue;
        box-shadow: 0 0 30px cornflowerblue;
        transition: box-shadow 2s;
    }

    .no_flash_border{
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
        transition: box-shadow 2s;
    }

    .same_prev{
        margin-top: 3px !important;
    }

</style>

<script>

    jQuery(document).ready(function () {
        ///////////////////////////////////////////////////////////////
        /// DOM OBJECT ////////////////////////////////////////////////
        var parent_chat_box = jQuery('.wzs21_chat_ui_chatbox');
        var chat_container = jQuery('.wzs21_chat_ui_chatbox #chat_log');
        var chat_log = jQuery('.wzs21_chat_ui_chatbox #chat_log #chat_content');
        var chat_input = jQuery('.wzs21_chat_ui_chatbox textarea#chat_input');
        var prev_message = "";
        var other_status = jQuery('.wzs21_chat_ui_chatbox #header #other_status');
        //var ori_chat_input = chat_input;

        var flashInterval = null;
        //addFlashToDom(parent_chat_box);
        parent_chat_box.click(function () {
            removeFlashDom(parent_chat_box, flashInterval);
        });

        //var error_log = jQuery('.wzs21_chat_ui_chatbox #error_log');
        var btn_send = jQuery('.wzs21_chat_ui_chatbox #btn_send');
        var div_load_more = jQuery('.wzs21_chat_ui_chatbox .container_load_more');
        var btn_load_more = div_load_more.find('#btn_load_more');
        var icon_load_more = div_load_more.find('#icon_load_more');

        var self_user_id = <?php echo $self_user_id; ?>;
        var other_user_id = <?php echo $other_user_id; ?>;

        //2 seconds
        var INTERVAL_CHECK_MESSAGE = 2000;
        var LIMIT_MESSAGE_FETCH = <?= wzsChat::$LIMIT_MESSAGE_FETCH ?>;

        var chat_ready = false;
        var current_mes_count = 0;
        var current_start_pos = 0;


        Main();
        function Main() {
            console.log("self : " + self_user_id);
            console.log("other : " + other_user_id);

            //page_name = "session";

            //chat_input.append("Type a message");

            if (socket) {
                registerSocketEvent();
                socketChatInit();
            }

            get_message("init");

            if (!socket) {
                // if socket failed to created, have to used short polling
                var check_new_message_interval = setInterval(check_new_message, INTERVAL_CHECK_MESSAGE);
            }

        }


        /***** Chat Log Controller START ************************************ */

        /** Chat Specify Socket Handler - START **/



        function registerSocketEvent() {
            socket.on('other_offline', function (data) {
                console.log("Other offline");
                other_status.html("Offline");
                console.log(data);
            });

            socket.on('other_online', function (data) {
                console.log("Other online");
                other_status.html("Online");
                console.log(data);
            });

            socket.on('receive_message', function (data) {
                console.log(data);
                if (!chat_input.is(":focus")) {
                    flashInterval = addFlashToDom(parent_chat_box);
                }
                addChatLog(data.message, 'other', "append");
                /*
                 if (page_name === 'session') {
                 if (!chat_input.is(":focus")) {
                 flashInterval = addFlashToDom(parent_chat_box);
                 }
                 addChatLog(data.message, 'other', "append");
                 } else { //other pages.. show popup
                 
                 }
                 */
            });
        }

        function socketEmit(event, data) {
            if (socket) {
                socket.emit(event, data);
                return true;
            } else {
                return false;
            }
        }

        function socketChatInit() {
            var join_chat_data = {};
            join_chat_data.self_id = self_user_id;
            join_chat_data.other_id = other_user_id;

            socketEmit('open_chat', join_chat_data);
        }
        /** Chat Specify Socket Handler - END **/

        function get_message(type) {

            var data = {};
            data.action = 'wzs21_chat_get_message';
            data.self_user_id = self_user_id;
            data.other_user_id = other_user_id;
            data.type = type;

            if (type === "load_more") { //need to pass in extra param
                data.end_pos = current_start_pos - 1;
                toogleShowHidden(btn_load_more, icon_load_more);
            }

            jQuery.ajax({
                url: ajaxurl,
                data: data,
                type: 'POST',
                success: function (response) {
                    response = JSON.parse(response);
                    console.log(response);
                    if (response.data.length === LIMIT_MESSAGE_FETCH) {
                        div_load_more.show();
                    }

                    //current_start_pos = Number.parseInt(response.start_pos);
                    current_start_pos = Number(response.start_pos);
                    if (current_start_pos <= 0) {
                        div_load_more.hide();
                    }

                    process_message_data(response, type);

                },
                error: function (err) {
                    console.log("Err " + err);

                }
            });
        }

        function process_message_data(response, type) {

            if (response.status === "success") {

                //init
                if (type === "init") {
                    for (var m in response.data) {
                        var mes = response.data[m];
                        addChatLog(mes['message'], mes['type'], "append");
                    }
                }

                //load more has to be reverse
                else if (type === "load_more") {
                    for (var m in response.data) {
                        var mes = response.data[response.data.length - m - 1];
                        addChatLog(mes['message'], mes['type'], "prepend");
                    }
                    toogleShowHidden(btn_load_more, icon_load_more);
                }


                current_mes_count = response.mes_count;
                //console.log(current_mes_count);

            } else if (response.status === "error") {
                if (response.data === '<?= wzsChat::$RES_GM_NULL ?>') {
                    div_load_more.hide();

                    if (current_mes_count === 0) {
                        //no message at all
                    }
                    //console.log("No More message");
                }
                console.log("Error : " + response.data);
            }

            chat_ready = true;

        }

        //this function only needed if socket failed
        function check_new_message() {
            if (chat_ready) {
                jQuery.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'wzs21_chat_check_new_message', // return true or false
                        self_user_id: self_user_id,
                        other_user_id: other_user_id,
                        current_mes_count: current_mes_count
                    },
                    type: 'POST',
                    success: function (response) {
                        response = JSON.parse(response);
                        //console.log(response);
                        if (response.has_new && response.status !== "error") {

                            console.log("New Message Received");
                            //console.log(response);
                            if (!chat_input.is(":focus")) {
                                flashInterval = addFlashToDom(parent_chat_box);
                            }
                            //this will append
                            process_message_data(response, "init");
                            /*
                             if (page_name === 'session') {
                             if (!chat_input.is(":focus")) {
                             flashInterval = addFlashToDom(parent_chat_box);
                             }
                             //this will append
                             process_message_data(response, "init");
                             }
                             */

                        } else {
                            //console.log("No New Message");
                        }
                    },
                    error: function (err) {
                        console.log("Err " + err);

                    }
                });
            }
        }

        //*** LOAD MORE MESSAGE ***//
        btn_load_more.click(function () {
            get_message("load_more");
        });

        /***** Chat Log Controller END ************************************ */


        /***** Chat Input Controller START ************************************ */
        var input_message = "";

        chat_input
                .focus(function () {
                    if (this.value === this.defaultValue) {
                        this.value = '';
                    }
                })
                .blur(function () {
                    if (this.value === '') {
                        this.value = this.defaultValue;
                    }
                })
                .bind('input propertychange', function () {
                    input_message = this.value;

                    if (input_message.length === 0) {
                        btn_send.attr("disabled", true);
                    } else {
                        btn_send.removeAttr("disabled");
                    }
                });

        chat_input.keypress(function (e) {

            //add new line on Ctrl+Enter
            if (e.ctrlKey && this.value !== "") {
                this.value += "\n";
                //scroll to bottom
                chat_input.animate({
                    scrollTop: jQuery(this).get(0).scrollHeight
                }, 0);
            }

            // send on Enter
            if (e.keyCode === 13) {
                if (this.value !== "") {
                    btn_send.trigger("click");
                }
                e.preventDefault();
            }



        });

        //*** SEND MESSAGE ***//

        btn_send.click(function () {
            btn_send.attr("disabled", true);
            if (input_message === "") {
                input_message = chat_input.val();
            }

            addChatLog(input_message, 'self', "append");

            var mes = {};
            mes['to_id'] = other_user_id;
            mes['from_id'] = self_user_id;
            mes['message'] = input_message;

            socketEmit('send_message', mes);

            storeMessageToDB();
            finishSendMessage();

            function storeMessageToDB() {
                jQuery.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'wzs21_chat_send_message',
                        self_user_id: self_user_id,
                        other_user_id: other_user_id,
                        message: input_message
                    },
                    type: 'POST',
                    success: function (response) {
                        response = JSON.parse(response);

                        if (response.status === "success") {
                            current_mes_count++;
                        } else if (response.status === "error") {
                            //btn_send.removeAttr("disabled");
                        }

                        console.log(response);

                    },
                    error: function (err) {
                        console.log("Err " + err);

                    }
                });
            }

            function finishSendMessage() {
                chat_input.val("");
                //chat_input.val("Type a message");
                input_message = "";
            }
        });
        /***** Chat Input Controller END ************************************ */


        /***********************************************************************************/
        /********** JS FUNCTION ************************************************************/
        function addFlashToDom(dom) {
            return setInterval(function () {
                dom.toggleClass('flash_border');
                dom.toggleClass('no_flash_border');
            }, 1000);
        }

        function removeFlashDom(dom, flashInterval) {
            if (flashInterval !== null) {
                clearInterval(flashInterval);
                dom.removeClass('flash_border');
                dom.addClass('no_flash_border');
            }
        }

        function addChatLog(message, whose, type) {
            var same_prev_class = "";
            if (type === "append") {
                if (prev_message === whose) {
                    same_prev_class = "same_prev";
                }
            } else if (type === "prepend") {
                var first = chat_log[0].firstChild;
                first = jQuery(first);
                if (prev_message === whose) {
                    first.addClass("same_prev");
                }
            }

            var new_message = "<div class = '" + same_prev_class + " message message_" + whose + "'>";
            new_message += replaceAll(message, "\n", "<br>");
            new_message += "</div>";

            if (type === "append") {
                chat_log.append(new_message);
                //scroll to bottom
                chat_container.animate({
                    scrollTop: chat_log.get(0).scrollHeight}, 0);

            } else if (type === "prepend") {
                chat_log.prepend(new_message);
            }

            prev_message = whose;
        }

    }); // ->> END OF jQuery DOCUMENT READY

</script>
