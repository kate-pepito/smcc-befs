<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload Progress</title>
    <!-- Vendor CSS Files -->
    <link href="<?= $BASE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="<?= $BASE_URL ?>/assets/css/style.css" rel="stylesheet">

    <style>
        .progress { height: 25px; }
        .progress-bar { font-weight: bold; }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>FTP Upload</h1>
    <button id="start-upload" class="btn btn-primary">Start Upload</button>
    <div id="status" class="mt-3"></div>
    <div class="progress mt-3">
        <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;">0%</div>
    </div>
    <code id="e-message" class="p-4 m-4"></code>
</div>
<!-- Vendor JS Files -->
<script src="<?= $BASE_URL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
    function displayMessageUploaded(data) {
        const eMessage = document.getElementById('e-message');
        eMessage.innerHTML = "";
        if (Array.isArray(data)) {
            data.forEach(({ success, error, skipped, reason }) => {
                const li = document.createElement('li');
                if (error) {
                    li.textContent = `Error: ${error}`;
                } else if (skipped) {
                    li.textContent = `Skipped: ${skipped} - ${reason}`;
                } else if (success) {
                    li.textContent = `Success: ${success}`;
                }
                eMessage.appendChild(li);
            });
        } else {
            eMessage.textContent = data;
        }
        eMessage.style.display = "block";
    }
    document.getElementById('start-upload').addEventListener('click', function() {
        const progressBar = document.getElementById('progress-bar');
        const status = document.getElementById('status');
        this.disabled = true;
        const searchURL = new URLSearchParams({ftp_server: "185.27.134.11", ftp_username: "if0_38161483", ftp_password: "eBpMR2Fj4E48A1h", ftp_directory: "/smcc-befs.infinityfreeapp.com/htdocs", ftp_src: "dist"})
        const eventSource = new EventSource(`ftp_sse?${searchURL.toString()}`);
        eventSource.onmessage = function(event) {
            let data = event.data.split("|");
            console.log("received data:", data);
            let seq = Number.parseInt(data[0]);
            if (seq === 45 || seq === 55 || seq === 65 || seq === 100) {
                let eMsg = {};
                eventSource.close();
                status.innerHTML = seq < 100 ? "Upload Failed." : "Upload completed.";
                eMsg = seq === 100 ? JSON.parse(data[1]) : data[1];
                progressBar.style.width = data[0] + '%';
                progressBar.innerHTML = data[0] + '%';
                document.getElementById('start-upload').disabled = false;
                displayMessageUploaded(eMsg);
            } else {
                status.innerHTML = data[1];
                progressBar.style.width = data[0] + '%';
                progressBar.innerHTML = data[0] + '%';
                displayMessageUploaded(data[1]);
            }
        };
        eventSource.onopen = function() {
            console.log("Connected...");
        }
        eventSource.onerror = function(event) {
            eventSource.close()
        }

    });
</script>
</body>
</html>