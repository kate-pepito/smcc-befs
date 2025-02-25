<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload Progress</title>
    <!-- Vendor CSS Files -->
    <link href="<?= base_url() ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Template Main CSS File -->
    <link href="<?= base_url() ?>/assets/css/style.css" rel="stylesheet">

</head>
<body>
<div class="container mt-4">
    <h2>Directory Files</h2>
    <div class="d-flex justify-content-between">
        <button class="btn btn-primary" title="Click to transfer all to dist" onclick="onTransferAllFiles(event)">
            <i class="ri-folder-transfer-line"></i> Transfer all files to dist
        </button>
        <button class="btn btn-primary" title="Click to upload dist files to ftp server" onclick="onUploadDistFiles(event)">
            <i class="ri-folder-transfer-line"></i> Upload dist files to ftp server
        </button>
        <button class="btn btn-primary" title="Click to retrieve modified infor" onclick="onGetModifiedFiles()">
            <i class="ri-restart"></i> Refresh
        </button>
    </div>
    <table id="fileTable" class="table table-striped">
        <thead>
            <tr>
                <th>Filename</th>
                <th>Extension</th>
                <th>Type</th>
                <th>Size (Bytes)</th>
                <th>Directory Files</th>
                <th>Transfer to Dist</th>
                <th>Modified?</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
</div>

<!-- Vendor JS Files -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    var fetchOnLoad = false;
    var stopAllFetch = false;
    var fetchings = {};
    async function onTransferAction(event, id, enableAfterUpload = false) {
        if (!id) {
            event.preventDefault();
        }
        const btn = !id ? event.target : document.getElementById(id);
        const filename = !id ? btn.id : id;
        if (!Object.keys(fetchings).includes(filename)) {
            fetchings[filename] = true;
        }
        const body = {
            "source": filename,
            "destination": `dist/${filename}`
        }
        btn.dataset.status = "copying";
        return new Promise((resolve) => {
            fetchOnLoad = true;
            if (stopAllFetch) {
                resolve();
            }
            $.post(`ftp_api?command=copy_file`, body)
                .done(jsonData => {
                    if (jsonData.success) {
                        btn.dataset.status = "success"
                    } else if (jsonData.error) {
                        btn.dataset.status = "error"
                    }
                    if (enableAfterUpload) {
                        fetchOnLoad = false;
                        setTimeout(() => {
                            divideGetModifiedFiles({
                                ftp_server: "185.27.134.11",
                                ftp_username: "if0_38161483",
                                ftp_password: "eBpMR2Fj4E48A1h",
                                ftp_directory: "/smcc-befs.infinityfreeapp.com/htdocs/",
                                local_src_directory: "dist/",
                            }, filename, true).catch(console.log)    
                        }, 500)
                    }
                    fetchings[filename] = false;
                    resolve()
                })
                .fail((...errors) => {
                    console.log(...errors)
                    fetchings[filename] = false;
                    resolve();
                })
        })
    }

    async function onTransferAllFiles(event) {
        event.preventDefault();
        const btns = {
            "async": []
        };
        $(`button[data-btn-type=action]`).each(function() {
            btns["async"] = [...btns.async, [null, $(this).attr('id')]];
        });
        await Promise.all(btns.async.map(async (args) => onTransferAction(...args)));
        await onGetModifiedFiles();

        fetchOnLoad = false;
    }

    async function divideGetModifiedFiles(body, ftp_files, enableAfterUpload = false) {
        return new Promise((resolve) => {
            fetchOnLoad = true;
            if (stopAllFetch) {
                resolve();
            }
            body['ftp_files'] = typeof(ftp_files) === "string" ? ftp_files : ftp_files.join(",");
            if (!body['ftp_files']) return;
            body['ftp_files'].split(",").forEach((filename) => {
                if (!Object.keys(fetchings).includes(filename)) {
                    fetchings[filename] = true;
                }
            })
            $.post('ftp_api?command=check_ftp_modified', body)
                .done(modified_files => {
                    if (modified_files.success) {
                        let f = modified_files.data;
                        f.forEach((file) => {
                            let filename = file.file;

                            let is_modified = file.is_modified ? `<button class="btn btn-primary" id="upload_${filename}" data-upload-name="${filename}" onclick="onUploadAction(event, true)">Upload FTP</button>` : 'No';
                            let modid = `modified_${filename}`;
                            window.localStorage.removeItem(modid);
                            window.localStorage.setItem(modid, is_modified);
                            fetchings[filename] = false;
                        })
                    }
                    if (enableAfterUpload) {
                        fetchOnLoad = false;
                    }
                    resolve();
                })
                .fail((...errors) => {
                    console.log(errors);
                    ftp_files.split(",").forEach((f) => {
                        let modid = `modified_${f}`;
                        window.localStorage.removeItem(modid);
                        window.localStorage.setItem(modid, `<button class="btn btn-primary" id="upload_${f}" data-upload-name="${f}" onclick="onUploadAction(event, true)">Upload FTP</button>`);
                        fetchings[f] = false;
                    })
                    resolve();
                });
        });
        
    }

    async function onUploadAction(filenames, enableAfterUpload = false) {
        filenames = Array.isArray(filenames) ? filenames.join(',') : typeof(filenames) === "string" ? filenames : `${filenames.target.dataset.uploadName}`;
        if (!filenames) {
            return {error: 'No data'};
        }
        const body = {
            ftp_server: "185.27.134.11",
            ftp_username: "if0_38161483",
            ftp_password: "eBpMR2Fj4E48A1h",
            ftp_directory: "/smcc-befs.infinityfreeapp.com/htdocs/",
            local_src_directory: "dist/",
            files: filenames
        };
        return new Promise((resolve) => {
            fetchOnLoad = true;
            if (stopAllFetch) {
                resolve();
            }
            $.post(`ftp_api?command=upload`, body)
                .done(async jsonData => {
                    await divideGetModifiedFiles(body, filenames, filenames.split(",").length === 1).catch(console.log);
                    if (enableAfterUpload) {
                        fetchOnLoad = false;
                    }
                    resolve(jsonData);
                })
                .fail(async (...errors) => {
                    await divideGetModifiedFiles(body, filenames, filenames.split(",").length === 1).catch(console.log);
                    if (enableAfterUpload) {
                        fetchOnLoad = false;
                    }
                    resolve(errors);
                })
        })
    }

    async function onGetModifiedFiles() {
        const btns = {
            "items": []
        };
        $(`button[data-btn-type=action]`).each(function(i) {
            btns["items"] = [...btns.items, $(this).attr('id')];
            let span = document.getElementById(`modified_${$(this).attr('id')}`);
            span.innerText = "Loading...";
        });
        const body = {
            ftp_server: "185.27.134.11",
            ftp_username: "if0_38161483",
            ftp_password: "eBpMR2Fj4E48A1h",
            ftp_directory: "/smcc-befs.infinityfreeapp.com/htdocs/",
            local_src_directory: "dist/",
        };
        let limit = 30;
        let divided = btns.items.length / limit;
        for (let i = 0; i < divided; i++) {
            let start = i * limit;
            let end = Math.min((i+1) * limit, btns.items.length);
            let files = btns.items.slice(start, end);
            await divideGetModifiedFiles(body, files);
            if (stopAllFetch) {
                return;
            }
        }
        fetchOnLoad = false;
    }

    async function onUploadDistFiles(event) {
        event.preventDefault();
        const btns = {
            "items": []
        };
        let x = 0;
        let batch_current = 0;
        $(`div[data-btn-type=modified] button`).each(function() {
            let filename = $(this).attr('data-upload-name');
            if (!!filename) {
                if (batch_current <= btns.items.length) {
                    btns.items.push([]);
                }
                btns.items[batch_current].push(filename);
                if (x % 8 === 7) {
                    batch_current++;
                }
                x++;
            }
        });
        for (let i = 0; i < btns.items.length; i++) {
            if (stopAllFetch) {
                return
            }
            await Promise.all(btns.items[i].map((f) => onUploadAction(f)));
            if (stopAllFetch) {
                return;
            }
        }
        fetchOnLoad = false;
    }

    $(document).ready(function () {
        var dataActionListeners = {};
        var prevDataActionData = {};

        function setDataActionListener(id, dataname, callback) {
            dataActionListeners[id] = [dataname, callback];
        }

        function listenerLoop(d) {
            $('button[data-btn-type=action]').each(function () {
                const id = $(this).attr('id');
                if (Object.keys(dataActionListeners).includes(id)) {
                    const [dataname, callback] = dataActionListeners[id];
                    if (!Object.keys(prevDataActionData).includes(id)) {
                        prevDataActionData[id] = $(this).attr(`data-${dataname}`);
                        if (typeof(callback) === "function") {
                            callback($(this), $(this).get(0), id);
                        }
                    } else {
                        if (prevDataActionData[id] !== $(this).attr(`data-${dataname}`)) {
                            prevDataActionData[id] = $(this).attr(`data-${dataname}`);  
                            if (typeof(callback) === "function") {
                                callback($(this), $(this).get(0), id);
                            }
                        }
                    }
                    
                }

                if (!fetchings[id]) {
                    let modid = `modified_${id}`;
                    let span = document.getElementById(modid);
                    let is_modified = localStorage.getItem(modid);
                    if (span && !!is_modified && is_modified !== span.innerHTML) {
                        if (("" + span.innerHTML).toString().substring(0, 2) !== is_modified.substring(0, 2)) {
                            span.innerHTML = is_modified;
                        }
                        let b = document.getElementById(`upload_${id}`);
                        if (fetchOnLoad && !!b) {
                            b.disabled = true;
                        }
                    }
                }
            });
            if (!$('button').prop('disabled') && fetchOnLoad) {
                $('button').prop('disabled', true);
            } else if ($('button').prop('disabled') && !fetchOnLoad) {
                $('button').prop('disabled', false);
            }
        }
        

        function getActionButton(id, upload_status) {
           setDataActionListener(id, 'status', function($btn, btn, id) {
                let status = $btn.attr('data-status');
                if (status === "pending") {
                    if ($btn.hasClass('btn-danger')) {
                        $btn.removeClass('btn-danger');
                    }
                    if (!$btn.hasClass('btn-primary')) {
                        $btn.addClass('btn-primary');
                    }
                    $btn.prop('disabled', false);
                    $btn.text('Transfer');
                } else if (status === 'copying') {
                    $btn.prop('disabled', true);
                    $btn.text('Transfering to dist...');
                } else if (status === 'error') {
                    if ($btn.hasClass('btn-primary')) {
                        $btn.removeClass('btn-primary');
                    }
                    if (!$btn.hasClass('btn-danger')) {
                        $btn.addClass('btn-danger');
                    }
                    $btn.prop('disabled', false);
                    $btn.text('Transfering Failed');
                } else {
                    if ($btn.hasClass('btn-danger')) {
                        $btn.removeClass('btn-danger');
                    }
                    if ($btn.hasClass('btn-primary')) {
                        $btn.removeClass('btn-primary');
                    }
                    if (!$btn.hasClass('btn-success')) {
                        $btn.addClass('btn-success');
                    }
                    $btn.prop('disabled', false);
                    $btn.text('Transfered');
                }
           });
           return `<button id="${id}" data-btn-type="action" data-status="${upload_status}" class="btn btn-primary btn-sm" onclick="onTransferAction(event, null, true)">Transfer</button>`;
        }
        
        function displayDatafilesToTableData($table, data = {}, basedir = "") {
            data.forEach(item => {
                if (item.type === "file") {
                    $table.row.add([
                        `${basedir}${item.filename}`,
                        item.file_extension || "-",
                        item.type,
                        item.size || "-",
                        "File",
                        getActionButton(`${basedir}${item.filename}`, item.transfer_status || "pending"),
                        `<div id="modified_${basedir}${item.filename}" data-btn-type="modified">Loading...</div>`
                    ]).draw();
                } else if (item.type === "directory") {
                    $table.row.add([
                        `${basedir}${item.filename}/`,
                        item.file_extension || "-",
                        item.type,
                        item.size || "-",
                        item.files.length,
                        "-",
                        "-"
                    ]).draw();
                    displayDatafilesToTableData($table, item.files, `${basedir}${item.filename}/`)
                }
            });
        }
        
        function displayDatafilesToTable(jsonData = {}, distData = {}) {
            let $table = $("#fileTable").DataTable({
                "bPaginate": false,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": false
            });
            if (jsonData.success && distData.success) {
                function getFinalData(src, dist) {
                    let srcTmp = {...src};
                    let distTmp = {...dist};
                    return {
                        ...srcTmp,
                        files: srcTmp.files.map((jd) => {
                            if (jd.type === "directory") {
                                let dd = distTmp.files?.find((dd) => dd.filename === jd.filename && dd.type === jd.type);
                                return getFinalData(jd, dd ? dd : {});
                            }
                            return {
                                ...jd,
                                transfer_status: !!distTmp.files && distTmp.files.some((dd) => dd.filename === jd.filename && dd.type === jd.type && dd.size === jd.size && dd.file_extension === jd.file_extension) ? "success" : "pending"
                            }
                        })
                    }
                }
                const finalData = jsonData.data.map((jd) => {
                    if (jd.type === "directory") {
                        let dd = distData.data?.find((dd) => dd.filename === jd.filename && dd.type === jd.type);
                        return getFinalData(jd, dd ? dd : {});
                    }
                    return {
                        ...jd,
                        transfer_status: !!distData.data && distData.data.some((dd) => dd.filename === jd.filename && dd.type === jd.type && dd.size === jd.size && dd.file_extension === jd.file_extension) ? "success" : "pending"
                    }
                })
                displayDatafilesToTableData($table, finalData);
                
            }
        }
    
        const excluded_files = [
            'dist', '.DS_Store', '.git', '.gitignore', 'node_modules', 'package.json', 'ftp_api.php', 
            'ftp.php', 'yarn.lock', 'LICENSE', 'README.md', '.vscode', '.env', '.env.production', '_passwords.txt',
            'uploads', 'pwat.exe', 'watch.bat',
        ];

        const excluded_str = excluded_files.join(',');

        let urlparam = new URLSearchParams({
            "command": "get_directory_files",
            "path": ".",
            "excluded": excluded_str,
        });
        let urlparamdist = new URLSearchParams({
            "command": "get_directory_files",
            "path": "dist",
            "excluded": excluded_str,
        });

        $.get(`ftp_api?${urlparam.toString()}`)
            .done(jsonData => new Promise((resolve) => {
                $.get(`ftp_api?${urlparamdist.toString()}`)
                    .done(distData => resolve({ jsonData, distData }))
                    .fail(console.log)
            }).then(({ jsonData, distData }) => {
                displayDatafilesToTable(jsonData, distData)
            }))
            .fail(console.log);


        setInterval(listenerLoop, 100);
    });
    $(window).on('beforeunload', function() {
        // Your code here
        stopAllFetch = true;
    });
</script>
</body>
</html>