const Copy = {
    async install(Vue, options) {
        function fallbackCopyTextToClipboard(text) {
            let textArea = document.createElement("textarea");
            textArea.value = text;

            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";

            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                document.execCommand('copy');
            } catch (err) {
                console.error('Could not copy text: ', err);
            }

            document.body.removeChild(textArea);
        }
        function copyTextToClipboard(text) {
            if (!navigator.clipboard) {
                fallbackCopyTextToClipboard(text);
                return;
            }
            navigator.clipboard.writeText(text).then(function() {}, function(err) {
                console.error('Could not copy text: ', err);
            });
        }

        Vue.prototype.copyToClipboard = function(text) {
            copyTextToClipboard(text);
        };
    },
}

export default Copy;
