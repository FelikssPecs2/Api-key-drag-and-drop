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
    let columnCount = 3; // Noklusējuma kolonnu skaits
    initializeColumns(columnCount);

    // Kad mainās kolonnu skaits
    $('#columnCount').change(function () {
        columnCount = $(this).val();
        initializeColumns(columnCount);
    });

    // Ielādēt API datus
    $('#loadData').click(function () {
        let apiUrl = $('#apiUrl').val();
        if (!apiUrl) {
            alert('Lūdzu ievadi API URL!');
            return;
        }

        $.post('/fetch', { url: apiUrl }, function (data) {
            $('#column-0').empty(); // Notīram sākotnējo kolonnu
            data.forEach(item => {
                $('#column-0').append(`<li class="item" data-id="${item.id}">${item.name}</li>`);
            });
            initializeDragAndDrop();
        }).fail(function () {
            alert('Neizdevās ielādēt datus!');
        });
    });

    // Funkcija kolonnu sagatavošanai
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

    // Drag and Drop funkcionalitāte
    function initializeDragAndDrop() {
        $(".list").sortable({
            connectWith: ".list",
            placeholder: "ui-state-highlight"
        }).disableSelection();
    }

    // Meklēšana kolonnās
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