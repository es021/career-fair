<?php ?>

<script>

    jQuery(document).ready(function ($) {

        ///////////////////////////////////////////////////////////////
        /// DOM OBJECT ////////////////////////////////////////////////
        var chat_log = jQuery('.wzs21_chat_ui_chatbox #chat_log');
        var chat_input = jQuery('.wzs21_chat_ui_chatbox textarea#chat_input');
        chat_input.append("Type a message");
        var ori_chat_input = chat_input;

        var error_log = jQuery('.wzs21_chat_ui_chatbox #error_log');
        var btn_send = jQuery('.wzs21_chat_ui_chatbox #btn_send');
        var btn_load_more = jQuery('.wzs21_chat_ui_chatbox #btn_load_more');

        var self_user_id = <?php echo $current_user->ID; ?>;
        var other_user_id = <?php echo $other_user_id; ?>;

        var chat_ready = false;
        var current_mes_count = 0;

        /***** Chat Log Controller START ************************************ */


        //get_message("init");

        function get_message(type) {

            var data = {};
            data.action = 'wzs21_chat_get_message';
            data.self_user_id = self_user_id;
            data.other_user_id = other_user_id;
            data.type = type;

            console.log(data);

            jQuery.ajax({
                url: ajaxurl,
                data: data,
                type: 'POST',
                success: function (response) {
                    response = JSON.parse(response);

                    process_message_data(response);

                },
                error: function (err) {
                    console.log("Err " + err);

                }
            });
        }

        function process_message_data(response) {

            if (response.status == "success") {

                for (var m in response.data) {
                    var mes = response.data[m];
                    appendChatLog(mes['message'], mes['type']);
                }

                chat_ready = true;
                current_mes_count = response.mes_count;
                console.log(current_mes_count);

            } else if (response.status == "error") {
                console.log("Error : " + response.data);
            }

        }

        //var check_new_message_interval = setInterval(check_new_message,2000);
        function check_new_message() {
            //console.log("1");
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
                        if (response.has_new) {

                            console.log("New Message Received");
                            console.log(response);
                            process_message_data(response);

                        } else {
                            console.log("No New Message");
                        }
                    },
                    error: function (err) {
                        console.log("Err " + err);

                    }
                });
            }
        }

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

        // todo
        btn_load_more.click(function () {

        });

        btn_send.click(function () {
            appendChatLog(input_message, 'self');

            btn_send.attr("disabled", true);

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
                    //console.log("Suc "+ response);

                    response = JSON.parse(response);

                    if (response.status == "success") {
                        chat_input.value = chat_input.defaultValue;
                        chat_input.val("Type a message");
                        input_message = "";
                        current_mes_count++;
                    } else if (response.status == "error") {
                        btn_send.removeAttr("disabled");
                    }

                    console.log(response);

                },
                error: function (err) {
                    console.log("Err " + err);

                }
            });

        });
        /***** Chat Input Controller END ************************************ */


        /***********************************************************************************/
        /********** JS FUNCTION ************************************************************/

        function appendChatLog(message, whose) {

            var new_message = "<div class = 'message_" + whose + "'>";
            new_message += message;
            new_message += "</div>";
            chat_log.append(new_message);

            chat_log.animate({
                scrollTop: chat_log.get(0).scrollHeight}, 0);

            // chat log has overflow, show load more button
            if (chat_log.get(0).offsetHeight < chat_log.get(0).scrollHeight) {
                btn_load_more.removeAttr("hidden");
            }

        }

    }) // ->> END OF jQuery DOCUMENT READY


</script>

<div class="wzs21_chat_ui_chatbox">
    <div id="error_log"></div>

    <div id="chat_log">
        <div class="text-center" id="btn_load_more" hidden="true"><small><button class="btn btn-link">Load more message</button></small></div>
    </div> 

    <div id="chat_input">
        <textarea id="chat_input" rows="2"></textarea>
        <button id="btn_send" type="button" class="btn btn-primary" disabled="true">Send</button> 
    </div> <!-- END OF CHAT INPUT -->

</div>
