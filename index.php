<?php
// Backend Code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    $apiUrl = $_POST['url'];
    
    
    $data = fetchDataFromApi($apiUrl);
    
    if (!is_array($data)) {
        $data = [$data];
    }

    echo json_encode($data);
    exit;
}

function fetchDataFromApi($url) {
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $response = file_get_contents($url);
        
        if ($response === FALSE) {
            return ['error' => 'Failed to fetch data from API.'];
        }

        $data = json_decode($response, true);
        error_log("Fetched API Data: " . print_r($data, true)); // Log data for debugging

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Failed to decode JSON response from the API.'];
        }

        return $data;
    } else {
        return ['error' => 'Invalid API URL.'];
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Drag & Drop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .container { max-width: 900px; margin: auto; }
        .input, .button, .search-input { margin: 10px; padding: 5px; }
        .columns { display: flex; gap: 10px; justify-content: center; }
        .column { flex: 1; background: #f4f4f4; padding: 10px; min-height: 300px; border-radius: 5px; }
        .list { min-height: 100px; background: white; padding: 5px; border: 1px solid #ddd; border-radius: 5px; }
        .item { padding: 8px; background: #ddd; margin-bottom: 5px; cursor: grab; border-radius: 3px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Laravel Drag & Drop Sistēma</h2>

    <!-- API URL Input -->
    <input type="text" id="apiUrl" placeholder="Ievadi API URL" class="input"/>
    <button id="loadData" class="button">Ielādēt</button>

    <!-- Kolonnu skaits -->
    <label for="columnCount">Kolonnu skaits:</label>
    <select id="columnCount">
        <option value="1">1 kolonna</option>
        <option value="2">2 kolonnas</option>
        <option value="3" selected>3 kolonnas</option>
        <option value="4">4 kolonnas</option>
        <option value="5">5 kolonnas</option>
    </select>

    <div class="columns" id="columns">
        <!-- Pirmā kolonna ar API datiem -->
        <div class="column">
            <input type="text" class="search-input" placeholder="Meklēt..." onkeyup="searchItems(this, 0)">
            <ul id="column-0" class="list"></ul>
        </div>
        <!-- Papildus kolonnas -->
    </div>
</div>

<script>
$(document).ready(function () {
    let columnCount = 1; //Sakuma kolonas
    initializeColumns(columnCount);

    $('#columnCount').change(function () {
        columnCount = $(this).val();
        initializeColumns(columnCount);
    });

    $('#loadData').click(function () {
        let apiUrl = $('#apiUrl').val();
        if (!apiUrl) {
            alert('Lūdzu ievadi API URL!');
            return;
        }

        console.log('Sending request to API URL:', apiUrl); // Debugging

        $.post('index.php', { url: apiUrl }, function (response) {
            console.log('Received raw response from server:', response);

            try {
                let data = JSON.parse(response);
                console.log('Parsed JSON data:', data);

                if (data.error) {
                    alert(data.error);
                    return;
                }

                if (!Array.isArray(data)) {
                    console.error('Invalid data format: Expected an array, got', typeof data, data);
                    alert('Invalid data format received!');
                    return;
                }

                $('#column-0').empty();
                data.forEach(item => {
                    if (item.id && item.name) {
                        $('#column-0').append(`<li class="item" data-id="${item.id}">${item.name}</li>`);
                    } else {
                        console.warn('Skipping item due to missing "id" or "name" field:', item);
                    }
                });

                initializeDragAndDrop();
            } catch (e) {
                console.error('Error parsing JSON response:', e);
                alert('Invalid JSON response from the server.');
            }
        }).fail(function () {
            alert('Neizdevās ielādēt datus!');
        });
    });

    function initializeColumns(count) {
        $('#columns').empty();
        for (let i = 0; i < count; i++) {
            let columnHtml = `
                <div class="column">
                    <input type="text" class="search-input" placeholder="Meklēt..." onkeyup="searchItems(this, ${i})">
                    <ul id="column-${i}" class="list"></ul>
                </div>`;
            $('#columns').append(columnHtml);
        }
        initializeDragAndDrop();
    }

    function initializeDragAndDrop() {
        $(".list").sortable({
            connectWith: ".list",
            placeholder: "ui-state-highlight"
        }).disableSelection();
    }

    window.searchItems = function(input, columnIndex) {
        let searchText = $(input).val().toLowerCase();
        $(`#column-${columnIndex} .item`).each(function() {
            let text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(searchText));
        });
    };
});
</script>

</body>
</html> 