$(document).ready(function() {
    // Personel listesini yükle
    loadPersonelList();

    // Arama işlemi
    $('#searchButton').click(function() {
        loadPersonelList();
    });

    $('#searchInput').keypress(function(e) {
        if(e.which == 13) {
            loadPersonelList();
        }
    });
});

function loadPersonelList() {
    const searchTerm = $('#searchInput').val();
    
    $.ajax({
        url: 'ajax/get_personel.php',
        type: 'GET',
        data: { search: searchTerm },
        success: function(response) {
            $('#personelTable tbody').html(response);
        },
        error: function() {
            alert('Veriler yüklenirken bir hata oluştu.');
        }
    });
}

// Dinamik arama fonksiyonu
function searchPersonel() {
    const searchInput = document.getElementById('searchInput');
    const searchValue = searchInput.value.toLowerCase();
    const table = document.querySelector('table');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;

        for (let j = 0; j < cells.length; j++) {
            const cell = cells[j];
            if (cell.textContent.toLowerCase().indexOf(searchValue) > -1) {
                found = true;
                break;
            }
        }

        row.style.display = found ? '' : 'none';
    }
}

// Telefon arama fonksiyonu
function callPersonel(telefon) {
    window.location.href = 'tel:' + telefon;
}

// WhatsApp arama fonksiyonu
function whatsappPersonel(telefon) {
    window.open('https://wa.me/' + telefon.replace(/[^0-9]/g, ''));
}

// Sayfa yüklendiğinde arama input'unu dinle
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', searchPersonel);
    }
}); 