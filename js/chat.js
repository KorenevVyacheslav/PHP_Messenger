var escapeHtml = function (unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
};

var jsDateFromUnixTimestamp = function (timestamp) {
    return new Date(timestamp * 1000);
};

var jsDateFromMySqlDateTime = function (mySqlDateTime) {
    var t = mySqlDateTime.split(/[- :]/);
    return new Date(Date.UTC(t[0], t[1] - 1, t[2], t[3], t[4], t[5]));
};

var formatDate = function (date) {
    return date.toLocaleString();
};

var formatUnixTimestamp = function (timestamp) {
    return formatDate(jsDateFromUnixTimestamp(timestamp));
};

var formatMySqlDateTime = function (mySqlDateTime) {
    return formatDate(jsDateFromMySqlDateTime(mySqlDateTime));
};

var addBrs = function (str) {
    return str.replace(/(?:\r\n|\r|\n)/g, '<br>');
};

var $chatList = $("#chat_list"),
    $chat = $("#chat_area"),
    $inputRow = $("#input_row"),
    $nav = $("#nav"),
    $sendButton = $("#send_button"),
    $textarea = $("#textarea"),
    $loader = $("#loader"),
    $chatForm = $("#chat_form");

var scrollToBottom = function () {
    $chatList.scrollTop($chatList.get(0).scrollHeight); // прокручиваем сообщения вниз
};


$(document).ready(function () {
    var refreshHeight = function () {
        $chatList.height(document.documentElement.clientHeight - $nav.height() - $inputRow.height());
    };
    $(window).resize(refreshHeight);
    refreshHeight();

    setInterval(function () {
        $inputRow.height($("#textarea_field").height());
        $chatList.height(document.documentElement.clientHeight - $nav.height() - $inputRow.height());
        $("#button_container").css("margin-top", ($inputRow.height() - 60) + "px");
    }, 250);

    $textarea.keydown(function (e) {
        if (e.keyCode === 13 && e.ctrlKey) {
            $(this).val(function (i, val) {
                return val + "\n";
            });
        }
    }).keypress(function (e) {
        if (e.keyCode === 13 && !e.ctrlKey) {
            $chatForm.submit();
            e.preventDefault();
        }
    });
});