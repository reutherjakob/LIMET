<!DOCTYPE html>
<html>
<head>
    <title>Save Cookie to Desktop</title>
</head>
<body>
<button id="saveCookie">Save Cookie</button>

<script>
    document.getElementById('saveCookie').addEventListener('click', function() {
        // Create a cookie
        document.cookie = "username=Hahahah; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";

        // Get the cookie value
        let cookieValue = document.cookie;

        // Create a blob with the cookie data
        let blob = new Blob([cookieValue], { type: 'text/plain' });

        // Create a link element
        let link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'cookie.txt';

        // Append the link to the body
        document.body.appendChild(link);

        // Programmatically click the link to trigger the download
        link.click();

        // Remove the link from the document
        document.body.removeChild(link);
    });
</script>
</body>
</html>